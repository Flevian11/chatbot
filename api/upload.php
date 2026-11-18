<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/lib/bootstrap.php';
require_once dirname(__DIR__).'/lib/attachments.php';
if($_SERVER['REQUEST_METHOD']!=='POST')app_json(['ok'=>false],405);
app_require_csrf();
$chatId=(string)($_POST['chat_id']??'');$chat=app_chat_load($chatId);
if(!$chat)app_json(['ok'=>false,'error'=>['message'=>'Conversation not found.']],404);
if(empty($_FILES['files']))app_json(['ok'=>false,'error'=>['message'=>'Choose at least one file.']],422);
$files=$_FILES['files'];$count=is_array($files['name'])?count($files['name']):1;
if($count>5)app_json(['ok'=>false,'error'=>['message'=>'Attach up to five files at a time.']],422);
$existingCount=count($chat['attachments']??[]);
if($existingCount+$count>10)app_json(['ok'=>false,'error'=>['message'=>'A conversation can retain up to 10 attachments. Remove or start a new conversation.']],413);
$existingBytes=array_sum(array_map(fn($a)=>(int)($a['size']??0),$chat['attachments']??[]));
$owner=app_owner_id();$dir=APP_UPLOAD_DIR.'/'.$owner.'/'.$chatId;
if(!is_dir($dir)&&!mkdir($dir,0750,true))app_json(['ok'=>false,'error'=>['message'=>'Could not create private attachment storage.']],500);
$added=[];
for($i=0;$i<$count;$i++){
    $error=is_array($files['error'])?$files['error'][$i]:$files['error'];
    if($error!==UPLOAD_ERR_OK)app_json(['ok'=>false,'error'=>['message'=>'One of the files could not be uploaded.']],422);
    $size=(int)(is_array($files['size'])?$files['size'][$i]:$files['size']);
    if($size<1||$size>5*1024*1024||$existingBytes+$size>25*1024*1024)app_json(['ok'=>false,'error'=>['message'=>'Each attachment must be no larger than 5 MB.']],413);
    $tmp=is_array($files['tmp_name'])?$files['tmp_name'][$i]:$files['tmp_name'];
    $original=is_array($files['name'])?$files['name'][$i]:$files['name'];
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp)?:'application/octet-stream';
    $kind=app_attachment_kind($mime,(string)$original);
    if($kind==='unsupported')app_json(['ok'=>false,'error'=>['message'=>'Supported files: PDF, PNG, JPG, WEBP, TXT, Markdown, CSV, JSON, XML and LOG.']],415);
    $id=bin2hex(random_bytes(12));$safe=app_safe_filename((string)$original);$target=$dir.'/'.$id.'_'.$safe;
    if(!move_uploaded_file($tmp,$target))app_json(['ok'=>false,'error'=>['message'=>'Could not save the uploaded file.']],500);
    @chmod($target,0600);
    $item=['id'=>$id,'name'=>$safe,'mime'=>$mime,'kind'=>$kind,'size'=>$size,'path'=>$target,'text'=>app_attachment_text($target,$mime,$safe),'created_at'=>date('c')];
    $chat['attachments'][]=$item;$added[]=['id'=>$id,'name'=>$safe,'kind'=>$kind,'size'=>$size];
}
$chat['updated_at']=date('c');app_chat_save($chat);
app_json(['ok'=>true,'attachments'=>$added]);
