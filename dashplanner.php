<head>


  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
  <link href="assets/css/style.css" rel="stylesheet">
  
  </head>
  <?php
   // MENSAGENS DASHPLANNER //
      $mensagem = "";
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          if (isset($_POST['editafuel'])) {
              $mensagem = "Estamos abastecendo sua aeronave...";
          } elseif (isset($_POST['editatemp'])) {
              $mensagem = "Calculando novos pesos de operação de acordo com a temperatura...";
          }
          elseif (isset($_POST['editacarga'])) {
            $mensagem = "Estamos alterando a carga!";
        }
      } else {
          $mensagem = "Mantenha o ponto de espera<br>Estamos planejando o seu voo...<br>";
      }
      ?>
      
  <!-- Mensagem carregamento -->
<div id="overlay">
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <span class="loading-text"><?php echo $mensagem; ?></span>
    </div>
    <!-- Mensagem carregamento -->




<?php include 'lib/function.php' ?>
<?php include 'lib/conn.php'   ?>
<?php include 'lib/config.php'   ?>
<?php include 'lib/db_function.php'   ?>


<?php 

$idvoo = $_GET['idvoo'];
$origem = strtoupper($_GET['origem']);  
$destino = strtoupper($_GET['destino']);
$alternativo = strtoupper($_GET['alternativo']);
$datadovoo = $_GET['datadovoo'];
$id_aeronave = $_GET['aeronave'];
$horadep = $_GET['horadep'];
$cargaprev = $_GET['cargaprev'];

// CAPTURA O ULTIMO ID VOO NO BANCO DE DADOS //
$sqlid = "SELECT MAX(id) AS last_id FROM voos";
$result = mysqli_query($conexao, $sqlid);

if ($result && mysqli_num_rows($result) > 0) {
    $row_id = mysqli_fetch_assoc($result);
    $last_id = $row_id['last_id'];
} else {
    $last_id = null; // Ou qualquer valor padrão apropriado
}
// CAPTURA O ULTIMO ID VOO NO BANCO DE DADOS //

// SELECIONA INFO AERONAVE //
if (isset($id_aeronave) && is_numeric($id_aeronave)) {  
  $sql = "SELECT * FROM aeronaves WHERE id = ?";
  $stmt = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($stmt, 'i', $id_aeronave);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);  
  if ($lista_aeronaves = mysqli_fetch_assoc($result)) {
      $mtow = $lista_aeronaves['mtow'];
      $mzfw = $lista_aeronaves['mzfw'];
      $mfuelKG = $lista_aeronaves['mfuel'];
      $mlw = $lista_aeronaves['mlw'];
      $mtw = $lista_aeronaves['mtw'];
      $operador = $lista_aeronaves['operador'];
      $matricula = $lista_aeronaves['matricula'];
      $velocidade = $lista_aeronaves['velocidade_media'];
      $pbo = $lista_aeronaves['peso_basico'];
      $aeronave = $lista_aeronaves['icao_aeronave'];
      $nome_aeronave = $lista_aeronaves['nome_aeronave'];
      $modelo = $lista_aeronaves['modelo'];
      $consumo = $lista_aeronaves['consumo'];
      $consumo2 = $lista_aeronaves['consumo2'];
      $unidade = $lista_aeronaves['unidade'];
  }
}
  // SELECIONA AS INFO DAS ANV //

//SELECIONA AS INFO DOS AEROPORTOS E GUARDA EM UM ARRAY //

// Consulta única para buscar os aeroportos de interesse
$sqlairports = "SELECT ident, name, latitude_deg, longitude_deg, elevation_ft, iata_code 
                FROM airports 
                WHERE ident IN (?, ?, ?)";

$queryairports = mysqli_prepare($conexao, $sqlairports);
mysqli_stmt_bind_param($queryairports, "sss", $origem, $destino, $alternativo);
mysqli_stmt_execute($queryairports);
$resultairports = mysqli_stmt_get_result($queryairports);

$airports = array();

while ($db_airports = mysqli_fetch_assoc($resultairports)) {
    $airports[strtoupper($db_airports['ident'])] = array(
        'nome' => $db_airports['name'],
        'lat' => $db_airports['latitude_deg'],
        'long' => $db_airports['longitude_deg'],
        'elevation' => $db_airports['elevation_ft'],
        'iata' => $db_airports['iata_code']
    );
}

// Verifique a existência dos aeroportos
$missingAirports = array();

if (!isset($airports[$origem])) {
    $missingAirports[] = $origem;
}
if (!isset($airports[$destino])) {
    $missingAirports[] = $destino;
}
if (!isset($airports[$alternativo])) {
    $missingAirports[] = $alternativo;
}
// Redirecione se algum aeroporto estiver faltando
if (!empty($missingAirports)) {
    $message = "Os seguintes aeroportos não foram encontrados: " . implode(', ', $missingAirports);
header("Location: planner.php?error=" . urlencode($message) . "&aeronaveselec=" . urlencode($aeronave));
    exit;
}

// Processamento normal caso todos os aeroportos sejam encontrados
if (isset($airports[$origem])) {
    $nomeorigem = $airports[$origem]['nome'];
    $lat1 = $airports[$origem]['lat'];
    $long1 = $airports[$origem]['long'];
    $elevation1 = $airports[$origem]['elevation'];
    $iataorigem = $airports[$origem]['iata'];
}

if (isset($airports[$destino])) {
    $nomedestino = $airports[$destino]['nome'];
    $lat2 = $airports[$destino]['lat'];
    $long2 = $airports[$destino]['long'];
    $elevation2 = $airports[$destino]['elevation'];
    $iatadestino = $airports[$destino]['iata'];
}

if (isset($airports[$alternativo])) {
    $nomealternativo = $airports[$alternativo]['nome'];
    $lat3 = $airports[$alternativo]['lat'];
    $long3 = $airports[$alternativo]['long'];
    $elevation3 = $airports[$alternativo]['elevation'];
    $iataalternativo = $airports[$alternativo]['iata'];
}

//SELECIONA AS INFO DOS AEROPORTOS // 


  // NIVEL DE VOO SUGERIDO SCRIPT STBY //
  $magdec = getmagdec($lat1,$long1);
  $proamag = calcularProaMagnetica($lat1, $long1, $lat2, $long2, $magdec);
  $nivelsugerido = "";
 $nivel_voo_map = [
  "C95M" => 'getniveldevoo_1',
  "T27M" => 'getniveldevoo_1',
  "C98" => 'getniveldevoo_1',
  "C97" => 'getniveldevoo_3',
  "C105" => 'getniveldevoo_2',
  "KC390" => 'getniveldevoo_3',
  "C99" => 'getniveldevoo_3',
  "A29" => 'getniveldevoo_2'
];
if (isset($nivel_voo_map[$aeronave])) {
  $function_name = $nivel_voo_map[$aeronave];
  $nivelsugerido = $function_name($proamag);
} else {
  header("Location: planner.php?error=" . urlencode("Insira um nível de voo válido") . "&aeronaveselec=" . urlencode($aeronave));
}
  $niveldevoo = empty($_GET['niveldevoo']) ? $nivelsugerido : $_GET['niveldevoo'];
//convertendo data pra BR
$datadovoobr = date("d/m/Y",strtotime($_GET['datadovoo']));

$a29= calc_a29($lat1, $long1, $elevation1, $lat2, $long2, $elevation2, $niveldevoo, $lat3, $long3, $niveldevoo); 
if ($aeronave == "A29") {
  $timeABmin = $a29['tempo_total'] + time_proc;
$timeBCmin = $a29['tempo_alternativo'] + time_proc;
  }
//  DISTANCIA E TEMPO DE VOO //
$distanceAB = get_distance($lat1,$long1,$lat2,$long2);
$distanceBC = get_distance($lat2,$long2,$lat3,$long3);
$timeABmin = get_flighttime($velocidade,$distanceAB);
$timeBCmin = get_flighttime($velocidade,$distanceBC);
$consumo_por_min = $consumo/60;
//  COMBUSTÍVEL //
if ($aeronave == "A29") {
$fuelAB =  $a29['comb_total'];
$fuelBC = $a29['comb_alternativo'];
$fuelextra = ($a29['consumo_cruzeiro'])*(time_extra/60); // + 45MIN
}
$fuelAB =  ($aeronave == "C97" || $aeronave == "C105")? calcfuel_2($timeABmin,$consumo,$consumo2):calcfuel($timeABmin,$consumo);
$fuelBC = ($aeronave == "C97" || $aeronave == "C105")? calcfuel($timeBCmin,$consumo2):calcfuel($timeBCmin,$consumo);
$fuelproc = ($aeronave == "C97" || $aeronave == "C105")? calcfuel(time_proc,$consumo2):calcfuel(time_proc,$consumo);
$fuelextra = ($aeronave == "C97" || $aeronave == "C105")? calcfuel(time_extra,$consumo2) :calcfuel(time_extra,$consumo);

