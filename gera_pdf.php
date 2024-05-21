<link href="assets/css/style.css" rel="stylesheet">
<?php
 include 'lib/function.php' ;
 include 'lib/conn.php'   ;
 include 'lib/config.php'   ;
 require_once('lib/TCPDF/tcpdf.php');
 
 

ob_start();

	$origem = $_POST['origem'];  
	$destino =$_POST['destino'];
	$alternativo =$_POST['alternativo'];
	$nomeorigem=$_POST['nomeorigem'];
	$nomedestino=$_POST['nomedestino'];
	$niveldevoo = $_POST['niveldevoo'];
	$nomealternativo=$_POST['nomealternativo'];
	$iataorigem =$_POST['iataorigem'];
	$iatadestino =$_POST['iatadestino'];
	$iataalternativo =$_POST['iataalternativo'];
	$etahoras = $_POST['etahoras'];  
	$horadep =$_POST['horadep'];
	$datadovoobr =$_POST['datadovoobr'];
	$matricula = $_POST['matricula'];  
	$aeronave =$_POST['aeronave'];
	$modelo =$_POST['modelo'];
	$metarorigem =$_POST['metarorigem'];
	$metardestino =$_POST['metardestino'];
	$metaralternativo =$_POST['metaralternativo'];
	$notamorigem =$_POST['notamorigem'];
	$notamdestino=$_POST['notamdestino'];
	$notamalternativo =$_POST['notamalternativo'];	
	$mzfw =$_POST['mzfw'];
	$mtw =$_POST['mtw'];
	$mtow =$_POST['mtow'];
	$mlw =$_POST['mlw'];
	$zfw =$_POST['zfw'];
	$tw =$_POST['tw'];
	$tow =$_POST['tow'];
	$lw =$_POST['lw'];
	$consumo = $_POST['consumo'];
	$fuelAB  =$_POST['fuelAB'];
	$fuelBC =$_POST['fuelBC'];
	$fuelextra =$_POST['fuelextra'];
	$autonomiahoras =$_POST['autonomiahoras'];
	$autonomiafuel= $_POST['autonomiafuel'];
	$timeABhoras = mintohourspdf($_POST['timeABmin']);
	$timeBChoras= mintohourspdf($_POST['timeBCmin']);
	$timeABeet= mintohours($_POST['timeABmin']);
	$autonomiahoras= mintohourspdf($_POST['autonomiahoras']);
	$dispresult = $_POST['dispresult'];		
	$pbo = $_POST['pbo'];
	$autonomiafuelKG = $_POST['autonomiafuelKG'];	
	$cargaprev = $_POST['cargaprev'];	
	$time_extra = time_extra;
	$consumo_por_min = $consumo/60;
	$fuelproc = $consumo_por_min * time_proc;
	$timeproc = "000".time_proc;
    $idvoo = $_POST['idvoo'];
	
$selectrota = mysqli_query($conexao, "SELECT rota from voos WHERE id='$idvoo'") or die(mysqli_error($conexao));
$db_voos = mysqli_fetch_assoc($selectrota);
$rota = empty($db_voos['rota']) ? "Não informada" : strtoupper($db_voos['rota']);
	

	
	/* CARTA SIGWX REDEMET API 
	 
$url = 'https://api-redemet.decea.mil.br/produtos/sigwx?api_key=MwpGmMvXuFe0AIl8gA3FbIOBpG75wiN2w0haSvso';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$cartasigwx = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Erro na requisição: ' . curl_error($ch);
} 
curl_close($ch); */
// --------------------------------	

require_once('lib/TCPDF/tcpdf.php');

function quebrarPagina($pdf, $alturaMaxima) {
    if ($pdf->GetY() >= $alturaMaxima) {
        $pdf->AddPage();
    }
}


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Flight Desk By STO');
$pdf->SetTitle('Briefing '.$origem.'-'.$destino.'');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE , PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
$pdf->AddFont('Helvetica', '', 'lib/TCPDF/fonts/Helvetica.ttf', true);


