<?php
include '../lib/function.php';
header('Content-Type: text/plain');
if (isset($_GET['location'])) {
    echo getrotaer($_GET['location']);
}
?>