// Cálculo da autonomia de combustível
switch ($aeronave) {
  case "T27M":
      $autonomiafuel = autonomiafuel_t27;
      break;
  case "C97":
      $autonomiafuel = ($fuelAB + $fuelBC + $fuelproc + $fuelextra) < 1200 ? 1200 : round(($fuelAB + $fuelBC + $fuelproc + $fuelextra) / 100) * 100;
      break;
  case "C105":
      $autonomiafuel = round(($fuelAB + $fuelBC + $fuelextra + 200) / 100) * 100;
      break;
  case "A29":
      $autonomiafuel = round(($fuelAB + $fuelBC + $fuelextra));
      break;
  default:
      $autonomiafuel = round(($fuelAB + $fuelBC + $fuelproc + $fuelextra) / 100) * 100;
      break;
}

/*  INSERE COMBUSTÍVEL ABASTECIDO NO DB */
if(isset($_POST['fueleditado'])) {
  $fueleditado = $_POST['fueleditado']; //LB 
  $addfuel = mysqli_query($conexao, "UPDATE voos SET fuel = '$fueleditado' WHERE id = '$idvoo'") or die(mysqli_error($conexao));
}
if(isset($_POST['novo_payload'])) {
  $cargaeditada = $_POST['novo_payload']; //LB 
  $addcarga = mysqli_query($conexao, "UPDATE voos SET cargaprev = '$cargaeditada' WHERE id = '$idvoo'") or die(mysqli_error($conexao));
}

if(isset($_POST['temp_newdep'])) {
  $temp_dep = $_POST['temp_newdep'];
  $updatetemp_dep = mysqli_query($conexao, "UPDATE voos SET temp_dep = '$temp_dep' WHERE id = '$idvoo'") or die(mysqli_error($conexao));   
} else if(isset($_POST['temp_newldg'])){
  $temp_ldg = $_POST['temp_newldg']; 
  $updatetemp_ldg = mysqli_query($conexao, "UPDATE voos SET temp_ldg = '$temp_ldg' WHERE id = '$idvoo'") or die(mysqli_error($conexao));  
}

/* BUSCA ROTA E FUEL E TEMPS NO DB */
$selectrota = mysqli_query($conexao, "SELECT rota,fuel,cargaprev,temp_dep,temp_ldg from voos WHERE id='$idvoo'") or die(mysqli_error($conexao));
$db_voos = mysqli_fetch_assoc($selectrota);
/* ATUALIZA CARGA */
$cargaprev = empty($db_voos['cargaprev'])? $cargaprev: $db_voos['cargaprev'];

/* NOVO PMD DE ACORDO COM ALTITUDE E TEMP  C-95M e C97 */
$temp_dep = (isset($db_voos['temp_dep']) && $db_voos['temp_dep'] != 0) ? $db_voos['temp_dep'] : 30;
$temp_ldg = (isset($db_voos['temp_ldg']) && $db_voos['temp_ldg'] != 0) ? $db_voos['temp_ldg'] : 30;

/* TAXI WEIGHT */
$consumo_taxi_map = [
  "C95M" => 30,
  "C97" => 70,
  "C105" => 50,
  "C98" => 15
];
// Verifica se a aeronave está no array e atribui o consumo correspondente
$consumo_taxi = $consumo_taxi_map[$aeronave] ?? 0; // Valor padrão 0 se a aeronave não estiver no array

  /* RWY PERFORMANCE C95 E C97 */
  if ($aeronave == "C95M") {
    // Define o MTOW de acordo com o modelo
    $mtow_max = $lista_aeronaves['mtow'];
    // Calcula o MTOW baseado na temperatura e elevação
    $mtow = calculatePMD95($temp_dep, $elevation1,$modelo);
    $mtow = ($mtow > $mtow_max) ? $mtow_max : $mtow;

    // Calcula o MTW (MTOW + margem de 30, limitado ao máximo permitido)
    $mtw = ($mtow + 30 > $mtow_max + 30) ? $mtow_max + 30 : $mtow + 30;

} else if ($aeronave == "C97") {
  $mtow = getmtow_c97($origem, $temp_dep, $conexao);
  $mlw = getmlw_c97($destino, $temp_ldg, $conexao); 
  if ($mtow === 'N/A' && $mlw === 'N/A') {
    $mensagem_perf_c97 = "* $origem e $destino não constam no banco de dados do C97";
    $mtow = 12000;
    $mlw = 11700;
} else if ($mtow === 'N/A') {
    $mensagem_perf_c97 = "* $origem não consta no banco de dados do C97";
    $mtow = 12000;
} else if ($mlw === 'N/A') {
    $mensagem_perf_c97 = "* $destino não consta no banco de dados do C97";
    $mlw = 11700;
}
    $mtw = $mtow + 70;    
} 

 
 /* EDITAR COMBUSTIVEL */
if(!empty($db_voos['fuel'])) {
$novofuel = $db_voos['fuel'];
$novofuelKG = ceil(convert_lb_kg($novofuel));
$autonomiamin = $novofuel/$consumo_por_min; //$timeABmin + $timeBCmin  + time_extra;
$disptkoff = ($unidade == "kg")? (int)$mtow - (int)$pbo - (int)$cargaprev - $novofuel: (int)$mtow - (int)$pbo - (int)$cargaprev - $novofuelKG;
$displand = ($unidade == "kg")? (int)$mlw - (int)$pbo - (int)$cargaprev - $novofuel + $fuelAB: (int)$mlw - (int)$pbo - (int)$cargaprev - $novofuelKG + convert_lb_kg($fuelAB);
$dispresult = min(array($disptkoff, $displand));
$tw = ($unidade == "kg")? (int)$pbo + (int)$cargaprev + $novofuel: (int)$pbo + (int)$cargaprev + $novofuelKG ;
$tow = (int)$tw - $consumo_taxi;
$lw = (int)$tow - ceil(convert_lb_kg($fuelAB));
$zfw = (int)$pbo + (int)$cargaprev;
} else {
$autonomiamin = $timeABmin + $timeBCmin  + time_extra;
$autonomiafuelKG = ceil(convert_lb_kg($autonomiafuel));
  $disptkoff = ($unidade == "kg")? (int)$mtow - (int)$pbo - (int)$cargaprev - $autonomiafuel: (int)$mtow - (int)$pbo - (int)$cargaprev - $autonomiafuelKG;
  $displand = ($unidade == "kg")? (int)$mlw - (int)$pbo - (int)$cargaprev - $autonomiafuel + $fuelAB: (int)$mlw - (int)$pbo - (int)$cargaprev - $autonomiafuelKG + convert_lb_kg($fuelAB);
  $dispresult = floor(min(array($disptkoff,$displand)));
  $tw = ($unidade == "kg")? (int)$pbo + (int)$cargaprev + $autonomiafuel: (int)$pbo + (int)$cargaprev + $autonomiafuelKG ; 
  $tow = (int)$tw - $consumo_taxi;
  $lw = (int)$tow - ceil(convert_lb_kg($fuelAB));
  $zfw = (int)$pbo + (int)$cargaprev;
}
$fuelValueKG = ($unidade == "kg") 
        ? (!empty($db_voos['fuel']) ? $db_voos['fuel'] : $autonomiafuel) 
        : (!empty($db_voos['fuel']) ? $novofuelKG : $autonomiafuelKG);
   /* ----------------------------------------------------------------*/

  $espaco_aereo = "L";
  if($niveldevoo  > 245){
    $espaco_aereo = "H";
  }  
    $rotasugerida = select_rota($origem,$destino,$espaco_aereo) ;

// minutos para hora 

