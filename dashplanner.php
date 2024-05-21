
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
//$pesotrip= $_GET['pesotrip'];
$cargaprev = $_GET['cargaprev'];

// CAPTURA O ULTIMO ID VOO NO BANCO DE DADOS //
$sqlid = "SELECT id FROM voos";
$result = mysqli_query($conexao, $sqlid);
$last_id = array();
    
    while ($row_id = mysqli_fetch_assoc($result)) {       
        $last_id[] = $row_id['id'];
    }
// CAPTURA O ULTIMO ID VOO NO BANCO DE DADOS //   

//SELECIONA AS INFO DOS AEROPORTOS E GUARDA EM UM ARRAY //

$sqlairports = "SELECT ident,name,latitude_deg,longitude_deg,elevation_ft,iata_code FROM airports";
$queryairports = mysqli_prepare($conexao, $sqlairports);
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

if (isset($origem) && isset($airports[$origem])) {
    $nomeorigem = $airports[$origem]['nome'];
    $lat1 = $airports[$origem]['lat'];
    $long1 = $airports[$origem]['long'];
    $elevation1 = $airports[$origem]['elevation'];
    $iataorigem = $airports[$origem]['iata'];
}

if (isset($destino) && isset($airports[$destino])) {
    $nomedestino = $airports[$destino]['nome'];
    $lat2 = $airports[$destino]['lat'];
    $long2 = $airports[$destino]['long'];
    $elevation2 = $airports[$destino]['elevation'];
    $iatadestino = $airports[$destino]['iata'];
}

if (isset($alternativo) && isset($airports[$alternativo])) {
    $nomealternativo = $airports[$alternativo]['nome'];
    $lat3 = $airports[$alternativo]['lat'];
    $long3 = $airports[$alternativo]['long'];
    $elevation3 = $airports[$alternativo]['elevation'];
    $iataalternativo = $airports[$alternativo]['iata'];
}
//SELECIONA AS INFO DOS AEROPORTOS //

// SELECIONA AS INFO DAS ANV //
  
if (isset($id_aeronave) && is_numeric($id_aeronave)) {  
  $sql = "SELECT * FROM aeronaves WHERE id = ?";
  $stmt = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($stmt, 'i', $id_aeronave);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);  
  if ($lista_aeronaves = mysqli_fetch_assoc($result)) {
      $mtow = $lista_aeronaves['mtow'];
      $mzfw = $lista_aeronaves['mzfw'];
      $mfuel = $lista_aeronaves['mfuel'];
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

  // NIVEL DE VOO SUGERIDO SCRIPT //
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
  "C99" => 'getniveldevoo_3'
];
if (isset($nivel_voo_map[$aeronave])) {
  $function_name = $nivel_voo_map[$aeronave];
  $nivelsugerido = $function_name($proamag);
} else {
  $nivelsugerido = "Erro";
}
  $niveldevoo = empty($_GET['niveldevoo']) ? $nivelsugerido : $_GET['niveldevoo'];
//convertendo data pra BR
$datadovoobr = date("d/m/Y",strtotime($_GET['datadovoo']));

//FUNCTION DISTANCE and FLIGHT TIME

$distanceAB = get_distance($lat1,$long1,$lat2,$long2);
$distanceBC = get_distance($lat2,$long2,$lat3,$long3);

$timeABmin = get_flighttime($velocidade,$distanceAB) + time_proc;
$timeBCmin = get_flighttime($velocidade,$distanceBC) + time_proc;

$fuelAB =  ($aeronave == "C97" || $aeronave == "C105")? calcfuel_2($timeABmin,$consumo,$consumo2):calcfuel($timeABmin,$consumo);
$fuelBC = ($aeronave == "C97" || $aeronave == "C105")? calcfuel($timeBCmin,$consumo2):calcfuel($timeBCmin,$consumo);
$fuelproc = ($aeronave == "C97" || $aeronave == "C105")? calcfuel(time_proc,$consumo2):calcfuel(time_proc,$consumo);
$fuelextra = ($aeronave == "C97" || $aeronave == "C105")? calcfuel(time_extra,$consumo2) :calcfuel(time_extra,$consumo);

