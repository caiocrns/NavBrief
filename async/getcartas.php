<?php
include '../lib/function.php';
header('Content-Type: text/plain');
if (isset($_GET['location'], $_GET['type'])) {
    $icaoarpt = strtoupper($_GET['location']);
    $type = strtoupper($_GET['type']);

    // Função que gera as cartas com base no ICAO e tipo
    getcartas($icaoarpt, $type);
}
?>