$timeABhoras = mintohours($timeABmin);
$timeBChoras = mintohours($timeBCmin);
$autonomiahoras = mintohours($autonomiamin);

// GRAMET HOUR 
$coord_origem = formatCoordinates($lat1, $long1);
$coord_dest = formatCoordinates($lat2, $long2);
$hdep_timestamp = strtotime($horadep);
$timeABseg = ceil($timeABmin*60);
$gramet_hour = ceil($timeABseg/3600);
$etaseg = $hdep_timestamp + $timeABseg;
$etahoras = date('H:i',$etaseg);
  
// PEGAR URL DA PAGINA
$protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
$url = '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 

$linkvoo = $protocolo.$url;


if (isset($_GET['submit']) && isset($_GET['idvoo']) && $_GET['idvoo'] > $last_id) {
        include_once('lib/conn.php');      
      $resultaddflight = mysqli_query($conexao,"INSERT INTO `voos`(`id`,`origem`, `destino`, `alternativo`, `rota`, `niveldevoo`, `datadovoo`, `aeronave`,`horadep`,`cargaprev`,`criado`,`linkvoo`) VALUES ( '$idvoo', '$origem','$destino','$alternativo','','$niveldevoo','$datadovoo','$id_aeronave','$horadep','$cargaprev',NOW(),'$linkvoo')") or die(mysqli_error($conexao));

      }  
  if (isset($_GET['submit']) && isset($_GET['idvoo']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
  include_once('lib/conn.php'); 
  $linkvoo = str_replace("&action=edit", "", $linkvoo);     
  $sql = "UPDATE `voos` SET `origem` = '$origem', `destino` = '$destino', `alternativo` = '$alternativo', `niveldevoo` = '$niveldevoo', `datadovoo` = '$datadovoo', `aeronave` = '$id_aeronave', `horadep` = '$horadep', `linkvoo` = '$linkvoo', `criado` = NOW() WHERE `id` = '$idvoo'"; 
  $result_edit= mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

if ($result_edit) {
  echo "
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
              icon: 'success',
              title: 'Voo atualizado com sucesso!',                   
              timer: 3000,
              showConfirmButton: true
          });
      });
  </script>";
} else {
  echo "
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
              icon: 'error',
              title: 'Erro ao atualizar voo!',
              text: 'Tente novamente.',
              timer: 3000,
              showConfirmButton: true
          });
      });
  </script>";
}

 }
         
      $conexao->close();
?>




<?php include 'includes/header.php' ?>
<?php include 'includes/sidebar.php' ?>

  <main id="main" class="main">  
  <Style>
      .rightmenu { display: inline-block; float: right; padding-right: 10px;}
      .leftmenu { display: inline-block; float: left; padding-left: 10px;} 
     
      </style>

  <!-- HEADER MAIN -->
     <div class="pagetitle text-center">
      <h1>Planejamento do voo </h1>
      <nav>
      <div class="rightmenu">
      <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#compartilhar"><i class="fa fa-link" aria-hidden="true" ></i> Compartilhar</button>
      <!--<button onClick="shorturlecopiar()" class="btn btn-outline-success btn-sm"><i class="fa fa-link" aria-hidden="true" ></i> Compartilhar</button>-->
      <?php if (isset($_GET['idvoo'])) { ?>
        <a href="editplanner.php?id=<?php echo $_GET['idvoo']?>" class="btn btn-outline-info btn-sm"><i class="fa fa-edit" target="_blank" aria-hidden="true"></i> Editar voo</a>
      <?php } ?>           
        
      </div>
    
          <ol class="breadcrumb">
           
          <!-- INPUT GERA_PDF --> 
          <li><form  method="post" action="gera_pdf.php" target="_blank">                      
       <?php include 'inputpdf.php'; ?>  
       
         <button type="submit" target="_blank" name="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>
 Briefing PDF</button>                  
                </div></form></li>
                 <!-- END INPUT GERA PDF -->
          <!--<li class="breadcrumb-item active">Planejamento</li>-->
        </ol>         
      </nav>   
      </div>
      <!-- END HEADER MAIN -->
     
<!-- MODAL COMPARTILHAR-->
<div class="modal" id="compartilhar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-link" aria-hidden="true" ></i> Compartilhar Planejamento</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <table>
        <tr>
          <th class="text-muted small">Copie o link e<br> envie no Whatsapp</th>
         
      </tr>
      <tr>
        <td><button id="copyLinkButton" class="btn btn-outline-success btn-sm"><i class="fa fa-link" aria-hidden="true" ></i> Copiar Link</button></td>
         </tr>
      </table>         
      </div>
    </div>
  </div>
</div>
<!-- END MODAL COMPARTILHAR -->

  

    <hr>

   
    <section class="section dashboard">
      <div class="row">     

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">   
<!-- CARDS -->
<?php
$cards = [
    [
        'tipo' => 'Origem',
        'horario' => 'ETD: ' . ($_GET['horadep'] ?? '') . ' Z',
        'img' => 'departures.png',
        'icao' => strtoupper($_GET['origem'] ?? ''),
        'elevation' => $elevation1,
        'nome' => $nomeorigem,
    ],
    [
        'tipo' => 'Destino',
        'horario' => 'ETA: ' . ($etahoras ?? '') . ' Z',
        'img' => 'arrivals.png',
        'icao' => strtoupper($_GET['destino'] ?? ''),
        'elevation' => $elevation2,
        'nome' => $nomedestino,
    ],
    [
        'tipo' => 'Alternativo',
        'horario' => '',
        'img' => 'arrivals.png',
        'icao' => strtoupper($_GET['alternativo'] ?? ''),
        'elevation' => $elevation3,
        'nome' => $nomealternativo,
    ],
];
?>

<?php foreach ($cards as $card): ?>
<div class="col-xxl-4 col-md-6">
    <div class="card info-card">
        <div class="card-body">
            <h5 class="card-title">
                <span><?php echo $card['tipo']; ?> | </span> <?php echo $card['horario']; ?>
            </h5>
            <div class="d-flex align-items-center">
                <div class="ps-3">
                    <h6>
                        <img src="assets/img/<?php echo $card['img']; ?>" style="width:30px;height:30px;">
                        <?php echo $card['icao']; ?>
                        <span style="font-size:13px;">(<?php echo $card['elevation']; ?> ft)</span>
                        <button 
                            type="button" 
                            class="button-carta" 
                            data-bs-toggle="modal" 
                            data-bs-target="#cartas-modal" 
                            data-icao="<?php echo $card['icao']; ?>" 
                            data-nome="<?php echo $card['nome']; ?>">
                            <i class="bi bi-map"></i> Cartas
                        </button>
                    </h6>
                    <span class="text-muted small pt-2 ps-1" style="font-size:13px;">
                        <?php echo $card['nome']; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- MODAL DE CARTAS -->
<div class="modal fade" id="cartas-modal" tabindex="-1" aria-labelledby="cartas-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cartas-modal-label">Cartas Aeronáuticas</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 id="modal-icao" class="mb-3"></h5>
                <div class="accordion" id="accordion-cartas">
                    <?php foreach (['PDC', 'ADC', 'SID', 'IAC', 'STAR','VAC'] as $type): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo strtolower($type); ?>">
                            <button 
                                class="accordion-button collapsed" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse-<?php echo strtolower($type); ?>" 
                                aria-expanded="false" 
                                aria-controls="collapse-<?php echo strtolower($type); ?>" 
                                data-type="<?php echo $type; ?>">
                                <?php echo $type; ?>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo strtolower($type); ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo strtolower($type); ?>" data-bs-parent="#accordion-cartas">
                            <div class="accordion-body" id="content-<?php echo strtolower($type); ?>">
                                <!-- Conteúdo será carregado dinamicamente -->
                                <p>Carregando <?php echo $type; ?>...</p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


          

<!-- Aeronave card-->

<div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">               
                <div class="card-body">           
                  <h5 class="card-title"> Aeronave <br><span><?php echo $nome_aeronave ?></span></h5>                  
                  <div class="d-flex align-items-center">                  
                    <div class="ps-3">
                      <h6 >  <?php echo $matricula ?> </h6>
                      <span class="text-muted small pt-2 ps-1"> 
                        <?php echo $aeronave . "|" . $modelo. "|". $operador  ?>                       
                       </span>                        
                      <!--<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>-->
                    </div>                    
                  </div>                  
                </div>
              </div>
            </div>
           <!-- END AERONAVE CARD-->

            <!-- NIVEL DE VOO CARD-->