// Use o nome da fonte retornado pelo método addTTFfont() ao definir a fonte atual.
$pdf->SetFont('Helvetica', '', 10);


// add a page
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
    margin-left: auto;
    margin-right: auto;
}
</style>

<table class="TableNormal" style="border-collapse:collapse" width="585">
	<tbody>
	
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.15pt">DATA DO VOO:</span></span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="margin-left:14px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.15pt">$datadovoobr</span></span></span></p>
			</td>
			<td colspan="4" style="width:212px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph" style="margin-left:23px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.15pt">&nbsp; </span></span></span></p>
			</td>
			<td colspan="2" style="width:77px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.15pt">ETD:</span></span></span></p>
			</td>
			<td style="width:83px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.15pt"> $horadep Z</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph" style="margin-left:3px">&nbsp;</p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center">&nbsp;</p>
			</td>
			<td colspan="4" style="width:212px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph" style="margin-left:23px">&nbsp;</p>
			</td>
			<td colspan="2" style="width:77px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:10.35pt">EET: </span></span></span></p>
			</td>
			<td style="width:83px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:10.35pt">$timeABeet</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:10.35pt">AERONAVE:</span></span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p align="center" class="TableParagraph" style="margin-left:5px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:10.35pt">$aeronave</span></span></span></p>
			</td>
			<td colspan="4" style="width:212px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph" style="margin-left:126px">&nbsp;</p>
			</td>
			<td colspan="2" style="width:77px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:10.35pt">ETA:</span></span></span></p>
			</td>
			<td style="width:83px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:10.35pt">$etahoras Z</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">MATR&Iacute;CULA:</span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$matricula</span></span></p>
			</td>
			<td colspan="4" style="width:212px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph" style="margin-left:23px">&nbsp;</p>
			</td>
			<td colspan="2" style="width:77px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p class="TableParagraph" style="margin-left:4px">&nbsp;</p>
			</td>
			<td style="width:83px; padding:0cm 0cm 0cm 0cm; height:15px" valign="top">
			<p align="center" class="TableParagraph" style="margin-left:51px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:14px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">MODELO:</span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:14px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$modelo</span></span></p>
			</td>
			<td colspan="4" style="width:212px; padding:0cm 0cm 0cm 0cm; height:14px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td colspan="2" style="width:77px; padding:0cm 0cm 0cm 0cm; height:14px" valign="top">
			<p class="TableParagraph" style="margin-left:4px">&nbsp;</p>
			</td>
			<td style="width:83px; padding:0cm 0cm 0cm 0cm; height:14px" valign="top">
			<p align="center" class="TableParagraph" style="margin-left:14px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center">&nbsp;</p>
			</td>
			<td style="width:42px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:65px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:47px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:58px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:76px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.25pt">        </span></span></span></p>
			</td>
			<td colspan="2" style="width:84px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:3px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.25pt">           </span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">ORIGEM:</span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$origem/$iataorigem</span></span></p>
			</td>
			<td colspan="7" style="width:372px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph" style="margin-right:3px; margin-left:3px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.25pt">&nbsp;($nomeorigem)</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">DESTINO:</span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$destino/$iatadestino</span></span></p>
			</td>
			<td colspan="7" style="width:372px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph" style="margin-right:3px; margin-left:3px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.25pt">&nbsp;($nomedestino)</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">ALTERNATIVO:</span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$alternativo/$iataalternativo</span></span></p>
			</td>
			<td colspan="7" style="width:372px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph" style="margin-right:3px; margin-left:3px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span style="line-height:11.25pt">&nbsp;($nomealternativo)</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center">&nbsp;</p>
			</td>
			<td style="width:42px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:65px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:47px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:58px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="width:76px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph" style="margin-left:4px">&nbsp;</p>
			</td>
			<td colspan="2" style="width:84px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:3px; margin-left:3px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="width:122px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p class="TableParagraph"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">N&Iacute;VEL DE VOO:</span></span></p>
			</td>
			<td style="width:91px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">FL $niveldevoo</span></span></p>
			</td>
			
			<td colspan="2" style="width:84px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:3px; margin-left:3px; text-align:center">&nbsp;</p>
			</td>
		</tr>
	
		<tr>
		
			<td colspan="2" style="width:84px; padding:0cm 0cm 0cm 0cm; height:16px" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:3px; margin-left:3px; text-align:center">&nbsp;</p>
			</td>
		</tr>
	</tbody>
