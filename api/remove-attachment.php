<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/lib/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST')app_json(['ok'=>false],405);
app_require_csrf();$body=json_decode(file_get_contents('php://input')?:'{}',true)?:[];
$chatId=(string)($body['chat_id']??'');$attachmentId=(string)($body['attachment_id']??'');
$chat=app_chat_load($chatId);if(!$chat)app_json(['ok'=>false,'error'=>['message'=>'Conversation not found.']],404);
$found=false;$kept=[];
foreach(($chat['attachments']??[]) as $a){
    if(($a['id']??'')===$attachmentId){$found=true;if(is_file($a['path']??''))@unlink($a['path']);}
    else $kept[]=$a;
}
if(!$found)app_json(['ok'=>false,'error'=>['message'=>'Attachment not found.']],404);
$chat['attachments']=$kept;$chat['updated_at']=date('c');app_chat_save($chat);app_json(['ok'=>true]);