<div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">                
                <div class="card-body">
                  <h5 class="card-title"> Nível de voo <span>  <?php  if (empty($_GET['niveldevoo'])) { echo "sugerido";} else {  echo ""; }?> </span></h5>
                  <div class="d-flex align-items-center">                   
                    <div class="ps-3">
                      <h6> FL <?php echo $niveldevoo ?> </h6>
                      <span class="text-muted small pt-2 ps-1"></span>
                      <!--<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>-->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--NIVEL DE VOO CARD -->

            <!-- DATA DO VOO CARD -->
<div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">        
                <div class="card-body">
                  <h5 class="card-title"> Data do voo  <span></span></h5>
                  <div class="d-flex align-items-center">                   
                    <div class="ps-3">
                      <h6> <?php echo "$datadovoobr"; ?> </h6>
                      <span class="text-muted small pt-2 ps-1"></span>
                      <!--<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>-->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- END DATA DO VOO CARD-->

           
            <script>
        function copiarTexto() {
            var texto = '<?php echo $rotasugerida ?>';
            var input = document.getElementById("rotainserida");
            input.value = texto;        }
        </script>

            <!-- ROTA  -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                  <div class="card-body">
                  <h5 class="card-title" style="text-align: center;"><i class="fa fa-map-o" aria-hidden="true" ></i> Planeje sua Rota<span></span></h5>
                   <div class= "text-center">               
                   <!--<iframe height="500px" width="95%" src="https://geoaisweb.decea.mil.br/#"></iframe>                    
                        <hr>-->
                        <table class="table text-center">
                <thead>
                  <tr>
                    <th scope="col" class="text-center">Rota sugerida:<p></p></th>
                    <th scope="col" class="text-muted" ><?php echo $rotasugerida ?><br><?php verifica_rota($rotasugerida) ?></th>
                    <?php  if ($rotasugerida == $rota_nao_encontrada) {
                          echo "<th scope='col'></th>";
                    } else { ?>
                      <th scope="col">
                      <a href="https://skyvector.com/?ll=-15.860957599564097,-49.18029785623944&chart=302&zoom=11&fpl=N0<?php echo $velocidade; ?>A<?php echo $niveldevoo; ?>%20<?php echo $_GET['origem']; ?>%20<?php echo $rotasugerida ?>%20<?php echo $_GET['destino']; ?>" class="button-skyvector" target="_blank"><img src="assets/img/skyvector1.png" style="height:15px;"></img></a>
                      <button onclick="copiarTexto()" class="button-route"><i class="fa fa-map-o" aria-hidden="true" ></i> <b>Utilizar</b></button>
                     </th>
                   <?php } ?>                                     
                   
                  </tr>
                </thead>
              </table>   
              <a href="https://app.nexatlas.com/auth" class="button-skyvector" target="_blank"><img src="https://nexatlas.com/wp-content/uploads/2021/07/Transparente-Horizontal-letra-preta.png" style="height:30px;"></img></a>
              <a href="https://skyvector.com/?ll=-15.860957599564097,-49.18029785623944&chart=302&zoom=11&fpl=N0<?php echo $velocidade; ?>A<?php echo $niveldevoo; ?>%20<?php echo $_GET['origem']; ?>%20<?php echo empty($db_voos['rota']) ? "":  $db_voos['rota'] ?>%20<?php echo $_GET['destino']; ?>" class="button-skyvector" target="_blank"><img src="assets/img/skyvector1.png" style="height:30px;"></img></a>
               <p></p>
                            <form method="post" id="atualizarota">
                            <div class="input-group mb-3">                      
                      <input type="text" class="form-control" id="rotainserida" style="text-transform: uppercase; font-weight: bolder;text-align:center;" name="rota" placeholder="Insira aqui sua rota e salve   >>>" value="<?php echo empty($db_voos['rota']) ? "":  $db_voos['rota'] ?>">
                      <input name="idvoo" value="<?php echo $_GET['idvoo']; ?>" hidden>                               
                      <input type="button" class="btn btn-success" id="insererota" name="insererota" value="Salvar">                      
                      <button id="copyrota" class="btn btn-outline-success">Copiar</button>
                           
                           </div>
                         </form>                      
                         <div id="mensagemrota"></div>                                    				    
                         </div> 
                         <!-- END ROTA -->

                         <!-- TEMPO DE VOO E COMBUSTIVEL -->
                            <hr>                                                        
                  <table class="table text-center">
                <thead>
                  <tr>
                    <th scope="col" class="text-center"><i class="ri-map-pin-time-line"></i></th>
                    <th scope="col"><span class="text-muted small pt-2 ps-1"> <?php echo "$distanceAB nm" ?> | </span><?php echo strtoupper($_GET['destino']); ?><span class="text-muted small pt-2 ps-1">| Dest</span></th>
                    <th scope="col"><span class="text-muted small pt-2 ps-1"> <?php echo "$distanceBC nm" ?> | </span><?php echo strtoupper($_GET['alternativo']); ?><span class="text-muted small pt-2 ps-1">| Altn</span></th>
                    <th scope="col">Extra</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>                  
                    <th scope="row" class="text-center"><i class="bi bi-clock" style="align:left"></i> Tempo de voo + Proc:</th>
                    <td class="text-center"><b><?php echo $timeABhoras  ?></b></td>
                    <td class="text-center"><?php echo $timeBChoras  ?></td>                    
                    <td class="text-center"><?php echo time_extra ?> min</td>
                  </tr>
                  <tr>
                    <th scope="row" class="text-center"><i class="bi bi-fuel-pump" style="align:left"></i> Combustível:</th>
                    <td class="text-center"> <?php echo $fuelAB  ?> <?php echo $unidade; ?> </td>
                    <td class="text-center"> <?php echo $fuelBC  ?> <?php echo $unidade; ?></td>                    
                    <td class="text-center"> <?php echo $fuelextra  ?> <?php echo $unidade; ?></td>
                  </tr>
                  <tr>
                    <th scope="row" class="text-center"><i class="bi bi-clock" style="align:left"></i> Autonomia:</th>
                    <td class="text-center"><strong><?php echo $autonomiahoras  ?></strong></td>
                    <td class="text-center"></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th scope="row" class="text-center"><i class="bi bi-fuel-pump" style="align:left"></i> Combustível mínimo: <p> Abastecido: </p></th>
                    <td class="text-center"><strong><?php echo $autonomiafuel  ?> <?php echo $unidade; ?>  <p> <?php echo !empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuel; ?> <?php echo $unidade; ?>  </p> </strong></td>
                    <td><span class="text-muted" style="font-size: 12px; margin-right:0px">Deseja alterar o combustível?</span></td>
                    <td><form method="post"> <input type="text"  class="form-control-sm" style="width:75px" name="fueleditado"><button name="editafuel" class="btn btn-success btn-sm" style="margin-left:2px"><i class="bi bi-fuel-pump"></i></button></form></td>
                  </tr>
                 </tbody>
              </table>          
                </div>
              </div>
            </div>
            <!-- END TEMPO DE VOO E COMBUSTIVEL -->
 
            <?php if ($aeronave !== "A29") {  ?>
            
            <!-- loadsheet and fuel-->
          
            <div class="col-12">
              <div class="card recent-sales overflow-auto">              
         
                <div class="card-body">
            
                
                <h5 class="card-title" style="text-align: center;">
    
    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M510.28 445.86l-73.03-292.13c-3.8-15.19-16.44-25.72-30.87-25.72h-60.25c3.57-10.05 5.88-20.72 5.88-32 0-53.02-42.98-96-96-96s-96 42.98-96 96c0 11.28 2.3 21.95 5.88 32h-60.25c-14.43 0-27.08 10.54-30.87 25.72L1.72 445.86C-6.61 479.17 16.38 512 48.03 512h415.95c31.64 0 54.63-32.83 46.3-66.14zM256 128c-17.64 0-32-14.36-32-32s14.36-32 32-32 32 14.36 32 32-14.36 32-32 32z"/></svg> LoadSheet <span id="unidade">(KG)</span>
 
    <!-- Unidade -->
    <div style="display: flex; justify-content: space-between; margin-top: 17px;">
        <div style="font-size: 13px; font-weight: bold;">
            <label>Unidade:</label>
            <select id="unitSelector" onchange="showSelectedValue(); convertlbtokg();">
                <option value="KG" selected>KG</option>
                <option value="LB">LB</option>
            </select>
        </div>
        <!-- editar carga --> 
        <div style="text-align: center; font-weight: bold; margin-top: 17px;">
    <form method="post">    
    <input placeholder="Editar Payload" type="text" class="form-control-sm" style="width:120px;" name="novo_payload" >
    <button name="editacarga" class="btn btn-sm btn-outline-primary ms-2">
                  <i class="fa fa-pencil"></i>
                </button>
  </form>
  </div>
<!-- temperatura -->
        <div style="font-size: 13px; font-weight: bold;">
        <?php if ($aeronave == "C95M") { ?>
          <label> <?php echo $origem; ?>(ºC):</label>
            <form method="post"> 
            <button id="abrirPMD" type="button" class="btn btn-sm btn-outline-primary" style="margin-left: 5px;"><i class="fa fa-book" aria-hidden="true"></i></button>
                <input type="text" class="form-control-sm" style="width:75px" name="temp_newdep"  value="<?php echo !empty($db_voos['temp_dep'])? $db_voos['temp_dep']: $temp_dep; ?>">
                <button name="editatemp" class="btn btn-success btn-sm" style="margin-left:2px"><i class="bi bi-sun"></i></button>
            </form> <?php
  } else if ($aeronave == "C97") { ?>
  <label> <?php echo $origem; ?>(ºC):</label>
<form method="post"> 
                <input type="text" class="form-control-sm" style="width:75px" name="temp_newdep"  value="<?php echo !empty($db_voos['temp_dep'])? $db_voos['temp_dep']: $temp_dep; ?>">
                <button name="editatemp" class="btn btn-success btn-sm" style="margin-left:2px"><i class="bi bi-sun"></i></button>
            </form>
            <label> <?php echo $destino; ?>(ºC):</label>
            <form method="post"> 
                <input type="text" class="form-control-sm" style="width:75px" name="temp_newldg" value="<?php echo !empty($db_voos['temp_ldg'])? $db_voos['temp_ldg']: $temp_ldg; ?>">
                <button name="editatemp" class="btn btn-success btn-sm" style="margin-left:2px"><i class="bi bi-sun"></i></button>
            </form>
   <?php } 
  ?>           
            
        </div>
    </div>    
    <div style="text-align: right; font-weight: bold; margin-top: 10px;">
        <span style="font-size: 13px;">* Altitude do AD: <?php echo $elevation1 ?> ft <br>* O MTOW pode variar de acordo com a temperatura e altitude do aeródromo.<br> <b><?php echo isset($mensagem_perf_c97)? $mensagem_perf_c97: null; ?></b></span>
    </div>
</h5>

<style>
    @media (max-width: 768px) {
        div[style*="display: flex"] {
            flex-direction: column;
            align-items: stretch;
        }

        div[style*="font-size: 13px"] {
            text-align: center;
            margin-bottom: 10px;
        }

        form[style*="display: inline-flex"] {
            flex-direction: column;
        }
    }
</style>
           
                  <table class="table responsive-table text-center">
                <thead>
                  <tr>
                  <th scope="col" class="text-center"> Peso (W)</th>
                    <th scope="col">TOW</th>
                    <th scope="col">TW</th>
                    <th scope="col">LW</th>
                    <th scope="col">ZFW</th>
                    <th scope="col">FUEL</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                  <th scope="row" class="text-center">MÁX </th> 
                    <td class = "table-active" data-attribute='lbtokg'><?php echo $mtow?> </td>
                    <td class = "table-active" data-attribute='lbtokg'><?php echo $mtw ?></td>
                    <td class = "table-active" data-attribute='lbtokg'><?php echo $mlw ?></td>
                    <td class = "table-active" data-attribute='lbtokg'><?php echo $mzfw ?></td>
                    <td class = "table-active" data-attribute='lbtokg'><?php echo $mfuelKG ?></td>
                  </tr>
                  <tr>
                    <th scope="row" class="text-center">PREV</th>
                    <?php
                    echo $tow <= $mtow ? "<td class = 'table-success' data-attribute='lbtokg'> $tow </td>": "<td class = 'table-danger' data-attribute='lbtokg'> $tow </td>";
                    echo $tw <= $mtw ? "<td class = 'table-success' data-attribute='lbtokg'> $tw </td>": "<td class = 'table-danger' data-attribute='lbtokg'> $tw </td>";
                    echo $lw <= $mlw ? "<td class = 'table-success' data-attribute='lbtokg'> $lw </td>": "<td class = 'table-danger' data-attribute='lbtokg'> $lw </td>";
                    echo $zfw <= $mzfw ? "<td class = 'table-success' data-attribute='lbtokg'> $zfw </td>": "<td class = 'table-danger' data-attribute='lbtokg'> $zfw </td>";
                    echo $fuelValueKG <= $mfuelKG ? "<td class = 'table-success' data-attribute='lbtokg'> $fuelValueKG </td>": "<td class = 'table-danger' data-attribute='lbtokg'> $fuelValueKG </td>";
                    ?>                                                                
                  </tr>
                </tbody>
              </table>

              <div style="display: flex; justify-content: center;">
              <table class="table responsive-table text-center">
               <thead>
                <tr>
  <th scope="col text-center">PBO</th>
  <th scope="col">FUEL</th>
  <th scope="col">
    PAYLOAD    
  </th>
  <th scope="col">DISP</th>
</tr>

                </thead>
            

      <input type="hidden" id="altitude" value="<?php echo $elevation1; ?>">
                <script>
    document.getElementById('abrirPMD').addEventListener('click', () => {
      Swal.fire({
        title: 'Calcular novo MTOW',
        input: 'text',
        inputLabel: 'Insira a temperatura',
        inputPlaceholder: 'ºC',
        showCancelButton: true,
        confirmButtonText: 'Calcular',
        preConfirm: (inputValue) => {
          if (!inputValue) {
            Swal.showValidationMessage('Por favor, insira um valor');
            return false;
          }

          // Retornar o valor para a próxima etapa
          return inputValue;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // Obter o valor da altitude do input hidden
          const altitude = document.getElementById('altitude').value;
          const pmd_atual = <?php echo $mtow; ?>;
          const modelo = "<?php echo $modelo; ?>"; // Exemplo: você pode obter essa informação dinamicamente

          // Fazer a requisição para o PHP com o valor inserido e a altitude
          fetch('async/fetchPMD95.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `inputValue=${encodeURIComponent(result.value)}&altitude=${encodeURIComponent(altitude)}&modelo=${encodeURIComponent(modelo)}`
          })
          .then(response => response.json())
          .then(data => {
           if (data.success) {
    // Define o MTOW máximo de acordo com o modelo
    let mtow_max;
    mtow_max = <?php echo $lista_aeronaves['mtow'];?> ;
    // Ajusta o resultado calculado ao limite do MTOW
    const mtow = Math.min(data.result, mtow_max);

    // Calcula a diferença para a disponibilidade de carga
    const diferenca = mtow - pmd_atual;

    // Gera a mensagem para o usuário
    const mensagem = `Teve uma alteração de ${diferenca} kg na disponibilidade de carga. Para atualizar, insira a nova temperatura na caixa ao lado.`;

    Swal.fire({
        title: `O MTOW Calculado é: ${mtow} kg`,
        text: mensagem,
        icon: "success"
    });
}
else {
              Swal.fire('Erro', data.message, 'error');
            }
          })
          .catch(error => {
            Swal.fire('Erro', 'Funcionalidade em manutenção', 'error');
          });
        }
      });
    });
  </script>

                <tbody>
                  <tr>
                    <td class= "table-info text-center" data-attribute='lbtokg'><?php echo $pbo ?></td>                   

        <td class='<?php     
    echo ($fuelValueKG > $mfuelKG ? "table-info table-danger" : "table-info"); 