$consumo_por_min = $consumo/60;
if($aeronave == "T27M") {
  $autonomiafuel = autonomiafuel_t27;
} else if ($aeronave == "C97") {
  $autonomiafuel = ($fuelAB + $fuelBC + $fuelproc + $fuelextra) < 1200? 1200: round(($fuelAB + $fuelBC + $fuelproc + $fuelextra),-2) ;
}
else if ($aeronave == "C105") {
  $autonomiafuel = ceil(($fuelAB + $fuelBC + $fuelextra + 200 )/100)*100; 
}
else {
  $autonomiafuel = ceil(($fuelAB + $fuelBC + $fuelproc + $fuelextra)/100)*100; 
}

/*  INSERE COMBUSTÍVEL ABASTECIDO NO DB */
if(isset($_POST['fueleditado'])) {
  $fueleditado = $_POST['fueleditado']; //LB 
  $addfuel = mysqli_query($conexao, "UPDATE voos SET fuel = '$fueleditado' WHERE id = '$idvoo'") or die(mysqli_error($conexao));
}
/* BUSCA ROTA E FUEL NO DB */
$selectrota = mysqli_query($conexao, "SELECT rota,fuel from voos WHERE id='$idvoo'") or die(mysqli_error($conexao));
$db_voos = mysqli_fetch_assoc($selectrota);

/* NOVO PMD DE ACORDO COM ALTITUDE E TEMP  C-95M */
$temp =  30;
if(isset($_POST['temp_new'])) {
  $temp = $_POST['temp_new']; //LB 
  
}

/* TAXI WEIGHT */
$consumo_taxi_map = [
  "C95M" => 30,
  "C97" => 70,
  "C105" => 50,
  "C98" => 15
];
// Verifica se a aeronave está no array e atribui o consumo correspondente
$consumo_taxi = $consumo_taxi_map[$aeronave] ?? 0; // Valor padrão 0 se a aeronave não estiver no array

if ($aeronave == "C95M") {
  $mtow = calculatePMD95($temp, $elevation1);  
  $mtow = ($mtow > 6000) ? 6000 : $mtow;
  $mtw = ($mtow + 30 > 6030) ? 6030 : $mtow + 30;
  }
 
 /* EDITAR COMBUSTIVEL */
if(!empty($db_voos['fuel'])) {
$novofuel = $db_voos['fuel'];
$novofuelKG = ceil($novofuel * 0.453592);
$autonomiamin = $novofuel/$consumo_por_min; //$timeABmin + $timeBCmin  + time_extra;
$disptkoff = ($unidade == "kg")? (int)$mtow - (int)$pbo - (int)$cargaprev - $novofuel: (int)$mtow - (int)$pbo - (int)$cargaprev - $novofuelKG;
$displand = ($unidade == "kg")? (int)$mlw - (int)$pbo - (int)$cargaprev - $novofuel + $fuelAB: (int)$mlw - (int)$pbo - (int)$cargaprev - $novofuelKG + $fuelAB * 0.453592;
$dispresult = min(array($disptkoff, $displand));
$tw = ($unidade == "kg")? (int)$pbo + (int)$cargaprev + $novofuel: (int)$pbo + (int)$cargaprev + $novofuelKG ;
$tow = (int)$tw - $consumo_taxi;
$lw = (int)$tow - ceil($fuelAB * 0.453592);
$zfw = (int)$pbo + (int)$cargaprev;
} else {

$autonomiamin = $timeABmin + $timeBCmin  + time_extra;
$autonomiafuelKG = ceil($autonomiafuel*0.453592);

  $disptkoff = ($unidade == "kg")? (int)$mtow - (int)$pbo - (int)$cargaprev - $autonomiafuel: (int)$mtow - (int)$pbo - (int)$cargaprev - $autonomiafuelKG;
  $displand = ($unidade == "kg")? (int)$mlw - (int)$pbo - (int)$cargaprev - $autonomiafuel + $fuelAB: (int)$mlw - (int)$pbo - (int)$cargaprev - $autonomiafuelKG + $fuelAB * 0.453592;
  $dispresult = floor(min(array($disptkoff,$displand)));
  $tw = ($unidade == "kg")? (int)$pbo + (int)$cargaprev + $autonomiafuel: (int)$pbo + (int)$cargaprev + $autonomiafuelKG ; 
  $tow = (int)$tw - $consumo_taxi;
  $lw = (int)$tow - ceil($fuelAB*0.453592);
  $zfw = (int)$pbo + (int)$cargaprev;
}
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
$gramet_hour = ceil($timeABmin/60);

