<?php
 include 'lib/function.php' ;
 include 'lib/conn.php'   ;
 include 'lib/config.php'   ;
 require_once('lib/TCPDF/tcpdf.php');
 
 header('Content-Type: application/pdf');
 header('Content-Disposition: attachment;');
 header('Cache-Control: no-cache, no-store, must-revalidate');
 header('Pragma: no-cache');
 header('Expires: 0');

ob_start();

$dep = strtoupper($_POST['dep']); 
$arr = strtoupper($_POST['arr']); 
$altn = strtoupper($_POST['altn']); 
$fl = $_POST['fl']; 
$metardep = $_POST['metardep'];
$metararr = $_POST['metararr'];
$metaraltn = $_POST['metaraltn'];
$notamdep = $_POST['notamdep'];
$notamarr = $_POST['notamarr'];
$notamaltn = $_POST['notamaltn'];

// Lista de cartas de vento disponíveis
$cartasDeVento = [050,100,180,240,300,340,390,450,630];
// Inicializa variáveis para armazenar a menor diferença e a carta correspondente
$menorDiferenca = null;
$cartaEscolhida = null;

// Itera sobre as cartas disponíveis
foreach ($cartasDeVento as $nivel) {
    $diferenca = abs($fl - (int)$nivel); // Calcula a diferença absoluta
    if ($menorDiferenca === null || $diferenca < $menorDiferenca) {
        $menorDiferenca = $diferenca;
        $nivelEscolhido = $nivel;
    }
}	

$sigwxImageUrl = getsigwx();
$satelite = "assets/img/satelite2.png";
$sigwx_sup = "https://aviationweather.gov/data/products/fax/F24_sigwx_hi_a.gif";
$winds_aloft = "https://aviationweather.gov/data/products/fax/F12_wind_{$nivelEscolhido}_a.gif";
$response_satelite = getsatelite("realcada");
$data_satelite = json_decode($response_satelite, true);

// --------------------------------	
function processRotaer($icaoCode) { 
  global $apiKey_aisweb;
  global $apiPass_aisweb;
  $area = 'rotaer';      
  $url = "http://aisweb.decea.gov.br/api/?apiKey={$apiKey_aisweb}&apiPass={$apiPass_aisweb}&area={$area}&icaoCode={$icaoCode}";

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

  $response = curl_exec($curl);

  if (curl_errno($curl)) {
      curl_close($curl);
      throw new Exception('Erro na conexão com a API ROTAER: ' . curl_error($curl));
  }

  curl_close($curl);

  if (!$response) {
      throw new Exception('A resposta da API ROTAER está vazia.');
  }

  // Carrega o XML retornado
  $xml = new SimpleXMLElement($response);

  // Extrai os remarks
$remarks = [];
$letter = 'A'; // Define a letra inicial

foreach ($xml->rmk->rmkText as $remark) {
    $remarks[] = $letter . ' - ' . (string)$remark; // Adiciona a letra antes do texto do remark
    $letter++; // Incrementa a letra para a próxima linha
}

return [
    'icaoCode' => $icaoCode,
    'remarks' => $remarks,
];

}

     



function quebrarPagina($pdf, $alturaMaxima) {
    if ($pdf->GetY() >= $alturaMaxima) {
        $pdf->AddPage();
    }
}



// Configuração de codificação interna
setlocale(LC_ALL, 'pt_BR.UTF-8');
mb_internal_encoding('UTF-8');

// Criação de um novo documento PDF com codificação UTF-8
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Definir informações do documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('NavBrief By STO');
$pdf->SetTitle('Briefing ' . utf8_encode($dep) . '-' . utf8_encode($arr));
$pdf->SetSubject('Plano de Voo');
$pdf->SetKeywords('NavBrief, plano de voo, briefing');