</table>

<hr>
<p></p>       <!-- ROTA -->

<table  cellspacing="0" class="Table" style="border-collapse:collapse; border:none; margin: 0 auto;">
	<tbody>
		<tr>
			<td style="border-bottom:none; border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; height:24px; vertical-align:top; width:501px">
			<p style="margin-left:-74px; margin-right:-81px; text-align:center"><span style="font-size:11px"><span style="font-family:Courier New,Courier,monospace">ROTA:</span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; height:54px; vertical-align:top; width:501px">
			<p style="text-align:center"><span style="font-size:11px"><span style="font-family:Courier New,Courier,monospace">$rota&nbsp;</span></span></p>
			</td>
		</tr>
	</tbody>
</table>

<p></p>
<!-- COMBUSTIVEL -->



<table style="border-collapse:collapse;border:none; " >
	<tbody>
		<tr>
			<td colspan="5" style="border-bottom:none; width:365px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:1px solid black; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">COMBUST&Iacute;VEL</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">ETAPA</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">ARPT</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">FUEL</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">TIME</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">DEST + PROC</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">$iatadestino</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">$fuelAB</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">$timeABhoras</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">ALTN </span></span></span></p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">$iataalternativo</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">$fuelBC</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">$timeBChoras</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">EXTRA </span></span></span></p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US"> $fuelextra</span></span></span></p>
			</td>
			<td style="border-bottom:none; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">00$time_extra</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">       </span></span></span></p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">     </span></span></span></p>
			</td>
			<td style="border-bottom:none; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">     </span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-left: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b><span lang="EN-US">ABASTECIDO</span></b></span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:1px solid black; width:61px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 9px; text-align: center;">&nbsp;</p>
			</td>
			<td style="border-bottom:1px solid black; width:59px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:none; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; margin-right: 3px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b><span lang="EN-US">$autonomiafuel</span></b></span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:62px; padding:0cm 0cm 0cm 0cm; height:29px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p class="TableParagraph" style="margin-top: 6px; text-align: center;"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b><span lang="EN-US">$autonomiahoras</span></b></span></span></p>
			</td>
		</tr>
	</tbody>
</table>

<p></p>

<!-- LOADSHEET -->
<center>
<table align="left" class="TableNormal" style="border-collapse:collapse; border:none; margin-left:6px; margin-right:6px" width="709">
	<tbody>
		<tr>
			<td colspan="7" style="border-bottom:none; width:709px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:1px solid black; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">LOADSHEET</span></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">PESO (KG)</span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">DEP</span></span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">RAMPA</span></span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:94px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">POUSO</span></span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:98px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">ZFW</span></span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:81px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">MÁX</span></span></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$mtow</span></span></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$mtw</span></span></p>
			</td>
			<td style="border-bottom:none; width:94px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$mlw</span></span></p>
			</td>
			<td style="border-bottom:none; width:98px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$mzfw</span></span></p>
			</td>
			<td style="border-bottom:none; width:81px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">PREV</span></span></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$tow</span></span></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$tw</span></span></p>
			</td>
			<td style="border-bottom:none; width:94px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$lw</span></span></p>
			</td>
			<td style="border-bottom:none; width:98px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$zfw</span></span></p>
			</td>
			<td style="border-bottom:none; width:81px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:94px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:98px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
			<td style="border-bottom:none; width:81px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:none; width:123px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"></span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">PBO</span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">FUEL</span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">PAYLOAD</span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:94px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">DISP</span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:98px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"></span></span></strong></u></p>
			</td>
			<td style="border-bottom:none; width:81px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"></span></span></strong></u></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">&nbsp; </span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$pbo</span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$autonomiafuel</span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:104px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$cargaprev</span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:94px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">$dispresult</span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:98px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:none; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"></span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:81px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"></span></span></p>
			</td>
		</tr>
	</tbody>