$hdepseg = strtotime($_GET['horadep']);
$timeABseg = $timeABmin*60;
$etaseg = $hdepseg + $timeABseg;
$etahoras = date('H:i',$etaseg);
  
// PEGAR URL DA PAGINA
$protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
$url = '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 

$linkvoo = $protocolo.$url;
$linkcurto = "<script>document.write(texto)</script>";

if(isset($_GET['submit']) && isset($_GET['idvoo']) && !in_array($_GET['idvoo'], $last_id))
      { 
        include_once('lib/conn.php');       
     
       $resultaddflight = mysqli_query($conexao,"INSERT INTO `voos`(`id`,`origem`, `destino`, `alternativo`, `rota`, `niveldevoo`, `datadovoo`, `aeronave`,`horadep`,`cargaprev`,`criado`) VALUES ( '$idvoo', '$origem','$destino','$alternativo','','$niveldevoo','$datadovoo','$id_aeronave','$horadep','$cargaprev',NOW())") or die(mysqli_error($conexao));
    
      }  

 $conexao->close();
?>
<head><link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png"><link href="assets/css/style.css" rel="stylesheet"></head>
<div id="overlay"><!--<img src = "../assets/img/fdicon.png">--><div class="loading-spinner"></div><span class="loading-text">Mantenha o ponto de espera<br>Estamos planejando o seu voo...<br></span></div>


