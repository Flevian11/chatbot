<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/lib/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST')app_json(['ok'=>false],405);
app_require_csrf();$chat=app_new_chat();app_chat_save($chat);app_json(['ok'=>true,'chat'=>['id'=>$chat['id'],'title'=>$chat['title'],'mode'=>$chat['mode']]]);