</table>
</center>




</body>


EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// add a page
$pdf->AddPage();

$html = '   <!-- METAR E TAF -->

<table align="left" class="TableNormal" style="border-collapse:collapse; border:none; margin-left:6px; margin-right:6px" width="710">
	<tbody>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:710px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:1px solid black; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">BRIEFING METEOROL&Oacute;GICO</span></span></span></strong></u></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:710px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-right:9px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b> '.$origem.' ( '.$nomeorigem.')</b></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:53px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"> - </span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:587px; padding:0cm 0cm 0cm 0cm; height:53px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">'.$metarorigem.'</span></span></p>
			</td>
		</tr>
	
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:710px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">&nbsp;</span></span></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:710px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; text-align:justify"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b> '.$destino.' ( '.$nomedestino.')</b></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"> - </span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:587px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">'.$metardestino.'</span></span></p>
			</td>
		</tr>
	
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:710px; padding:0cm 0cm 0cm 0cm; height:26px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:710px; padding:0cm 0cm 0cm 0cm; height:26px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-right:9px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b>'.$alternativo.' ( '.$nomealternativo.')</b></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"> - </span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:587px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">'.$metaralternativo.'</span></span></p>
			</td>
		</tr>
		
	</tbody>
</table>
<p></p>
<hr>


<table align="left" class="TableNormal" style="border-collapse:collapse; border:none; margin-left:6px; margin-right:6px" width="709">
	<tbody>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:709px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:1px solid black; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-right:9px; text-align:center"><u><strong><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><span lang="EN-US">NOTAM </span></span></span></strong></u></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:709px; padding:0cm 0cm 0cm 0cm; height:24px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-right:9px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b>'.$origem.'/'.$iataorigem.'</b></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:53px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"> NOTAM </span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:586px; padding:0cm 0cm 0cm 0cm; height:53px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"> '.$notamorigem.'</span></span></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:709px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-left:3px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">&nbsp;</span></span></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:709px; padding:0cm 0cm 0cm 0cm; height:30px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; text-align:justify"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b>'.$destino.'/'.$iatadestino.'</b></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">NOTAM</span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:586px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">'.$notamdestino.'</span></span></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:709px; padding:0cm 0cm 0cm 0cm; height:26px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom:1px solid black; width:709px; padding:0cm 0cm 0cm 0cm; height:26px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p class="TableParagraph" style="margin-top:6px; margin-right:9px"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;"><b>'.$alternativo.'/'.$iataalternativo.'</b></span></span></p>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid black; width:123px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:1px solid black" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-left:3px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">NOTAM:</span></span></p>
			</td>
			<td style="border-bottom:1px solid black; width:586px; padding:0cm 0cm 0cm 0cm; height:60px; border-top:none; border-right:1px solid black; border-left:none" valign="top">
			<p align="center" class="TableParagraph" style="margin-top:6px; margin-right:9px; text-align:center"><span style="font-size:11px;"><span style="font-family:Courier New,Courier,monospace;">'.$notamalternativo.'</span></span></p>
			</td>
		</tr>
	</tbody>
</table>
<p></p>

    
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
$pdf->Output('OFP ('.$origem.'-'.$destino.')', 'I');
ob_end_flush(); 

//============================================================+
// END OF FILE
//============================================================+
?>