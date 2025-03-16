<?php

// Inclusão de dependências
include 'lib/conn.php';
include 'lib/config.php';
include 'lib/db_function.php';
include 'lib/function.php';

$serverURL = "https://api.autorouter.aero/v1.0";

// Autenticação na API
$token = authenticate();
if ($token === null) {
    header("Location: home.php?error=Falha+na+autenticacao");
    exit();
}

// Parâmetros de entrada (exemplo de uso)
$origem = 'SBGL'; // Rio de Janeiro - Galeão
$destino = 'SBBR'; // Brasília
$departureTime = time(); // Hora atual como exemplo
$totaleet = 7200; // Tempo estimado em segundos (2 horas)
$altitude = 10000; // Altitude em pés
$formatoDesejado = 'pdf'; // Formato do GRAMET

// Busca coordenadas dos aeroportos
$airports = getAirports($conexao, $origem, $destino);
$waypoints = sprintf(
    "%s,%s %s,%s",
    $airports[$origem]['lat'],
    $airports[$origem]['long'],
    $airports[$destino]['lat'],
    $airports[$destino]['long']
);

// Obtenção do GRAMET
get_gramet($departureTime, $totaleet, $altitude, $formatoDesejado, $token);

/**
 * Função para autenticar e obter o token.
 */
function authenticate() {
    global $serverURL;
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cl, CURLOPT_URL, "$serverURL/oauth2/token");
    curl_setopt($cl, CURLOPT_POST, true);
    curl_setopt($cl, CURLOPT_POSTFIELDS, [
        "grant_type" => "client_credentials",
        "client_id" => "caiorn.santos@gmail.com",
        "client_secret" => "123456",
    ]);
    $response = curl_exec($cl);
    curl_close($cl);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

/**
 * Função para obter o GRAMET.
 */
function get_gramet($departuretime, $totaleet, $altitude, $formatoDesejado, $token) {
    global $serverURL;

    // Validação básica
    if (empty($departuretime) || empty($totaleet) || empty($altitude)) {
        header("Location: home.php?error=Parametros+invalidos");
        exit();
    }

    // Construa a URL da API
    $url = "$serverURL/met/gramet?waypoints=SBGL SBBR&departuretime=$departuretime&totaleet=$totaleet&altitude=$altitude&distanceperpage=999&format=$formato";

    $cl = curl_init();
    curl_setopt($cl, CURLOPT_URL, $url);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Accept: application/$formato"
    ]);

    $response = curl_exec($cl);
    $httpCode = curl_getinfo($cl, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($cl, CURLINFO_CONTENT_TYPE);

    // Verifique o código HTTP e redirecione para home.php em caso de erro
    if (curl_errno($cl) || $httpCode !== 200 || strpos($contentType, $formato) === false) {
        $errorMessage = urlencode("Erro HTTP $httpCode ao obter o GRAMET.");
        header("Location: home.php?error=$errorMessage");
        exit();
    }

    // Valida se o conteúdo é realmente um PDF (no caso de formato PDF)
    if ($formato === 'pdf' && strpos($response, '%PDF') !== 0) {
        $errorMessage = urlencode("Erro: O arquivo gerado não é um PDF válido.");
        header("Location: home.php?error=$errorMessage");
        exit();
    }

    // Envia o arquivo ao cliente para download
    header("Content-Type: application/$formato");
    header("Content-Disposition: attachment; filename=GRAMET_" . urlencode($rota) . ".$formato");
    echo $response;

    curl_close($cl);
}

/**
 * Função para obter os dados dos aeroportos.
 */
function getAirports($conexao, $origem, $destino) {
    // Sanitiza as entradas para evitar SQL Injection
    $origem = mysqli_real_escape_string($conexao, $origem);
    $destino = mysqli_real_escape_string($conexao, $destino);

    // Consulta para buscar os aeroportos de origem e destino
    $sql = "
        SELECT ident, latitude_deg, longitude_deg 
        FROM airports 
        WHERE ident IN ('$origem', '$destino')
    ";
    $result = mysqli_query($conexao, $sql);

    if (!$result) {
        // Erro na consulta
        $error = urlencode("Erro ao buscar dados dos aeroportos: " . mysqli_error($conexao));
        header("Location: home.php?error=$error");
        exit();
    }

    $airports = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $airports[strtoupper($row['ident'])] = [
            'lat' => $row['latitude_deg'],
            'long' => $row['longitude_deg'],
        ];
    }

    // Validação de resultados
    if (!isset($airports[strtoupper($origem)]) || !isset($airports[strtoupper($destino)])) {
        $missing = [];
        if (!isset($airports[strtoupper($origem)])) $missing[] = $origem;
        if (!isset($airports[strtoupper($destino)])) $missing[] = $destino;
        $error = urlencode("Aeroportos ausentes: " . implode(", ", $missing));
        header("Location: home.php?error=$error");
        exit();
    }

    return $airports;
}

?>
