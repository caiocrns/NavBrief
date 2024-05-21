<?php
 include 'lib/conn.php';  


// Coordenadas da origem e destino do voo
$origin_latitude = -8.126385; // Latitude da origem (SBRF)
$origin_longitude = -34.923777; // Longitude da origem (SBRF)
$destination_latitude = -5.911417; // Latitude do destino (SBNT)
$destination_longitude = -35.247803; // Longitude do destino (SBNT)

// Consulta para obter os waypoints entre a origem e o destino do banco de dados
$sql = "SELECT * FROM waypoint_aisweb WHERE tipo = 'ICAO'";
$result = $conexao->query($sql);

// Array para armazenar os waypoints
$waypoints = array();

// Processamento dos waypoints
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $waypoints[] = array(
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'ident' => $row['ident']
        );
    }
}

// Função para calcular a distância entre dois pontos
function calculateDistance($lat1, $long1, $lat2, $long2) {
    $r = 3441.0350972;

    $lat = $lat2 - $lat1;
    $long = $long2 - $long1;

    $dlat = deg2rad($lat);
    $dlong = deg2rad($long);

    $a = sin($dlat/2) *sin($dlat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlong/2) *sin($dlong/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $nm = $r * $c;
    $nm2 = ceil($nm);
    return $nm2;
}

// Função para encontrar o waypoint mais próximo de uma linha reta entre a origem e o destino
function findWaypointsAlongStraightLine($waypoints, $latitudeOrigem, $longitudeOrigem, $latitudeDestino, $longitudeDestino) {
    $waypointsFiltrados = [];

    foreach ($waypoints as $waypoint) {
        $latitude = $waypoint['latitude'];
        $longitude = $waypoint['longitude'];

        $distanciaOrigem = calculateDistance($latitudeOrigem, $longitudeOrigem, $latitude, $longitude);
        $distanciaDestino = calculateDistance($latitudeDestino, $longitudeDestino, $latitude, $longitude);
        $distanciaReta = calculateDistance($latitudeOrigem, $longitudeOrigem, $latitudeDestino, $longitudeDestino);

        // Verificar se o waypoint está próximo o suficiente da linha reta entre origem e destino
        if ($distanciaOrigem + $distanciaDestino <= $distanciaReta) {
            $waypointsFiltrados[] = $waypoint;
        }
    }

    return $waypointsFiltrados;
}

// Encontrar os waypoints próximos à linha reta entre origem e destino
$waypointsFiltrados = findWaypointsAlongStraightLine($waypoints, $origin_latitude, $origin_longitude, $destination_latitude, $destination_longitude);

// Imprimir a rota de voo
echo "Rota de voo:<br>";
foreach ($waypointsFiltrados as $waypoint) {
    echo $waypoint['ident'] . ": Latitude: " . $waypoint['latitude'] . ", Longitude: " . $waypoint['longitude'] . "<br>";
}










?>
<?php


if(isset($_POST['fueleditado'])){
  $autonomiafuelLB = $_POST["fueleditado"];
  $autonomiafuelKG = ceil($autonomiafuelLB * 0.45);
  $autonomiamin = $autonomiafuelLB / $consumo_por_min; //$timeABmin + $timeBCmin  + time_extra;
  $disptkoff = (int)$mtow - (int)$pbo - (int)$cargaprev - $autonomiafuelKG;
  $displand = (int)$mlw - (int)$pbo - (int)$cargaprev - $autonomiafuelKG + $fuelAB * 0.45;
  $dispresult = min(array($disptkoff, $displand));
  $tow = (int)$pbo + (int)$cargaprev + $autonomiafuelKG;
  $tw = (int)$tow + 30;
  $lw = (int)$tow - ceil($fuelAB * 0.45);
  $zfw = (int)$pbo + (int)$cargaprev;

  // Constrói o array com os dados da tabela
  $tabela = array(
    'max' => array(
      'dec' => $mtow,
      'ramp' => $mtw,
      'pouso' => $mlw,
      'zeroComb' => $mzfw
    ),
    'prev' => array(
      'dec' => $tow,
      'ramp' => $tw,
      'pouso' => $lw,
      'zeroComb' => $zfw
    ),
    'payload' => array(
      'pesoBasico' => $pesobasico,
      'pesoTrip' => (empty($pesotrip) ? "Não informado" : $pesotrip),
      'eqp' => $eqp,
      'pbo' => $pbo,
      'fuel' => $autonomiafuelKG,
      'payload' => (empty($cargaprev) ? "Não informado" : $cargaprev),
      'disp' => $dispresult
    ),
    'limitacao' => ($dispresult == $disptkoff) ? 'Limitado MTOW' : 'Limitado MLW'
  );

  // Retorna a resposta como JSON
  echo json_encode($tabela);
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tabela de Dados</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
        $('#atualizafuel').submit(function(e) {
          e.preventDefault(); // Impede o comportamento padrão do formulário
          var novoNome = $('#fueleditado').val();

          $.ajax({
            url: '',
            method: 'POST',
            data: { fueleditado: novoNome },
            dataType: 'json',
            success: function(response) {
              $('#tabela_antiga').hide();
              $('#tabela_nova').html(`
                <table class="table responsive-table text-center">
                  <thead>
                    <tr>
                      <th scope="col" class="text-center"> Peso (Kg)</th>
                      <th scope="col">Decolagem</th>
                      <th scope="col">Rampa</th>
                      <th scope="col">Pouso</th>
                      <th scope="col">Zero Comb</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row" class="text-center">MÁX</th>
                      <td class="table-active">${response.max.dec}</td>
                      <td class="table-active">${response.max.ramp}</td>
                      <td class="table-active">${response.max.pouso}</td>
                      <td class="table-active">${response.max.zeroComb}</td>
                    </tr>
                    <tr>
                      <th scope="row" class="text-center">PREV</th>
                      <td class="table-info">${response.prev.dec}</td>
                      <td class="table-info">${response.prev.ramp}</td>
                      <td class="table-info">${response.prev.pouso}</td>
                      <td class="table-info">${response.prev.zeroComb}</td>
                    </tr>
                  </tbody>
                </table>

                <table class="table responsive-table text-center">
                  <thead>
                    <tr>
                      <th scope="col text-center">(Kg) Peso Básico</th>
                      <th scope="col">Peso Trip</th>
                      <th scope="col">EQP</th>
                      <th scope="col">PBO</th>
                      <th scope="col">FUEL (Kg)</th>
                      <th scope="col">PAYLOAD</th>
                      <th scope="col">DISP</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="table-info text-center">${response.payload.pesoBasico}</td>
                      <td class="table-info">${response.payload.pesoTrip}</td>
                      <td class="table-info">${response.payload.eqp}</td>
                      <td class="table-info">${response.payload.pbo}</td>
                      <td class="table-info">${response.payload.fuel}</td>
                      <td class="table-info">${response.payload.payload}</td>
                      <td class="${response.payload.disp > 0 ? 'table-success' : 'table-danger'}"><strong>${response.payload.disp}</strong></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td colspan="3">${response.limitacao}</td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              `);
            },
            error: function(xhr, status, error) {
              console.log(error);
            }
          });
        });
      });
    </script>
</head>
<body>
    <!-- Formulário para atualizar o fuel -->
    <form method="POST" id="atualizafuel">
        <label for="fueleditado">Novo Fuel:</label>
        <input type="text" name="fueleditado" id="fueleditado">
        <input type="submit" value="Atualizar Fuel">
    </form>

    <!-- Tabela antiga -->
    <div id="tabela_antiga">
       TESTE 
    </div>

    <!-- Tabela nova -->
    <div id="tabela_nova"></div>
</body>
</html>

