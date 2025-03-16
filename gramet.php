<?php

$rota = urlencode($_GET['rota']);
$dep_time = $_GET['dep_time'];
$niveldevoo = $_GET['fl'];
$tempodevoo_seg = $_GET['eet'];


$serverURL = "https://api.autorouter.aero/v1.0";

// Obter o token de autenticação
$token = auth();
if ($token === null) {
    exit("Authentication failed.");
}

// Função para autenticar e obter o token
function auth()
{
    global $serverURL;
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cl, CURLOPT_URL, "$serverURL/oauth2/token");
    curl_setopt($cl, CURLOPT_POST, true);
    // curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false); // Descomente em caso de problemas com SSL
    curl_setopt($cl, CURLOPT_POSTFIELDS, array(
        "grant_type" => "client_credentials",
        "client_id" => "caiorn.santos@gmail.com",
        "client_secret" => "123456"
    ));
    $auth_response = curl_exec($cl);
    if ($auth_response === false) {
        echo "Failed to authenticate\n";
        var_dump(curl_getinfo($cl));
        curl_close($cl);
        return null;
    }
    curl_close($cl);
    return json_decode($auth_response, true);
}

// Função para obter o GRAMET no formato escolhido (PNG ou PDF)
function get_gramet($rota,$departuretime,$totaleet,$altitude,$formato, $token)
{
    global $serverURL;

    // Verificar se o formato é válido
    if (!in_array($formato, ['png', 'pdf'])) {
        exit("Invalid format. Please choose 'png' or 'pdf'.");
    }

    // URL do GRAMET
    $url = "$serverURL/met/gramet?waypoints=$rota&departuretime=$departuretime&totaleet=$totaleet&altitude=$altitude&distanceperpage=999&format=$formato";

    $cl = curl_init();
    curl_setopt($cl, CURLOPT_URL, $url);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cl, CURLOPT_HEADER, false); // Ignorar cabeçalhos na resposta
    curl_setopt($cl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $token['access_token'],
        "Accept: application/$formato"
    ]);

    $response = curl_exec($cl);
    $httpCode = curl_getinfo($cl, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($cl, CURLINFO_CONTENT_TYPE);

    if (curl_errno($cl)) {
        echo "CURL Error: " . curl_error($cl);
        curl_close($cl);
        return;
    }

    if ($httpCode === 200 && strpos($contentType, $formato) !== false) {
        // Configurar cabeçalhos para download no formato escolhido
        header("Content-Type: application/$formato");
        header("Content-Disposition: attachment; filename=GRAMET_$rota.$formato");
        echo $response;
    } else {
        echo "API Error: HTTP Code $httpCode, Content-Type: $contentType";
        echo "Response: " . htmlentities($response);
    }

    curl_close($cl);
}

// Escolha o formato desejado: 'png' ou 'pdf'
$formatoDesejado = 'pdf'; // Altere para 'pdf' se necessário
get_gramet($rota,$dep_time,$tempodevoo_seg,$niveldevoo,$formatoDesejado, $token)

?>