<?php include 'includes/header.php' ?>
<?php include 'includes/sidebar.php' ?>

  <main id="main" class="main">  

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
      <Style>
      .rightmenu { display: inline-block; float: right; padding-right: 10px;}
      .leftmenu { display: inline-block; float: left; padding-left: 10px;}
      </style>
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
          <th class="text-muted small"> ou &nbsp</th>          
          <th class="text-muted small" style="text-align:right;margin-right:10px;"> Aponte a câmera do seu celular </th>
      </tr>
      <tr>
        <td><button onClick="shorturlecopiar()" class="btn btn-outline-success btn-sm"><i class="fa fa-link" aria-hidden="true" ></i> Copiar Link</button></td>
        <td></td>
        <td rowspan="2" style="text-align:right"><img src= "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo urlencode($linkvoo) ?>" alt='QR Code'></td>
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

            <!-- ORIGEM CARD-->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
               
                <div class="card-body">
                <h5 class="card-title"> <span> Origem | ETD: </span> <?php echo ($_GET['horadep']);  ?> Z</h5>

                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                      <h6><img src="assets/img/departures.png" style="width:30px;height:30px;">  <?php echo strtoupper($_GET['origem']); ?><span style="font-size:13px;">(<?php echo $elevation1; ?> ft)</span></img><a href="https://www.opennavcharts.com.br/app/search?icao=<?php echo $_GET['origem']; ?>&procedureType=TAXI" target="_blank"  class="button-carta"><i class="bi bi-map"></i> Cartas</a></h6>
                      <span class="text-success small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1" style="font-size:13px;"> <?php echo $nomeorigem ?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- END ORIGEM CARD -->

          

            <!-- DESTINO CARD-->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">
              

                <div class="card-body">
                <h5 class="card-title"> <span> Destino | ETA: </span> <?php echo $etahoras?> Z</h5>

                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                      <h6><img src="assets/img/arrivals.png" style="width:30px;height:30px;">  <?php echo strtoupper($_GET['destino']); ?><span style="font-size:13px;">(<?php echo $elevation2; ?> ft)</span></img><a href="https://www.opennavcharts.com.br/app/search?icao=<?php echo $_GET['destino']; ?>&procedureType=TAXI" target="_blank"  class="button-carta"><i class="bi bi-map"></i> Cartas</a></h6>
                      <span class="text-success small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1" style="font-size:13px;" > <?php echo $nomedestino ?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- END DESTINO CARD-->


            <!-- ALTERNATIVO CARD-->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                

                <div class="card-body">
                 <h5 class="card-title"> <span>Alternativo</span> <!--<?php echo $timeBChoras ?>--></h5>

                  <div class="d-flex align-items-center">
                    
                    <div class="ps-3">
                      <h6><?php echo strtoupper($_GET['alternativo']); ?><span style="font-size:13px;">(<?php echo $elevation3; ?> ft)</span><a href="https://www.opennavcharts.com.br/app/search?icao=<?php echo $_GET['alternativo']; ?>&procedureType=TAXI" target="_blank"  class="button-carta"><i class="bi bi-map"></i> Cartas</a> </h6>
                      <span class="text-success small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1" style="font-size:13px;"> <?php echo $nomealternativo ?></span>
                      
                    </div>
                  </div>

                </div>
              </div>

            </div><!-- END ALTERNATIVO CARD -->

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
           <!-- NED AERONAVE CARD-->

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
            </div><!--NIVEL DE VOO CARD -->

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
            </div><!-- END DATA DO VOO CARD-->

           
            <script>
        function copiarTexto() {
            var texto = '<?php echo $rotasugerida ?>';
            var input = document.getElementById("rotainserida");

            input.value = texto;
        }
        </script>

            <!-- rota -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                  <div class="card-body">
                  <h5 class="card-title" style="text-align: center;"><i class="fa fa-map-o" aria-hidden="true" ></i> Planeje sua Rota<span></span></h5>
                   <div class= "text-center">
                

                   <iframe height="500px" width="95%" src="https://geoaisweb.decea.mil.br/#"></iframe>
                    

                        <hr>
                        <table class="table text-center">
                <thead>
                  <tr>
                    <th scope="col" class="text-center">Rota sugerida:<p></p></th>
                    <th scope="col" class="text-muted" ><?php echo $rotasugerida ?><br><?php verifica_rota($rotasugerida) ?></th>
                    <?php  if ($rotasugerida == $rota_nao_encontrada) {
                          echo "<th scope='col'></th>";
                    } else { ?>
                      <th scope="col">
                      <a href="https://skyvector.com/?ll=-15.860957599564097,-49.18029785623944&chart=302&zoom=11&fpl=%20<?php echo $_GET['origem']; ?>%20<?php echo $rotasugerida ?>%20<?php echo $_GET['destino']; ?>" class="button-skyvector" target="_blank"><img src="assets/img/skyvector1.png" style="height:15px;"></img></a>
                      <button onclick="copiarTexto()" class="button-route"><i class="fa fa-map-o" aria-hidden="true" ></i> <b>Utilizar</b></button>
                     </th>
                   <?php } ?>                                     
                  
                  </tr>
                </thead>
              </table>   

              <a href="https://skyvector.com/?ll=-15.860957599564097,-49.18029785623944&chart=302&zoom=11&fpl=%20<?php echo $_GET['origem']; ?>%20<?php echo empty($db_voos['rota']) ? "":  $db_voos['rota'] ?>%20<?php echo $_GET['destino']; ?>" class="button-skyvector" target="_blank"><img src="assets/img/skyvector1.png" style="height:30px;"></img></a>
               <p></p>
                            <form method="post" id="atualizarota">
                            <div class="input-group mb-3">                      
                      <input type="text" class="form-control" id="rotainserida" style="text-transform: uppercase; font-weight: bolder;text-align:center;" name="rota" placeholder="Insira aqui sua rota e salve   >>>" value="<?php echo empty($db_voos['rota']) ? "":  $db_voos['rota'] ?>">
                      <input name="idvoo" value="<?php echo $_GET['idvoo']; ?>" hidden>   
                            
                      <input type="button" class="btn btn-success" id="insererota" name="insererota" value="Salvar">                     

                           </div>
                         </form>                      
                         <div id="mensagemrota"></div>                                    				    
                         </div> 

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
            </div><!-- End rota -->
 
