<?php 

// Inclusão de dependências
include 'lib/conn.php';
include 'lib/config.php';
include 'lib/function.php';


// Sanitização e validação de entrada
$origem = isset($_GET['origem']) ? strtoupper($_GET['origem']) : null;
$destino = isset($_GET['destino']) ? strtoupper($_GET['destino']) : null;
$datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : null;
$velocidade = isset($_GET['velocidade']) ? $_GET['velocidade'] : null;
$niveldevoo = isset($_GET['niveldevoo']) ? $_GET['niveldevoo'] * 100 : null;


// Conversão da hora de decolagem
$dep_time = $datetime ? strtotime($datetime) : null;

// Obtenção dos dados dos aeroportos
$airports = getAirports($conexao, $origem, $destino);

// Coordenadas
$lat1 = $airports[$origem]['lat'];
$long1 = $airports[$origem]['long'];
$lat2 = $airports[$destino]['lat'];
$long2 = $airports[$destino]['long'];

// Formatação das coordenadas
$coord_origem = formatCoordinates($lat1, $long1);
$coord_dest = formatCoordinates($lat2, $long2);

// Cálculo da rota, distância e tempo de voo
$rota = $coord_origem . '+' . $coord_dest;
$distanceAB = get_distance($lat1, $long1, $lat2, $long2);
$tempodevoo_seg = ceil(get_flighttime($velocidade, $distanceAB) + time_proc) * 60;

$serverURL = "https://api.autorouter.aero/v1.0";

// Autenticação na API
$token = auth();
if ($token === null) {
    exit("Falha na autenticação.");
}

// Escolha do formato do GRAMET
$formatoDesejado = 'pdf'; // Alterar para 'png' se necessário


// Função para autenticar e obter o token
function auth()
{
    global $serverURL;
    global $email_autorouter;
    global $password_autorouter;
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cl, CURLOPT_URL, "$serverURL/oauth2/token");
    curl_setopt($cl, CURLOPT_POST, true);
    // curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false); // Descomente em caso de problemas com SSL
    curl_setopt($cl, CURLOPT_POSTFIELDS, array(
        "grant_type" => "client_credentials",
        "client_id" => "{$email_autorouter}",
        "client_secret" => "{$password_autorouter}"
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
        // Erro na consulta, redireciona para home.php com mensagem de erro
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
    // Verifica se encontrou tanto a origem quanto o destino

    $missing = [];
    if (!isset($airports[strtoupper($origem)])) {
        $missing[] = $origem;
    }
    if (!isset($airports[strtoupper($destino)])) {
        $missing[] = $destino;
    }
    if (!empty($missing)) {
        // Cria uma mensagem com os aeroportos ausentes
        $error = urlencode("Não foi possível encontrar os seguintes aeroportos no banco de dados: " . implode(", ", $missing));
        header("Location: home.php?error=$error");
        exit();
    }
    return $airports;
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
get_gramet($rota, $dep_time, $tempodevoo_seg, $niveldevoo, $formatoDesejado, $token)

?>
