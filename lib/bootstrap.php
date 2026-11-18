<?php
declare(strict_types=1);
define('APP_ROOT', dirname(__DIR__));
define('APP_STORAGE', APP_ROOT . '/storage');
define('APP_LOG', APP_STORAGE . '/logs/app.log');
define('APP_CHAT_DIR', APP_STORAGE . '/chats');
define('APP_UPLOAD_DIR', APP_STORAGE . '/uploads');

function app_load_env(): void {
    $path = APP_ROOT . '/.env';
    if (!is_file($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key,$value] = explode('=',$line,2); $key=trim($key); $value=trim(trim($value),"\"'");
        if ($key !== '' && getenv($key) === false) putenv($key.'='.$value);
    }
}
app_load_env();
function app_env(string $key,string $default=''): string { $v=getenv($key); return $v===false?$default:trim((string)$v); }
function app_public_config(): array {
    return ['default_mode'=>app_env('DEFAULT_MODE','lms')==='general'?'general':'lms',
        'max_input_chars'=>max(300,min(12000,(int)app_env('MAX_INPUT_CHARS','6000'))),
        'max_file_bytes'=>5*1024*1024,
        'knowledge_files'=>count(glob(APP_ROOT . '/knowledge/*.md') ?: [])];
}
function app_start_session(): void {
    if(session_status()===PHP_SESSION_ACTIVE)return;
    @ini_set('session.gc_maxlifetime','2592000');
    $https=!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off';
    session_name('lwf_ai_session');
    session_set_cookie_params(['lifetime'=>2592000,'httponly'=>true,'secure'=>$https,'samesite'=>'Lax','path'=>'/']);
    session_start();
}
function app_csrf_token(): string {
    app_start_session(); if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32)); return (string)$_SESSION['csrf'];
}
function app_json(array $data,int $status=200): never {
    http_response_code($status);header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store, private');
    echo json_encode($data,JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE);exit;
}
function app_require_csrf(): void {
    app_start_session();$token=(string)($_SERVER['HTTP_X_CSRF_TOKEN']??'');
    if($token===''||!hash_equals((string)($_SESSION['csrf']??''),$token))app_json(['ok'=>false,'error'=>['code'=>'csrf_invalid','message'=>'Your session security token expired. Refresh the page and try again.']],419);
}
function app_log(string $event,array $context=[]): void {
    if(!is_dir(dirname(APP_LOG)))@mkdir(dirname(APP_LOG),0750,true);
    @file_put_contents(APP_LOG,json_encode(['time'=>date('c'),'event'=>$event,'context'=>$context],JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE).PHP_EOL,FILE_APPEND|LOCK_EX);
}
function app_rate_limit(): void {
    $max=max(1,min(120,(int)app_env('RATE_LIMIT_PER_HOUR','45')));
    $dir=APP_STORAGE.'/rate';if(!is_dir($dir))@mkdir($dir,0750,true);
    $id=hash('sha256',session_id().'|'.($_SERVER['REMOTE_ADDR']??'unknown'));$file=$dir.'/'.$id.'.json';
    $fp=fopen($file,'c+');if(!$fp)return;flock($fp,LOCK_EX);
    $state=json_decode(stream_get_contents($fp)?:'{}',true)?:['start'=>time(),'count'=>0];
    if(time()-(int)$state['start']>=3600)$state=['start'=>time(),'count'=>0];$state['count']++;
    ftruncate($fp,0);rewind($fp);fwrite($fp,json_encode($state));fflush($fp);flock($fp,LOCK_UN);fclose($fp);
    if($state['count']>$max)app_json(['ok'=>false,'error'=>['code'=>'rate_limited','message'=>'Request limit reached. Please wait before trying again.']],429);
}
function app_clean_text(string $text,int $max): string {
    $text=trim(str_replace(["\0","\r"],'',$text));return mb_strlen($text)>$max?mb_substr($text,0,$max):$text;
}
function app_owner_id(): string { app_start_session();return hash('sha256',session_id()); }
function app_valid_chat_id(string $id): bool { return (bool)preg_match('/^[a-f0-9]{32}$/',$id); }
function app_chat_path(string $id): string { return APP_CHAT_DIR.'/'.app_owner_id().'_'.$id.'.json'; }
function app_chat_load(string $id): ?array {
    if(!app_valid_chat_id($id))return null;$path=app_chat_path($id);if(!is_file($path))return null;
    $data=json_decode((string)file_get_contents($path),true);return is_array($data)?$data:null;
}
function app_chat_save(array $chat): void {
    if(!is_dir(APP_CHAT_DIR))@mkdir(APP_CHAT_DIR,0750,true);
    $path=app_chat_path($chat['id']);$tmp=$path.'.'.bin2hex(random_bytes(4)).'.tmp';
    file_put_contents($tmp,json_encode($chat,JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE),LOCK_EX);
    @chmod($tmp,0600);rename($tmp,$path);
}
function app_new_chat(): array {
    return ['id'=>bin2hex(random_bytes(16)),'title'=>'New conversation','mode'=>'lms','messages'=>[],
        'attachments'=>[],'created_at'=>date('c'),'updated_at'=>date('c')];
}
function app_title_from_message(string $message): string {
    $message=preg_replace('/\s+/u',' ',trim($message))??trim($message);
    $message=preg_replace('/^(please|can you|could you|would you|help me|i want to|i need to)\s+/iu','',$message)??$message;
    $words=preg_split('/\s+/u',$message)?:[];$title=implode(' ',array_slice($words,0,9));
    if(mb_strlen($title)>68)$title=mb_substr($title,0,65).'…';
    return $title!==''?mb_strtoupper(mb_substr($title,0,1)).mb_substr($title,1):'New conversation';
}
function app_openrouter_models(): array {
    $raw=app_env('OPENROUTER_MODELS','google/gemini-3-flash-preview,moonshotai/kimi-k2.5,meta/muse-ember');
    $models=array_values(array_unique(array_filter(array_map('trim',explode(',',$raw)))));
    return array_slice($models,0,3);
}
function app_openrouter_key(): string {
    $key=app_env('OPENROUTER_API_KEY');
    if($key===''||$key==='paste_your_openrouter_key_here')app_json(['ok'=>false,'error'=>['code'=>'not_configured','message'=>'OpenRouter API key is not configured in the server .env file.']],500);
    return $key;
}
