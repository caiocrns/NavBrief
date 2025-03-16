<?php
include '../lib/function.php';

if (isset($_GET['fetch_satellite'])) {
    $tipo_satelite = $_GET['fetch_satellite'];
    header('Content-Type: application/json');

    // Chama a função getsatelite
    $response = getsatelite($tipo_satelite);
    // Decodifica a resposta da função
    $data = json_decode($response, true);
    // Verifica se houve erro na resposta
    if (isset($data['error'])) {
        echo json_encode(['error' => $data['error']]);
    } else {
        // Retorna todos os dados fornecidos pela função
        echo $response;
    }
    exit;
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetro inválido.']);
    exit;
}

?>
