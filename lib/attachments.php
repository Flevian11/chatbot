<?php
declare(strict_types=1);

function app_attachment_text(string $path,string $mime,string $name): ?string {
    $ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
    if(in_array($ext,['txt','md','csv','json','xml','log'],true)) {
        $text=file_get_contents($path);
        if($text===false)return null;
        return mb_substr($text,0,50000);
    }
    return null;
}
function app_attachment_kind(string $mime,string $name): string {
    $ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
    if($mime==='application/pdf'||$ext==='pdf')return 'pdf';
    if(str_starts_with($mime,'image/')&&in_array($ext,['png','jpg','jpeg','webp'],true))return 'image';
    if(in_array($ext,['txt','md','csv','json','xml','log'],true))return 'text';
    return 'unsupported';
}
function app_safe_filename(string $name): string {
    $name=basename(str_replace('\\','/',$name));
    $name=preg_replace('/[^A-Za-z0-9._-]+/','_',$name)??'attachment';
    return substr($name,0,120) ?: 'attachment';
}
