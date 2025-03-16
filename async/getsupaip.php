<?php
include '../lib/function.php';
header('Content-Type: text/plain');
if (isset($_GET['location'])) {
    echo get_supaip($_GET['location']);
}
?>
