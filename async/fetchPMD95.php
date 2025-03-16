<?php

include '../lib/function.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $inputValue = $_POST['inputValue'] ?? null;
    $altitude = $_POST['altitude'] ?? null;
    $modelo = $_POST['modelo'] ?? null;

    if ($inputValue === null || $altitude === null) {
        echo json_encode(['success' => false, 'message' => 'Faltam dados para o cálculo.']);
        exit;
    }

    // Exemplo de cálculo com altitude e valor
    $result = calculatePMD95($inputValue, $altitude,$modelo);

    echo json_encode(['success' => true, 'result' => $result]);
    exit;
}


 ?>