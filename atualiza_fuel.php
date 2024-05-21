<?php
session_start();
include('lib/conn.php'); // Certifique-se de que este arquivo define a conexão $conexao

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fueleditado = $_POST['fueleditado'];
    $idvoo = $_POST['idvoo'];

    // Atualiza o combustível no banco de dados
    $addfuel = mysqli_query($conexao, "UPDATE voos SET fuel = '$fueleditado' WHERE id = '$idvoo'") or die(mysqli_error($conexao));

    // Verifica se a atualização foi bem-sucedida
    if ($addfuel) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o combustível']);
    }
    exit();
} else {
    // Se a solicitação não for POST, enviar uma resposta de erro
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit();
}
