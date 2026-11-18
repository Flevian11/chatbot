<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/lib/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') app_json(['ok'=>false],405);
app_require_csrf();
app_start_session();
$_SESSION['chat_history'] = [];
app_json(['ok'=>true]);