?>' 
    id='lbtokg' 
    data-attribute='lbtokg'>
    <?php 
    echo $fuelValueKG; ?>
</td>    
                    <td class = "table-info" data-attribute='lbtokg'><?php echo empty($cargaprev)? "Não informado": $cargaprev ?></td>                   
                  
                    <?php 
                     if( $dispresult >= 0 ) {
                      echo "<td class = 'table-success' data-attribute='lbtokg'> <strong> $dispresult </strong></td>";
                    }
                    else {
                     echo "<td class = 'table-danger' data-attribute='lbtokg'> <Strong>$dispresult</strong></td>";
                    }
                   ?>
                  </tr>
                <tr>               
              
                  <td></td>
                  
                  <td colspan="2"><?php 
                     if( $dispresult == $disptkoff ) {
                      echo "<td style='font-size:80%;'>Limitado MTOW </td>";
                    }
                    else {
                     echo "<td style='font-size:80%;'> Limitado MLW</td>";
                    }
                   ?>  </td>
                              
                  </tr>            
       
                </tbody>
              </table>  
                  </div>
                
                </div>

              </div>
            </div> 
            <!-- END loadsheet and fuel-->

            <?php } ?>
            
            <!-- SCRIPT MODAL ALERTA LOADSHEET ERRO -->
            <?php
   
   if ($tow > $mtow || $lw > $mlw || $dispresult < 0 || $fuelValueKG > $mfuelKG) {
      echo ' 
      <script>
    document.addEventListener("DOMContentLoaded", function() {
      Swal.fire({
        title: "Atenção",
        text: "Aeronave fora do envelope. Por favor, verifique seu planejamento.",
        icon: "error",      
        showCancelButton: true,
        confirmButtonText: "Editar voo",
        cancelButtonText: "Fechar"
      }).then((result) => {
        if (result.isConfirmed) {          
          window.location.href = "editplanner.php?id=' . $idvoo . '";
        }
      });
    });
    </script>';
    }
    if (!empty($db_voos['fuel']) && $db_voos['fuel'] < $autonomiafuel) {
      echo '
      <script>
          document.addEventListener("DOMContentLoaded", function() {
              Swal.fire({
                  title: "Atenção",
                  text: "Abastecido abaixo do combustível mínimo.",
                  icon: "error",
                  showCancelButton: false,
                  confirmButtonText: "OK",
                  cancelButtonText: "Fechar"
              });
          });
      </script>';
    }
    ?>
    

          

            <!-- REDEMET-->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
              <div class="card-body">
              <h5 class="card-title" style="text-align: center;"><i class="bi bi-cloud-sun"></i> Briefing meteorológico<span></span></h5>            
 
   <!-- Botões para alternar entre Redemet, Windy e Cartas SIGWX -->