<!-- loadsheet and fuel-->
          
            <div class="col-12">
              <div class="card recent-sales overflow-auto">              
         
                <div class="card-body">
            
                
                <h5 class="card-title" style="text-align: center;">
    
    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M510.28 445.86l-73.03-292.13c-3.8-15.19-16.44-25.72-30.87-25.72h-60.25c3.57-10.05 5.88-20.72 5.88-32 0-53.02-42.98-96-96-96s-96 42.98-96 96c0 11.28 2.3 21.95 5.88 32h-60.25c-14.43 0-27.08 10.54-30.87 25.72L1.72 445.86C-6.61 479.17 16.38 512 48.03 512h415.95c31.64 0 54.63-32.83 46.3-66.14zM256 128c-17.64 0-32-14.36-32-32s14.36-32 32-32 32 14.36 32 32-14.36 32-32 32z"/></svg> LoadSheet <span id="unidade">(KG)</span>
 
    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
        <div style="font-size: 13px; font-weight: bold;">
            <label>Unidade:</label>
            <select id="unitSelector" onchange="showSelectedValue(); convertlbtokg();">
                <option value="KG" selected>KG</option>
                <option value="LB">LB</option>
            </select>
        </div>
        <div style="font-size: 13px; font-weight: bold;">
        <?php if ($aeronave == "C95M") { ?>
          <label>Temperatura (ºC):</label>
            <form method="post"> 
                <input type="text" class="form-control-sm" style="width:75px" name="temp_new" value="<?php echo $temp ?>">
                <button name="editatemp" class="btn btn-success btn-sm" style="margin-left:2px"><i class="bi bi-sun"></i></button>
            </form> <?php
  }
  ?>           
            
        </div>
    </div>
    
    <div style="text-align: right; font-weight: bold; margin-top: 10px;">
        <span style="font-size: 13px;">* Altitude do AD: <?php echo $elevation1 ?> ft <br>* O MTOW pode variar de acordo com a temperatura e altitude do aeródromo.</span>
    </div>
</h5>

           
                  <table class="table responsive-table text-center">
                <thead>
                  <tr>
                    <th scope="col" class="text-center"> Peso </th>
                    <th scope="col">Decolagem</th>
                    <th scope="col">Rampa</th>
                    <th scope="col">Pouso</th>
                    <th scope="col">Zero Comb</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row" class="text-center">MÁX</th> 
                    <td class = "table-active" id="lbtokg"><?php echo $mtow?> </td>
                    <td class = "table-active" id="lbtokg"><?php echo $mtw ?></td>
                    <td class = "table-active" id="lbtokg"><?php echo $mlw ?></td>
                    <td class = "table-active" id="lbtokg"><?php echo $mzfw ?></td>
                  </tr>
                  <tr>
                    <th scope="row" class="text-center">PREV</th>
                    <?php
                    echo $tow <= $mtow ? "<td class = 'table-success' id='lbtokg'> $tow </td>": "<td class = 'table-danger' id='lbtokg'> $tow </td>";
                    echo $tw <= $mtw ? "<td class = 'table-success' id='lbtokg'> $tw </td>": "<td class = 'table-danger' id='lbtokg'> $tw </td>";
                    echo $lw <= $mlw ? "<td class = 'table-success' id='lbtokg'> $lw </td>": "<td class = 'table-danger' id='lbtokg'> $lw </td>";
                    echo $zfw <= $mzfw ? "<td class = 'table-success' id='lbtokg'> $zfw </td>": "<td class = 'table-danger' id='lbtokg'> $zfw </td>";
                    ?>                                   
                  </tr>
       
                </tbody>
              </table>

              <div style="display: flex; justify-content: center;">
              <table class="table responsive-table text-center">
                <thead>
                  <tr>
                    <th scope="col text-center"> PBO</th>                    
                    <th scope="col">FUEL</th>
                    <th scope="col">PAYLOAD</th>                  
                    <th scope="col">DISP</th>
                  </tr>
                </thead>
               
                <tbody>
                  <tr>
                    <td class= "table-info text-center" id="lbtokg"><?php echo $pbo ?></td>                   
                    <?php 
