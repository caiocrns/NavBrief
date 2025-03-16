<?php

include_once('../lib/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $rwy = $_POST['rwy'];
    $temp = $_POST['temp'];
    $mtow = $_POST['mtow'];
    $mlw = $_POST['mlw'];

    $sql = "UPDATE rwy_analise97 SET rwy='$rwy', temp='$temp', mtow='$mtow', mlw='$mlw' WHERE id='$id'";

    if (mysqli_query($conexao, $sql)) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . mysqli_error($conexao);
    }
}

mysqli_close($conexao);
?>
