<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if (isset($_GET['id'])) {
    $apiUrl = $_GET['id'];

    $response = file_get_contents($apiUrl);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao buscar os dados da API."]);
    } else {
        echo $response;
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Parâmetro 'id' não fornecido."]);
}
?>
