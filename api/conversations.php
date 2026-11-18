<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/lib/bootstrap.php';
app_start_session();
$owner=app_owner_id();$items=[];
foreach(glob(APP_CHAT_DIR.'/'.$owner.'_*.json')?:[] as $file){
    $c=json_decode((string)file_get_contents($file),true);if(!is_array($c))continue;
    $items[]=['id'=>$c['id'],'title'=>$c['title']??'Conversation','mode'=>$c['mode']??'lms','updated_at'=>$c['updated_at']??''];
}
usort($items,fn($a,$b)=>strcmp($b['updated_at'],$a['updated_at']));
app_json(['ok'=>true,'conversations'=>array_slice($items,0,80)]);
