<!-- STAND BY -->


<?php
include_once('lib/conn.php');

$fueleditado = $_POST['fueleditado']; //LB 
$idvoo = $_POST['idvoo'];
$dispresult = $_POST['dispresult'];
$autonomiafuelKG = $_POST['autonomiafuelKG']; //KG

$autonomiafuelKG_editado = $fueleditado*0.45; //KG
$dispresult_editado = ($dispresult - ($autonomiafuelKG_editado - $autonomiafuelKG));

// Usamos a declaração UPDATE para atualizar o valor do campo "fuel" na tabela "voos" para o valor fornecido
$addfuel = mysqli_query($conexao, "UPDATE voos SET fuel = '$fueleditado' WHERE id = '$idvoo'") or die(mysqli_error($conexao));

$mensagemfuel = "<span style='color: green;font-size:11px'> Aeronave abastecida </span> <br>";

$response_array = array('fueleditado' => $fueleditado, 'autonomiafuelKG_editado' => $autonomiafuelKG_editado, 'mensagemfuel' => $mensagemfuel,'dispresult_editado' => $dispresult_editado);

header('Content-Type: application/json');
echo json_encode($response_array);

?>