<div class="d-flex justify-content-center mb-4">
    <button class="btn btn-outline-primary me-2" type="button" onclick="toggleCollapse('redemet')" id="btn-redemet" disabled>
        Redemet
    </button>
    <button class="btn btn-outline-danger me-2" type="button" onclick="toggleCollapse('windy')" id="btn-windy">
        Windy
    </button>
    <button class="btn btn-outline-primary" type="button" onclick="toggleCollapse('sigwx')" id="btn-sigwx">
        Cartas SIGWX/Wind Aloft
    </button>
</div>

<!-- Collapsible Content -->
<div class="collapse show" id="redemet">            
    <iframe width="100%" height="600" src="https://www.redemet.aer.mil.br/" frameborder="0"></iframe>    
</div>

<div class="collapse" id="windy" style="display: none;">            
    <iframe width="100%" height="600"
        src="https://embed.windy.com/embed2.html?lat=<?php echo $lat1;?>&lon=<?php echo $long1; ?>&detailLat=<?php echo $lat1;?>&detailLon=<?php echo $long1; ?>&width=650&height=450&zoom=5&level=surface&overlay=clouds&product=ecmwf&menu=&message=&marker=&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=kt&metricTemp=%C2%B0C&radarRange=-1"
        frameborder="0"></iframe>    
</div>

<div class="collapse" id="sigwx" style="display: none;">  
<div class="container d-flex justify-content-center align-items-center">
    <div style="width: 600px; max-height: 90vh; overflow-y: auto;">      
    <?php include 'includes/menu_cartasmeteo.php'; ?>
    </div>
    
    </div>
</div>

<!-- JavaScript para alternar entre os botões -->
<script>
    function toggleCollapse(target) {
        const sections = ['redemet', 'windy', 'sigwx'];

        // Iterar sobre todas as seções
        sections.forEach(section => {
            const button = document.getElementById(`btn-${section}`);
            const collapse = document.getElementById(section);

            if (section === target) {
                // Ativar o botão e exibir a seção correspondente
                button.disabled = true;
                collapse.style.display = "block";
            } else {
                // Desativar os outros botões e esconder as respectivas seções
                button.disabled = false;
                collapse.style.display = "none";
            }
        });
    }

    // Garantir que apenas a seção correta está visível ao carregar a página
    document.addEventListener("DOMContentLoaded", () => {
        toggleCollapse('redemet'); // Exibir apenas Redemet ao carregar
    });
</script>



             
                            <hr>                             
                            <div class="text-center" >  
                            <a href="https://www.redemet.aer.mil.br/old/gera_pdf.php?acao=autoatendimento&localidades=<?php echo $_GET['origem']; ?>%2C<?php echo $_GET['destino']; ?>%2C<?php echo $_GET['alternativo']; ?>&nivelvoo=<?php echo $niveldevoo ?>&datahora=<?php echo "$datadovoobr"; ?>+<?php echo $_GET['horadep']; ?>&fir%5B%5D=SBAZ&fir%5B%5D=SBAO&fir%5B%5D=SBBS&fir%5B%5D=SBCW&fir%5B%5D=SBRE&fir_extra=&msg_met%5B%5D=metar&msg_met%5B%5D=taf&sigwx=sigwx&vento=vento&img_sat=img_sat&embedded=true" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Briefing REDEMET </a>                        
                            <p></p>
                           <!--<a  class="btn btn-outline-warning" style="text-align: center;" href="https://www.ogimet.com/display_gramet.php?icao=<?php echo strtoupper($_GET['origem']); ?>_<?php echo strtoupper($_GET['destino']); ?>&hini=&tref=<?php echo $hdep_timestamp ?>&hfin=<?php echo $gramet_hour?>&fl=<?php echo $niveldevoo ?>&enviar=Enviar" target="_blank"><i class="fa fa-cloud" aria-hidden="true"></i> Gerar GRAMET</a> -->
                           <!--<a  class="btn btn-outline-primary" style="text-align: center;" href="gramet.php?rota=<?php echo $_GET['origem']; ?>%20<?php echo $_GET['destino']; ?>&dep_time=<?php echo $hdep_timestamp ?>&fl=<?php echo $niveldevoo ?>00&eet=<?php echo $timeABseg ?>" target="_blank"><i class="fa fa-cloud" aria-hidden="true"></i> Gerar GRAMET (autorouter)</a>-->
                           <a class="btn btn-outline-primary" style="text-align: center;" id="generate-gramet" href="gramet.php?rota=<?php echo $coord_origem; ?>%20<?php echo $coord_dest; ?>&dep_time=<?php echo $hdep_timestamp ?>&fl=<?php echo $niveldevoo ?>00&eet=<?php echo $timeABseg ?>" target="_blank">
                           <i class="fa fa-cloud" aria-hidden="true"></i> Gerar GRAMET (autorouter)
                           </a>
                          </div>
                            <hr> 
                            <script>
