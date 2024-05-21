          <input name="origem" value="<?php echo strtoupper($_GET['origem'])?>" hidden > 
          <input name="destino" value="<?php echo strtoupper($_GET['destino'])?>" hidden > 
          <input name="alternativo" value="<?php echo strtoupper($_GET['alternativo'])?>" hidden >
          <input name="iataorigem" value="<?php echo $iataorigem ?>" hidden > 
          <input name="iatadestino" value="<?php echo $iatadestino ?>" hidden > 
          <input name="iataalternativo" value="<?php echo $iataalternativo ?>" hidden >                
          <input name="nomeorigem" value="<?php echo $nomeorigem ?>" hidden > 
          <input name="nomedestino" value="<?php echo $nomedestino ?>" hidden > 
          <input name="nomealternativo" value="<?php echo $nomealternativo ?>" hidden > 
          <input name="etahoras" value="<?php echo $etahoras?>" hidden > 
          <input name="horadep" value="<?php echo ($_GET['horadep']);  ?>" hidden > 
          <input name="datadovoobr" value="<?php echo "$datadovoobr"; ?>" hidden > 
          <input name="matricula" value="<?php echo $matricula ?> " hidden > 
          <input name="aeronave" value=" <?php echo $aeronave ?> " hidden > 
          <input name="modelo" value=" <?php echo $modelo ?> " hidden >       
          
          <input name="metarorigem" value=" <?php echo getmetaretaf($_GET['origem']); ?>  " hidden >
          <input name="metardestino" value=" <?php echo getmetaretaf($_GET['destino']); ?>  " hidden >
          <input name="metaralternativo" value=" <?php echo getmetaretaf($_GET['alternativo']); ?> " hidden >
          <input name="notamorigem" value=" <?php echo getnotampdf($_GET['origem']); ?>  " hidden >
          <input name="notamdestino" value=" <?php echo getnotampdf($_GET['destino']); ?>  " hidden >
          <input name="notamalternativo" value=" <?php echo getnotampdf($_GET['alternativo']); ?>  " hidden >       

          <input name="mtw" value=" <?php echo $mtw ?> " hidden >                    
          <input name="mzfw" value=" <?php echo $mzfw ?>  " hidden >
          <input name="mlw" value="<?php echo $mlw ?>  " hidden >
          <input name="mtow" value=" <?php echo $mtow ?>" hidden >    

          <input name="tw" value=" <?php echo $tw ?> " hidden >                    
          <input name="zfw" value=" <?php echo $zfw ?>  " hidden >
          <input name="lw" value="<?php echo $lw ?>  " hidden >
          <input name="tow" value=" <?php echo $tow ?>" hidden >  
          <input name="niveldevoo" value="<?php echo  $niveldevoo ?> " hidden >            
          <input name="consumo" value=" <?php echo $consumo ?> " hidden >              
                  
          <input name="pbo" value=" <?php echo $pbo ?>" hidden >  
          <input name="autonomiafuelKG" value="<?php echo $autonomiafuelKG ?> " hidden >
          <input name="dispresult" value="<?php echo $dispresult ?> " hidden >
          <input name="cargaprev" value="<?php echo empty($cargaprev)? "Não informado": $cargaprev ?> " hidden >
          <input name="autonomiahoras" value="<?php echo $autonomiamin ?> " hidden >
          <input name="autonomiafuel" value="<?php echo !empty($db_voos['fuel'])? $db_voos['fuel']: $autonomiafuel  ?> " hidden >        
          

          
          <input name="fuelAB" value=" <?php echo $fuelAB  ?> " hidden >                    
          <input name="fuelBC" value=" <?php echo $fuelBC  ?> " hidden >
          <input name="fuelextra" value="<?php echo $fuelextra  ?>  " hidden >

          <input name="timeABmin" value=" <?php echo $timeABmin   ?>" hidden > 
          <input name="timeBCmin" value=" <?php echo $timeBCmin  ?>" hidden >  
          <input name="idvoo" value="<?php echo $_GET['idvoo']; ?>" hidden>    