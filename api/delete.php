<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/lib/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST')app_json(['ok'=>false],405);
app_require_csrf();$body=json_decode(file_get_contents('php://input')?:'{}',true)?:[];$id=(string)($body['id']??'');
$chat=app_chat_load($id);if(!$chat)app_json(['ok'=>false,'error'=>['message'=>'Conversation not found.']],404);
@unlink(app_chat_path($id));
$dir=APP_UPLOAD_DIR.'/'.app_owner_id().'/'.$id;
if(is_dir($dir)){foreach(glob($dir.'/*')?:[] as $f)@unlink($f);@rmdir($dir);}
app_json(['ok'=>true]);