document.getElementById('generate-gramet').addEventListener('click', function(event) {
    event.preventDefault(); // Impede o redirecionamento imediato

    const href = this.href; // Obtém o link do botão

    Swal.fire({
    title: 'Confirmação',
    html: `
        <p>As coordenadas mostradas no PDF são dos aeródromos de <b>(ORGN e DEST)</b>.</p>
        <img src="assets/img/gramet2.png" alt="GRAMETex" style="width: 200px; height: auto; margin-top: 10px;">
    <br>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Gerar Gramet',
    cancelButtonText: 'Cancelar'
}).then((result) => {
    if (result.isConfirmed) {
        // Redireciona para o link após a confirmação
        window.open(href, '_blank');
    }
});

});
</script>
              </div>
            </div>
          </div>
          </div>
          <!-- End REDEMET -->          
        
             
    


        </div>
        <!-- End Left side columns -->

        <script src="https://api.checkwx.com/widget?key=<?php echo $checkwx_key;?>
" type="text/javascript"></script> 

        <!-- Right side columns -->
       
        <div class="col-lg-4">

<!-- METAR ORGN-->
<div class="card">      
  <div class="card-body overflow-auto">
    <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['origem']; ?>"></div>             
    <div class="activity">            
   <a href="https://metar-taf.com/pt/<?php echo $_GET['origem']; ?>" id="metartaf-tqHtSN4U" style="font-size:18px; font-weight:500; color:#000; width:350px; height:265px; display:block">METAR Aeroporto Internacional do Recife/Guararapes-Gilberto Freyre</a>
     <script async defer crossorigin="anonymous" src="https://metar-taf.com/pt/embed-js/<?php echo $_GET['origem']; ?>?u=2660&layout=landscape&target=tqHtSN4U"></script>
     <br>
     <div class="checkwx-container" data-type="TAF" data-station="<?php echo $_GET['origem']; ?>"></div>              
    </div>
  </div>            
</div>
<!-- END METAR ORGN-->

<!-- METAR DEST -->
<div class="card">     
  <div class="card-body overflow-auto">
  <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['destino']; ?>"></div> 
    <div class="activity">              
   <a href="https://metar-taf.com/pt/<?php echo $_GET['destino']; ?>" id="metartaf-TXwWEOe1" style="font-size:18px; font-weight:500; color:#000; width:350px; height:265px; display:block">METAR Aeroporto de Madrid-Barajas</a>
<script async defer crossorigin="anonymous" src="https://metar-taf.com/pt/embed-js/<?php echo $_GET['destino']; ?>?u=2660&layout=landscape&target=TXwWEOe1"></script>
<br>
<div class="checkwx-container" data-type="TAF" data-station="<?php echo $_GET['destino']; ?>"></div>

    </div>
  </div>            
</div>
<!-- END METAR DEST-->

<!-- METAR ALTN -->
<div class="card">             
  <div class="card-body overflow-auto">
    <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['alternativo']; ?>"></div>             
    <div class="activity">           
     <div class="checkwx-container" data-type="TAF" data-station="<?php echo $_GET['alternativo']; ?>"></div>
    </div>
  </div>            
</div>
<!-- END METAR ALTN-->

<!-- NOTAM - ROTAER - INFOTEMP -->
  
 
<div class="card">
<div class="text-center">
  <img src="assets/img/aisweb.png" style="width:150px;height:80px;text-align:center">
</div>
<h5 class="card-title" style="text-align: center;">ROTAER<span></span></h5>
<table class="table text-center">             
              <tr> 
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#rotaerorigem">
<b><?php echo $origem; ?></b>
</button></th>
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#rotaerdestino">
<b><?php echo $destino; ?></b>
</button></th>
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#rotaeraltn">
<b><?php echo $alternativo; ?></b>
</button></th>
              </tr>   
             </table>

<!-- Modais -->
<!-- Large Modal 1 -->
<div class="modal" id="rotaerorigem">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">ROTAER</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert" id="rotaer-origem-content">  
      Buscando dados...
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Large Modal 2 -->
<div class="modal" id="rotaerdestino">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">ROTAER</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert" id="rotaer-destino-content">  
      Buscando dados...
                  </div>
      </div>
    </div>
  </div>
</div>

<!-- Large Modal 3 -->
<div class="modal" id="rotaeraltn">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">ROTAER</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert" id="rotaer-alternativo-content">  
      Buscando dados...
                  </div>
      </div>
    </div>
  </div>
</div>



<!-- NOTAM -->
<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#notamorigem" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
  NOTAM <strong><?php echo $origem; ?></strong>
</a>
<br>
<div class="collapse" id="notamorigem">
  <div class="alert alert-primary alert-dismissible fade show" role="alert" id="notam-origem-content">
  Buscando dados...
  </div>
</div>

<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#notamdest" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
  NOTAM <strong><?php echo $destino; ?></strong>
</a>
<br>
<div class="collapse" id="notamdest">
  <div class="alert alert-primary alert-dismissible fade show" role="alert" id="notam-destino-content">
      Buscando dados...
  </div>
</div>

<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#notamaltn" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
  NOTAM <strong><?php echo $alternativo; ?></strong>
</a>
<br>
<div class="collapse" id="notamaltn">
  <div class="alert alert-primary alert-dismissible fade show" role="alert" id="notam-alternativo-content">
      Buscando dados...
  </div>
</div>

<hr>
<style> 
   
  hr.border2px {
      border: 2px solid;  }
</style>
<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#infotemp" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
  INFOTEMP
</a>
<br>
<div class="collapse" id="infotemp">
  <div class="alert alert-primary alert-dismissible fade show" role="alert">
      <div id="infotemp-origem-content">Buscando dados...</div>
      <hr class="border2px">
      <div id="infotemp-destino-content">Buscando dados...</div>
      <hr class="border2px">
      <div id="infotemp-alternativo-content">Buscando dados...</div>
  </div>
</div>

<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#supaip" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
 SUP AIP
</a>
<br>
<div class="collapse" id="supaip">
  <div class="alert alert-primary alert-dismissible fade show" role="alert">
      <div id="supaip-origem-content">Buscando dados...</div>
      <hr class="border2px">
      <div id="supaip-destino-content">Buscando dados...</div>
      <hr class="border2px">
      <div id="supaip-alternativo-content">Buscando dados...</div>
  </div>
</div>
</div>
<!-- END NOTAM - ROTAER - INFOTEMP -->


<!-- DONATION -->
   <div class="card">            
  <div class="card-body overflow-auto" style="text-align:center">                     
    <p class="card-title" style="font-size: 14px;"> Ajude o NavBrief a manter os seus servidores ativos a fim de prover o melhor planejamento para o seu voo.</p>
    <a href="donation.php" target="_blank"  class="btn btn-outline-success btn-sm"><b> Ajude o NavBrief! </b><i class="fa-brands fa-pix"></i></a>
                  
  </div>            
</div>
<!-- END DONATION -->

</div>          <!-- End Right side columns -->

      </div>

    </section>    
    
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'includes/footer.php'   ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script> function fetchData(endpoint, elementId) {
        document.getElementById(elementId).innerHTML = 'Buscando dados...';
        fetch(endpoint)
            .then(response => response.text())
            .then(data => {
                document.getElementById(elementId).innerHTML = data;
            })
            .catch(error => {
                document.getElementById(elementId).innerHTML = 'Erro ao buscar dados.';
                console.error('Erro ao buscar dados:', error);
            });
    }
    </script>
    
    <script>
     
    document.addEventListener("DOMContentLoaded", function() {
        // URL original em PHP
        var link = "<?php echo $linkvoo ?>";
        
        // Parâmetros para a solicitação da API TinyURL
        var data = {
            "url": link
        };

        // Realiza a solicitação POST para a API TinyURL
        fetch('https://api.tinyurl.com/create', {
            method: 'POST',
            headers: {
                'accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'Bearer H84OxOsJGXPvKLdBqW9FGrlONwyURl19Ha5cb97mjkCMCrXBmBfZHe9EpgQe'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.data && data.data.tiny_url) {
                var shortLink = data.data.tiny_url;
                var copyButton = document.getElementById("copyLinkButton");

                if (copyButton) {
                    // Define o evento de clique para o botão "Copiar Link"
                    copyButton.onclick = function() {
                        // Cria um elemento input temporário para selecionar e copiar o texto
                        let inputTemp = document.createElement("input");
                        inputTemp.value = "Planejamento <?php echo htmlspecialchars($origem, ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($destino, ENT_QUOTES, 'UTF-8'); ?>: " + shortLink;
                        document.body.appendChild(inputTemp);

                        // Seleciona e copia o texto
                        inputTemp.select();
                        document.execCommand('copy');

                        // Remove o elemento temporário
                        document.body.removeChild(inputTemp);

                        // Exibe mensagem de sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'Link Copiado! Compartilhe.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    };
                } else {
                    console.error("Botão de cópia não encontrado.");
                }
            } else {
                throw new Error("Falha ao obter a URL encurtada.");
            }
        })
        .catch(function(error) {
            // Exibe mensagem de erro
            Swal.fire({
                icon: 'error',
                title: 'Erro ao encurtar o link!',
                text: error.message,
                showConfirmButton: false,
                timer: 3000
            });
        });
    });



           // Função que simula o progresso de carregamento
           var interval; // Variável para armazenar o intervalo da simulação

function simulateLoading() {
    var progressBar = document.getElementById('progress-bar');
    var progress = 0;

    interval = setInterval(function() {
        if (progress < 80) { // Limita o progresso a 90% enquanto a página carrega
            progress += Math.random() * 10; // Simula o progresso aleatório
            progressBar.style.width = progress + '%';
            progressBar.innerHTML = Math.floor(progress) + '%'; // Exibe a porcentagem dentro da barra
        }
    }, 100); // Atualiza a barra de progresso a cada 100ms
}

// Simula a finalização do carregamento ao completar o carregamento da página
window.onload = function() {
    var progressBar = document.getElementById('progress-bar');
    var overlay = document.getElementById('overlay');
    var conteudo = document.getElementById('main');

    // Faz o progresso ir direto para 100%
    progressBar.style.width = '100%';
    progressBar.innerHTML = '100%';

    // Aguarda um breve momento e então esconde o overlay
    setTimeout(function() {
        clearInterval(interval); // Interrompe a simulação de progresso
        overlay.style.display = 'none'; // Esconde o overlay
        conteudo.style.display = 'block'; // Exibe o conteúdo da página
    }, 500); // Adiciona um pequeno atraso antes de ocultar o overlay
};

// Inicia o carregamento assim que a página é requisitada
simulateLoading();
    </script>
                      <script>
        // Referências aos elementos
        const rotainserida = document.getElementById('rotainserida');
        const copyrota = document.getElementById('copyrota');

        // Função para copiar o texto do input
        copyrota.addEventListener('click', function(event) {
          event.preventDefault(); // Impede o comportamento padrão do botão
            if (rotainserida.value.trim() !== "") {
                rotainserida.select();
                document.execCommand('copy');
                Swal.fire({
                        icon: 'success',
                        title: 'Rota Copiada!',
                        text: 'Rota: ' + rotainserida.value.toUpperCase(),
                        showConfirmButton: false,
                        timer: 2000
                    });   
            } else {
              Swal.fire({
                        icon: 'error',
                        title: 'Insira sua rota',                        
                        showConfirmButton: false,
                        timer: 2000
                    });   
            }
        });
    </script>
    <script>document.addEventListener("DOMContentLoaded", function() {
    const params = {
        origem: "<?php echo $origem; ?>",
        destino: "<?php echo $destino; ?>",
        alternativo: "<?php echo $alternativo; ?>"
    };   

    /* Eventos para abrir modais e carregar dados */
    document.querySelector('button[data-target="#rotaerorigem"]').addEventListener('click', function () {
                fetchData(`async/getrotaer.php?location=${params.origem}`,'rotaer-origem-content');
            });

            document.querySelector('button[data-target="#rotaerdestino"]').addEventListener('click', function () {
                fetchData(`async/getrotaer.php?location=${params.destino}`,'rotaer-destino-content');
            });

            document.querySelector('button[data-target="#rotaeraltn"]').addEventListener('click', function () {
                fetchData(`async/getrotaer.php?location=${params.alternativo}`,'rotaer-alternativo-content');
            });  
    document.getElementById('notamorigem').addEventListener('show.bs.collapse', function () {
        fetchData(`async/getnotam.php?location=${params.origem}`, 'notam-origem-content');
    });
    document.getElementById('notamdest').addEventListener('show.bs.collapse', function () {
        fetchData(`async/getnotam.php?location=${params.destino}`, 'notam-destino-content');
    });
    document.getElementById('notamaltn').addEventListener('show.bs.collapse', function () {
        fetchData(`async/getnotam.php?location=${params.alternativo}`, 'notam-alternativo-content');
    });
    document.getElementById('infotemp').addEventListener('show.bs.collapse', function () {
        fetchData(`async/getinfotemp.php?location=${params.origem}`, 'infotemp-origem-content');
        fetchData(`async/getinfotemp.php?location=${params.destino}`, 'infotemp-destino-content');
        fetchData(`async/getinfotemp.php?location=${params.alternativo}`, 'infotemp-alternativo-content');
    });
     document.getElementById('supaip').addEventListener('show.bs.collapse', function () {
        fetchData(`async/getsupaip.php?location=${params.origem}`, 'supaip-origem-content');
        fetchData(`async/getsupaip.php?location=${params.destino}`, 'supaip-destino-content');
        fetchData(`async/getsupaip.php?location=${params.alternativo}`, 'supaip-alternativo-content');
    });
});</script>

<!-- MODAL CARTAS -->
<script>
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function () {
            const icao = document.getElementById('modal-icao').textContent.split(' - ')[0]; // Obtém o ICAO do modal
            const type = this.getAttribute('data-type'); // Obtém o tipo de carta (PDC, ADC, etc.)
            const contentId = `content-${type.toLowerCase()}`; // Define o ID do elemento onde o conteúdo será inserido

            // Verifica se o conteúdo já foi carregado
            const contentElement = document.getElementById(contentId);
            if (!contentElement.getAttribute('data-loaded')) {
                fetch(`async/getcartas.php?location=${icao}&type=${type}`)
                    .then(response => response.text())
                    .then(data => {
                        contentElement.innerHTML = data;
                        contentElement.setAttribute('data-loaded', 'true'); // Marca como carregado
                    })
                    .catch(error => {
                        console.error('Erro ao carregar cartas:', error);
                        contentElement.innerHTML = '<p>Erro ao carregar cartas. Tente novamente mais tarde.</p>';
                    });
            }
        });
    });

    // Atualiza o título do modal e reinicia os collapses ao abrir o modal
    document.getElementById('cartas-modal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Botão que acionou o modal
        const icao = button.getAttribute('data-icao'); // Obtém o ICAO do aeródromo
        const nome = button.getAttribute('data-nome'); // Obtém o nome do aeródromo

        document.getElementById('modal-icao').textContent = `${icao} - ${nome}`;

        // Reinicia os collapses
        document.querySelectorAll('.accordion-collapse').forEach(collapse => {
            collapse.classList.remove('show');
            collapse.previousElementSibling.setAttribute('aria-expanded', 'false');
        });

        // Remove o estado "carregado" dos conteúdos
        document.querySelectorAll('.accordion-body').forEach(body => {
            body.innerHTML = '<p>Carregando...</p>';
            body.removeAttribute('data-loaded');
        });
    });
</script>
    
<script>
function openViewer(url, nome) {
        // Remove o modal anterior se já existir
        let existingModal = document.getElementById("viewerModal");
        if (existingModal) {
            existingModal.remove();
        }
    
        // Criar um identificador único para evitar cache no iframe
        let uniqueUrl = url + "?nocache=" + new Date().getTime();
    
        // Criar o modal dinamicamente
        let modalHTML = `
            <div class="modal fade" id="viewerModal" tabindex="-1" aria-labelledby="viewerModalLabel" aria-hidden="true" 
                style="z-index: 9999; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="viewerModalLabel">Visualizando: ${nome}</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <iframe id="viewerFrame" src="https://docs.google.com/gview?url=${encodeURIComponent(uniqueUrl)}&embedded=true" 
                                width="100%" height="600px" style="border: none;"></iframe>
                        </div>
                        <div class="modal-footer">
                            <a id="downloadCarta" href="${url}" target="_blank" class="btn btn-primary">
                                <i class="fa fa-download"></i> Baixar Carta
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    
        // Adiciona o modal ao body
        document.body.insertAdjacentHTML("beforeend", modalHTML);
    
        // Aguarda um pequeno tempo para garantir que o modal seja carregado corretamente
        setTimeout(() => {
            var viewerModal = new bootstrap.Modal(document.getElementById("viewerModal"), { backdrop: 'static' });
            viewerModal.show();
        }, 100);
    }
  
  </script>



  <!-- Vendor JS Files -->
  <script src="assets/js/semrefresh.js" type="text/javascript"></script>
  <script src="assets/js/jquery-3.1.1.min.js" type="text/javascript"></script> 
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script> 
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
 
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/atualiza_fuel.js"></script>
  <script src="assets/js/scripts-navbrief.js"></script>
  <script src="assets/js/scripts-dashplanner.js"></script>
 
    
</body>

</html>