<?php include 'includes/header.php';
      include 'includes/sidebar.php';
      include 'lib/conn.php';
      include 'lib/config.php';
        

?>

<?php 
      include_once('lib/conn.php');   
      
     


      $sql = "SELECT * FROM voos";          // buscar voos banco dados
      $queryvoo= mysqli_query($conexao,$sql);
      $db_voos = mysqli_fetch_assoc($queryvoo);

      $sql = " SELECT id FROM voos ORDER BY id DESC LIMIT 1";                     // BUSCAR voos BANCO DE DADOS
      $queryvoos = mysqli_query($conexao,$sql);
      $last_id_voos = mysqli_fetch_assoc($queryvoos); 
       

      $url = '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
      $url = explode("=", $url);
      $id = $url[count($url) - 1]; 
      
      do { 

        if($id == $db_voos['id']) {
           $origem = $db_voos['origem'];
           $destino = $db_voos['destino'];
           $alternativo = $db_voos['alternativo'];
           $datadovoo = $db_voos['datadovoo'];
           $horadep = $db_voos ['horadep'];
           $niveldevoo = $db_voos['niveldevoo'];
           $idanv = $db_voos['aeronave'];
           $cargaprev = $db_voos['cargaprev'];                 
      
        }
         }
        while ($db_voos = mysqli_fetch_assoc($queryvoo)); 


       
        $sql = "SELECT id,icao_aeronave,matricula FROM aeronaves WHERE id = '$idanv'";       
        $result = $conexao->query($sql);
        $aeronavebyid = mysqli_fetch_assoc($result);

        $icao_anv_byid = $aeronavebyid['icao_aeronave'];
        
        
      $sql = "SELECT id,icao_aeronave,matricula FROM aeronaves WHERE icao_aeronave = '$icao_anv_byid'";          // buscar anv banco dados
      $queryanv = mysqli_query($conexao,$sql);
      $lista_aeronaves = mysqli_fetch_assoc($queryanv);

         ?>

  

  <main id="main" class="main">

    <div class="pagetitle">
      <h1></h1>
     
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-6">

      
          <div class="card">
            <div class="card-body">
              <h5 class="card-title" style="text-align: center;" >Edite as informações do seu voo</h5>

              <!-- Floating Labels Form -->
              <form class="row g-3"  id ="planner" class="text-transform: uppercase" method="GET" action="dashplanner.php">


              <div class="form-floating">
                  <input type="number" class="form-control" name="idvoo" value="<?php echo $last_id_voos ['id'] + 1 ?>" hidden >                   
                  </div>

                <div class="col-auto">
                  <div class="form-floating">
                    <input type="text" class="form-control" style="text-transform: uppercase" name="origem" placeholder="Origem" maxlength="4" value="<?php echo $origem?>" required> 
                    <label for="origem">Origem</label>
                  </div>
                </div>
  
                <div class="col-auto">
                  <div class="form-floating">
                    <input type="text" class="form-control" style="text-transform: uppercase" name="destino" placeholder="Destino" value="<?php echo $destino?>" required>
                    <label for="floatingEmail">Destino</label>
                  </div>
                </div>
                <div class="col-auto">
                  <div class="form-floating">
                    <input type="text" class="form-control" style="text-transform: uppercase" name="alternativo" placeholder="Alternativo" value="<?php echo $alternativo ?>" required>
                    <label for="floatingEmail">Alternativo</label>
                  </div>
                </div>
                <div class="col-auto">
                  <div class="form-floating">
                  <input type="number" class="form-control" name="niveldevoo" placeholder="Nivel de voo" value="<?php echo $niveldevoo?>" >
                    <label for="floatingPassword">Nível de voo</label>
                  </div>
                </div>            

                <div class="col-auto">                  
                    <div class="form-floating">
                      <input type="date" class="form-control" name="datadovoo" value="<?php echo $datadovoo?>" required>
                      <label for="inputDate">Data do voo</label>                    
                  </div>
                </div>

                <div class="col-auto">
                  <div class="form-floating mb-3">
                    <select class="form-select" name="aeronave" id="floatingSelect" aria-label="State" required>
                      
                    <option value = "<?php echo $aeronavebyid['id']; ?>"> <?php echo $aeronavebyid['icao_aeronave'];?> | <?php echo $aeronavebyid['matricula'];   ?> </option>
                    <?php do { ?>
                      
                      <option value="<?php echo $lista_aeronaves['id']; ?>"><?php echo $lista_aeronaves['icao_aeronave'];?> | <?php echo $lista_aeronaves['matricula'];   ?></option>
                      <?php } 
                      while ($lista_aeronaves = mysqli_fetch_assoc($queryanv)); ?>
                    </select>
                    <label for="floatingSelect">Matrícula</label>
                  </div>
                </div>
                <div class="col-auto">
                  <div class="form-floating">
                    <input type="time" class="form-control" name="horadep" placeholder="Horário" value="<?php echo $horadep ?>" required>
                    <label for="inputTime">DEP (UTC)</label>
                  </div>
                </div>

                <hr>
                <div class="text-center">
                                 
  <a  style="text-align: center;" class="btn btn-sm btn-primary"   data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Payload
  </a>
                    </div>


<div class="collapse" id="collapseExample">
<!--<div class="form-floating">
                  <input type="number" class="form-control"  name="pesotrip" value="<?php echo $pesotrip ?>" >
                    <label for="floatingPassword">Peso tripulação</label>
                  </div>
                  <p></p>-->
                  <div class="form-floating">
                  <input type="number" class="form-control" name="cargaprev" value="<?php echo $cargaprev ?>"  >
                    <label for="floatingPassword">Payload (Carga + Pax)</label>
                  </div>
                  <p></p>
                   <!-- SELECIONA LOADSHEET ATRAVES DA AERONAVE -->

<!-- OUTRO CONTEUDO DE DESPACHO -->

                  
                  </div>

                </div>
                </div>



                <div class="text-center">
                <button type="reset" class="btn btn-secondary">Resetar</button>
                <button type="submit" name="submit" class="btn btn-success">Planejar</button>                 
                  
                </div> 
              </form><!-- End floating Labels Form -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  
  


  
</script>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NavBrief by STO</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
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

</body>

</html>