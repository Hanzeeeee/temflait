<?php
require_once __DIR__ . '/config/app.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('index.php');
}
verify_csrf();
$_SESSION = [];
session_regenerate_id(true);
session_destroy();
redirect_to('login.php');
