
<?php
include '../lib/function.php';
header('Content-Type: text/plain');
if (isset($_GET['location'])) {
    echo getnotam($_GET['location']);
}
?>