// Configuração do cabeçalho padrão
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Configuração das fontes do cabeçalho e rodapé
$pdf->setHeaderFont(['helvetica', '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont(['helvetica', '', PDF_FONT_SIZE_DATA]);

// Definir fonte monoespaçada padrão
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Configuração de margens
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Ativar quebras de página automáticas
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// Definir fator de escala de imagem
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Definir fonte padrão compatível com UTF-8
$pdf->SetFont('helvetica', '', 10);

// Adicionar uma nova página ao documento
$pdf->AddPage();






/* NOTE:
 * *********************************************************
 * You can load external XHTML using :
 *
 * $html = file_get_contents('/path/to/your/file.html');
 *
 * External CSS files will be automatically loaded.
 * Sometimes you need to fix the path of the external CSS.
 * *********************************************************
 */

// define some HTML content with style
$html = <<<EOF

<body> 
<style>
  table {
    margin: 0 auto;
    border-collapse: collapse;
    width: 100%;
  }
  th {
    font-family: Courier, monospace;
    font-size: 12px;
    text-align: center;
    vertical-align: middle;
    padding: 5px;
    background-color:rgb(84, 125, 158);
    font-weight: bold;
    
  }
    td {
    font-family: Courier, monospace;
    font-size: 12px;
    text-align: center;
    vertical-align: middle;
    padding: 5px;
}
  
  .section-title {
    font-family: Courier, monospace;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    margin-top: 10px;
    margin-bottom: 5px;
  }
</style>

<div>
  <!-- Section Title -->
  <div class="section-title">INFORMAÇÕES</div>

  <!-- Airport Information -->
  <table border="1">
    <tr>
      <th width="33%">ORIGEM</th>
      <th width="33%">DESTINO</th>
      <th width="33%">ALTERNATIVO</th>      
    </tr>
    <tr>
      <td>$dep</td>
      <td>$arr</td>
      <td>$altn</td>
      
    </tr>
  </table>

 

<!-- METAR e TAF Section -->
  <div class="section-title">INFORMAÇÕES METEOROLÓGICAS</div>
  <table border="1">
    <tr>
      <th width="33%">ORIGEM</th> 
      <th width="33%">DESTINO</th> 
      <th width="33%">ALTERNATIVO</th>      
    </tr>
    <tr>
      <td>$metardep </td> 
      <td>$metararr</td>  
      <td>$metaraltn </td>       
    </tr>
  </table>
  </div>

<!-- NOTAM Section -->
<div class="section-title">NOTICE TO AIRMEN (NOTAM)</div>
<table border="1" style="width: 100%;">
    <tr>
        <th>ORIGEM ($dep)</th>
    </tr>
    <tr>
        <td style="text-align: left; text-align: justify;">$notamdep</td>
    </tr>
</table>
<p></p>

  <table border="1">
    <tr>
      <th width="100%">DESTINO ($arr)</th>      
    </tr>
    <tr>
      <td style="text-align: left; text-align: justify;">$notamarr </td>      
    </tr>
  </table>
<p></p>
  <table border="1">
    <tr>
      <th width="100%">ALTERNATIVO ($altn)</th>      
    </tr>
    <tr>
      <td style="text-align: left; text-align: justify;">$notamaltn </td>      
    </tr>
  </table>
</div>



</body>


EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -ROTAER ----------
// Função para adicionar as páginas ao PDF existente
function addRotaerToPdf($pdf, $rotaers) {
  foreach ($rotaers as $rotaer) {
      // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ROTAER ----------
      $pdf->AddPage();
      $pdf->SetFont('helvetica', '', 10);

      // Título
      $pdf->SetFont('helvetica', 'B', 12);
      $pdf->Cell(0, 10, "Remarks do ROTAER - {$rotaer['icaoCode']}", 0, 1, 'C');
      $pdf->Ln(10);

      // Remarks
      $pdf->SetFont('helvetica', 'B', 12);
      $pdf->Cell(0, 10, 'Remarks:', 0, 1, 'L');
      $pdf->SetFont('helvetica', '', 10);
      foreach ($rotaer['remarks'] as $remark) {
          $pdf->MultiCell(0, 5, strip_tags($remark), 0, 'L', false);
          $pdf->Ln(2);
      }
  }
}

try {
  // Defina os códigos ICAO de origem, destino e alternativo
  $icaoCodes = [
      'Origem' => $dep, // São Paulo/Guarulhos
      'Destino' => $arr, // Rio de Janeiro/Santos Dumont
      'Alternativo' => $altn, // São Paulo/Congonhas
  ];

  // Processa os dados para cada local
  $rotaers = [];
  foreach ($icaoCodes as $tipo => $icaoCode) {
      $rotaers[] = processRotaer($icaoCode);
  }

  // Adicione as páginas ao PDF existente
  // Presume-se que a variável $pdf já foi inicializada no seu sistema
  addRotaerToPdf($pdf, $rotaers);
} catch (Exception $e) {
  echo 'Erro: ' . $e->getMessage();
}
//---------------------------------------------------------------------------------ROTAER*-------------
// add a page
$pdf->AddPage();

// Inserir a carta SIGWX
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Carta SIGWX (SFC-FL250)', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Image($sigwxImageUrl, 15, 40, 180, 180, 'PNG', '', 'C', true, 300, '', false, false, 0, false, false, false);

// Inserir a carta SIGWX SUP
if($fl > 230) {
  $pdf->AddPage();
  $pdf->SetFont('helvetica', '', 12);
  $pdf->Cell(0, 10, 'Carta SIGWX (FL250-FL600)', 0, 1, 'C');
  $pdf->Ln(10);
  $pdf->Image($sigwx_sup, 15, 40, 180, 180, 'PNG', '', 'C', true, 300, '', false, false, 0, false, false, false);
}


// Inserir a carta WINDS ALOFT
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Carta de Vento', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Image($winds_aloft, 15, 40, 180, 180, 'GIF', '', 'C', true, 300, '', false, false, 0, false, false, false);

//IMAGEM SATELITE 
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
if (!isset($data_satelite['error'])) {
    // Adiciona título e espaço no PDF
    $pdf->Cell(0, 10, 'Imagem de Satélite', 0, 1, 'C');
    $pdf->Ln(10);
    // Recupera o caminho da imagem e imprime no PDF    
    $pdf->Image($satelite, 15, 40, 180, 180, 'PNG', '', 'C', true, 300, '', false, false, 0, false, false, false);
    $pdf->Image($data_satelite['path'], 15, 40, 180, 180, 'PNG', '', 'C', true, 300, '', false, false, 0, false, false, false);
    // Adiciona informações extras ao PDF, como coordenadas e timestamp
    $pdf->Ln(200);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(0, 10, 'Última Atualização (UTC): ' . $data_satelite['timestamp'], 0, 1, 'L');    
} else {
    // Exibe mensagem de erro no PDF se os dados não estiverem disponíveis
    $pdf->Cell(0, 10, 'Imagem de Satélite não disponível', 0, 1, 'C');
}


$html = '   
     
';




// output the HTML content
$pdf->writeHTML($html, true, false, true, false, ''); 

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
while( ob_get_level() ) {
    ob_end_clean();
}
$pdf->Output('Briefing ('.$dep.'-'.$arr.'-'.$altn.').pdf', 'D');
ob_end_flush(); 

//============================================================+
// END OF FILE
//============================================================+
?>