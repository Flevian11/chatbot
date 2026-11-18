<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/lib/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='GET')app_json(['ok'=>false],405);
$id=(string)($_GET['id']??'');$chat=app_chat_load($id);
if(!$chat)app_json(['ok'=>false,'error'=>['message'=>'Conversation not found.']],404);
$messages=array_map(fn($m)=>['role'=>$m['role'],'content'=>$m['content'],'sources'=>$m['sources']??[],'attachments'=>$m['attachments']??[],'model'=>$m['model']??null],$chat['messages']??[]);
app_json(['ok'=>true,'chat'=>['id'=>$chat['id'],'title'=>$chat['title'],'mode'=>$chat['mode'],'messages'=>$messages,
'attachments'=>array_map(fn($a)=>['id'=>$a['id'],'name'=>$a['name'],'kind'=>$a['kind'],'size'=>$a['size']],$chat['attachments']??[])]]);
