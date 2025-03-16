<?php

include_once('../lib/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $icao = $_POST['icao'];
    $rwy = $_POST['rwy'];
    $temp = $_POST['temp'];
    $mtow = $_POST['mtow'];
    $mlw = $_POST['mlw'];

    $sql = "INSERT INTO rwy_analise97 (icao, rwy, temp, mtow, mlw) VALUES ('$icao', '$rwy', '$temp', '$mtow', '$mlw')";

    if (mysqli_query($conexao, $sql)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conexao);
    }
}

mysqli_close($conexao);
?>