if ((($unidade == "kg") ? (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuel ): (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuelKG)) <= $mfuel) {
    echo "<td class='table-success' id='lbtokg'>" . (($unidade == "kg") ? (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuel ): (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuelKG)) . "</td>";
} else {
    echo "<td class='table-danger' id='lbtokg'>" . (($unidade == "kg") ? (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuel ): (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuelKG)) . "</td>";
}
?>
     
                    <td class = "table-info" id="lbtokg"><?php echo empty($cargaprev)? "Não informado": $cargaprev ?></td>                   
                  
                    <?php 
                     if( $dispresult > 0 ) {
                      echo "<td class = 'table-success' id='lbtokg'> <strong> $dispresult </strong></td>";
                    }
                    else {
                     echo "<td class = 'table-danger' id='lbtokg'> <Strong>$dispresult</strong></td>";
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
            
            <!-- SCRIPT MODAL ALERTA LOADSHEET ERRO -->
            <?php
   
    if ((($unidade == "kg") ? (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuel ): (!empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuelKG)) > $mfuel || $tow > $mtow || $lw > $mlw || $dispresult < 0) {
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
    ?>
    

          

            <!-- REDEMET-->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
              <div class="card-body">
              <h5 class="card-title" style="text-align: center;"><i class="bi bi-cloud-sun"></i> Briefing meteorológico<span></span></h5>
             
              <iframe width="100%" height="450"
                            src="https://www.redemet.aer.mil.br/old/gera_pdf.php?acao=autoatendimento&localidades=<?php echo $_GET['origem']; ?>%2C<?php echo $_GET['destino']; ?>%2C<?php echo $_GET['alternativo']; ?>&nivelvoo=<?php echo $niveldevoo ?>&datahora=<?php echo "$datadovoobr"; ?>+<?php echo $_GET['horadep']; ?>&fir%5B%5D=SBAZ&fir%5B%5D=SBAO&fir%5B%5D=SBBS&fir%5B%5D=SBCW&fir%5B%5D=SBRE&fir_extra=&msg_met%5B%5D=metar&msg_met%5B%5D=taf&sigwx=sigwx&vento=vento&img_sat=img_sat"
                            frameborder="0"></iframe>
                            <hr> 
                            <!--<div class="text-center" >                          
                            <img src="assets/img/ogimet.jpg" style="widht:30;height:30px"></img><a  class="btn btn-warning" style="text-align: center;" href="https://www.ogimet.com/display_gramet.php?icao=<?php echo strtoupper($_GET['origem']); ?>_<?php echo strtoupper($_GET['destino']); ?>&hini=0&tref=&hfin=<?php echo $gramet_hour ?>&fl=<?php echo $niveldevoo ?>&enviar=Enviar" target="_blank">Gerar GRAMET</a>
                            </div>
                            <hr> -->

              </div>
            </div>
          </div>
          </div> <!-- End REDEMET -->

            
            <!-- WINDY -->
          <div class="col-12">
              <div class="card">

                  <div class="card-body">
                  <h5 class="card-title" style="text-align: center;"> Windy<span></span></h5>

                <iframe width="100%" height="450"
                            src="https://embed.windy.com/embed2.html?lat=-19.212&lon=-45.256&detailLat=-22.002&detailLon=-47.368&width=650&height=450&zoom=5&level=surface&overlay=clouds&product=ecmwf&menu=&message=&marker=&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=kt&metricTemp=%C2%B0C&radarRange=-1" frameborder="0"></iframe>

             </div>

              </div>
            </div><!-- End WINDY -->     


        </div><!-- End Left side columns -->

        <!-- <script src="https://api.checkwx.com/widget?key=dc54f84fdc0d4efda425a60932
" type="text/javascript"></script>

        <!-- Right side columns -->
        <div class="col-lg-4">

          <!-- METAR ORGN-->
          <div class="card">
            <!--<div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-cloud-fill"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>   </h6>
                </li>

               </ul>
            </div>-->
             
       

            <div class="card-body overflow-auto">
              <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['origem']; ?>"></div>             
              <div class="activity">              
             
              <a href="https://metar-taf.com/pt/<?php echo $_GET['origem']; ?>" id="metartaf-tqHtSN4U" style="font-size:18px; font-weight:500; color:#000; width:350px; height:265px; display:block">METAR Aeroporto Internacional do Recife/Guararapes-Gilberto Freyre</a>
               <script async defer crossorigin="anonymous" src="https://metar-taf.com/pt/embed-js/<?php echo $_GET['origem']; ?>?u=2660&layout=landscape&target=tqHtSN4U"></script>
               <br>
               <div class="checkwx-container" data-type="TAF" data-station="<?php echo $_GET['origem']; ?>"></div>
               

              </div>
            </div>            
          </div><!-- END METAR ORGN-->

          <!-- METAR DEST -->
          <div class="card">
          <!--<div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-cloud-fill"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6> <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['destino']; ?>"></div> </h6>
                </li>

               </ul>
            </div>-->
            <div class="card-body overflow-auto">
            <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['destino']; ?>"></div> 

              <div class="activity">              
              
              <a href="https://metar-taf.com/pt/<?php echo $_GET['destino']; ?>" id="metartaf-TXwWEOe1" style="font-size:18px; font-weight:500; color:#000; width:350px; height:265px; display:block">METAR Aeroporto de Madrid-Barajas</a>
<script async defer crossorigin="anonymous" src="https://metar-taf.com/pt/embed-js/<?php echo $_GET['destino']; ?>?u=2660&layout=landscape&target=TXwWEOe1"></script>
<br>
    <div class="checkwx-container" data-type="TAF" data-station="<?php echo $_GET['destino']; ?>"></div>

              </div>
            </div>            
          </div><!-- END METAR DEST-->

          <!-- METAR ALTN -->
          <div class="card">
            <!--<div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-cloud-fill"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>   </h6>
                </li>

               </ul>
            </div>-->           
       

            <div class="card-body overflow-auto">
              <div class="checkwx-container" data-type="METAR" data-station="<?php echo $_GET['alternativo']; ?>"></div>             
              <div class="activity">              
             
           
               <div class="checkwx-container" data-type="TAF" data-station="<?php echo $_GET['alternativo']; ?>"></div>
               

              </div>
            </div>            
          </div><!-- END METAR ALTN-->





          <!-- Website Traffic -->
          <div class="card">
          <div class="text-center">
          <img src="assets/img/aisweb.png" style="width:150px;height:80px;text-align:center" ></a></td>
          </div>  

          
					                  
              <h5 class="card-title" style="text-align: center;">ROTAER<span></span></h5>
              <table class="table text-center">             
              <tr> 
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#rotaerorigem">
<b><?php echo strtoupper($_GET['origem']); ?></b>
</button></th>
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#rotaerdestino">
<b><?php echo strtoupper($_GET['destino']); ?></b>
</button></th>
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#rotaeraltn">
<b><?php echo strtoupper($_GET['alternativo']); ?></b>
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
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert">  
      <?php echo getrotaer($_GET['origem']) ?>
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
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert">  
      <?php echo getrotaer($_GET['destino']) ?>
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
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert">  
      <?php echo getrotaer($_GET['alternativo']) ?>
                  </div>
      </div>
    </div>
  </div>
</div>

             <a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#notamorigem" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
    NOTAM <Strong><?php echo strtoupper($_GET['origem']) ?></strong>
  </a> 
  <br>

<div class="collapse" id="notamorigem">
<div class="alert alert-primary alert-dismissible fade show" role="alert">   <!-- alert -->  

<?php getnotam($_GET['origem']) ?>
</div>  <!-- end alert -->

  
</div>
         

<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#notamdest" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
    NOTAM <Strong><?php echo strtoupper($_GET['destino']) ?></strong>
  </a> 
  <br>

<div class="collapse" id="notamdest">
<div class="alert alert-primary alert-dismissible fade show" role="alert">   
<?php getnotam($_GET['destino']) ?>
</div>
</div>

<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#notamaltn" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
    NOTAM <Strong><?php echo strtoupper($_GET['alternativo']) ?></strong>
  </a> 
<br>
<div class="collapse" id="notamaltn">
<div class="alert alert-primary alert-dismissible fade show" role="alert">
<?php getnotam($_GET['alternativo']) ?>
</div> 
</div>

<hr>
<style>
  hr.border2px {
  border: 2px solid ;
} </style>
<a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#infotemp" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bx bx-world"></i>
    INFOTEMP 
  </a> 
<br>
<div class="collapse" id="infotemp">
<div class="alert alert-primary alert-dismissible fade show" role="alert">
<?php getinfotemp(strtoupper($_GET['origem'])) ?>
<hr class="border2px">
<?php getinfotemp(strtoupper($_GET['destino'])) ?>
<hr class="border2px">
<?php getinfotemp(strtoupper($_GET['alternativo'])) ?>
</div> 
</div>


             </div><!-- End NOTAM / ROTAER  -->

<!-- DONATION -->
             <div class="card">            
            <div class="card-body overflow-auto" style="text-align:center">                     
              <p class="card-title" style="font-size: 14px;"> Ajude o NavBrief a manter os seus servidores ativos a fim de prover o melhor planejamento para o seu voo.</p>
              <a href="donation.php" target="_blank"  class="btn btn-outline-success btn-sm"><b> Ajude o NavBrief! </b><i class="fa-brands fa-pix"></i></a>
                            
            </div>            
          </div><!-- END DONATION -->


          <!-- News & Updates Traffic -->
          
          
  
             

           
          
            <!--<div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">News &amp; Updates <span>| Today</span></h5>

              <div class="news">
                <div class="post-item clearfix">
                  <img src="assets/img/news-1.jpg" alt="">
                  <h4><a href="#">Nihil blanditiis at in nihil autem</a></h4>
                  <p>Sit recusandae non aspernatur laboriosam. Quia enim eligendi sed ut harum...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-2.jpg" alt="">
                  <h4><a href="#">Quidem autem et impedit</a></h4>
                  <p>Illo nemo neque maiores vitae officiis cum eum turos elan dries werona nande...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-3.jpg" alt="">
                  <h4><a href="#">Id quia et et ut maxime similique occaecati ut</a></h4>
                  <p>Fugiat voluptas vero eaque accusantium eos. Consequuntur sed ipsam et totam...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-4.jpg" alt="">
                  <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                  <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-5.jpg" alt="">
                  <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                  <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
                </div>

               

              </div>--><!-- End sidebar recent posts-->

            </div>
          </div><!-- End News & Updates -->

        </div><!-- End Right side columns -->

      </div>

    </section>

    
    
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'includes/footer.php'   ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/js/semrefresh.js" type="text/javascript"></script>
  <script src="assets/js/jquery-3.1.1.min.js" type="text/javascript"></script> 
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
$(window).on('load', function() {
    var $overlay = $('#overlay');
    $overlay.fadeOut(function() {
      $overlay.removeClass('loading');
    });
  });
    </script>
  

  <!--    PEGAR URL DA PAGINA , ENCURTAR E COPIAR -->
  <script>


        let shorturlecopiar = () => {
          var data = {
    "domain":"9u6s.short.gy",
    "originalURL": "<?php echo $linkvoo ?>" ,
    "allowDuplicates":false }; 
 fetch('https://api.short.cm/links/public', {
    method: 'post',
    headers: {
      'accept': 'application/json',
      'Content-Type': 'application/json',
      'authorization': 'pk_9m9eseL2ArAG1ikD'
    },
    body: JSON.stringify(data)
  }) .then(function(response) {
        return response.json();
    }) 
    .then(function(data){  
     
            //O texto que será copiado
            const texto = "Planejamento (<?php echo strtoupper($_GET['origem']); ?> - <?php echo strtoupper($_GET['destino']); ?>): " + data.shortURL;
            //Cria um elemento input (pode ser um textarea)
            let inputTest = document.createElement("input");
            inputTest.value = texto;
            //Anexa o elemento ao body
            document.body.appendChild(inputTest);
            //seleciona todo o texto do elemento
            inputTest.select();
            //executa o comando copy
            //aqui é feito o ato de copiar para a area de trabalho com base na seleção
            document.execCommand('copy');
            Swal.fire({ 
  icon: 'success',
  title: 'Link Copiado! Compartilhe.',
  showConfirmButton: false,
  timer: 2000
})
            //remove o elemento
            document.body.removeChild(inputTest);
          
          })};

          function convertlbtokg() {
    const unit = document.getElementById('unitSelector').value;
    const lbsToKg = 0.453592;
    const kgToLbs = 2.20462;

    const convertibles = document.querySelectorAll('[id="lbtokg"]');
    convertibles.forEach(cell => {
        const originalValue = parseFloat(cell.textContent);
        if (!isNaN(originalValue)) {
            const convertedValue = unit === 'LB' ? Math.round(originalValue * kgToLbs) : Math.round(originalValue * lbsToKg);
            cell.textContent = convertedValue;
        }
    });
}

    function showSelectedValue() {
        const selectedValue = document.getElementById('unitSelector').value;
        document.getElementById('unidade').textContent = `(${selectedValue})`;
    }
 </script>
    
</body>

</html>