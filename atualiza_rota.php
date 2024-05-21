<?php


    include_once('lib/conn.php');

    $rota = strtoupper($_POST['rota']);
    $idvoo = $_POST['idvoo'];
    
    $addrota = mysqli_query($conexao, "UPDATE voos SET rota='$rota' WHERE id='$idvoo'") or die(mysqli_error($conexao));


echo json_encode("<span style='color: green;font-size:11px'>Rota atualizada</span> <br>");
        