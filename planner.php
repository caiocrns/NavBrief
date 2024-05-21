<?php include 'includes/header.php';
      include 'includes/sidebar.php';
      include 'lib/conn.php'; 
      include 'lib/config.php';  

?>

<?php 

      $aeronave_selec = $_POST['aeronaveselec'];

      $sql2 = " SELECT id FROM voos ORDER BY id DESC LIMIT 1";                     // BUSCAR voos BANCO DE DADOS
      $queryvoos = mysqli_query($conexao,$sql2);
      $last_id_voos = mysqli_fetch_assoc($queryvoos); 

      $sql3 = "SELECT DISTINCT operador,nome_aeronave FROM aeronaves WHERE icao_aeronave = '$aeronave_selec'";          // buscar anv banco dados
      $queryop= mysqli_query($conexao,$sql3);
      $lista_operador = mysqli_fetch_assoc($queryop);

       
    $conexao->close();
    
      ?>

      
 
 
  <main id="main" class="main">

    <div class="pagetitle">
      <h1></h1>
     
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-6">

      
          <div class="card">
            <div class="card-body ">
              <h5 class="card-title" style="text-align: center;" >Insira as informações do seu voo  </h5>

              <!-- Floating Labels Form -->
              <form class="row g-3" id="planner" class="text-transform: uppercase" method="GET" action="dashplanner.php" >

              <style>
  /* Custom styles for reducing font size and height */
  .form-control-sm {
    
    width: 170px;
  }
</style>
              
                  <div class="form-floating">
                  <input type="number" class="form-control" name="idvoo" value="<?php echo $last_id_voos ['id'] + 1 ?>" hidden >                   
                </div>                

                <div class="col-auto">
                  <div class="form-floating">
                    <input type="text" class="form-control form-control-sm" style="text-transform: uppercase" name="origem" placeholder="Origem" maxlength="4" required> 
                    <label for="origem">Origem</label>
                  </div>
                </div>  
                <div class="col-auto">
                  <div class="form-floating">
                    <input type="text" class="form-control form-control-sm" style="text-transform: uppercase" name="destino" placeholder="Destino" maxlength="4" required>
                    <label for="floatingEmail">Destino</label>
                  </div>
                </div>
                <div class="col-auto">
                  <div class="form-floating">
                    <input type="text" class="form-control form-control-sm" style="text-transform: uppercase" name="alternativo" placeholder="Alternativo" maxlength="4" required>
                    <label for="floatingEmail">Alternativo</label>
                  </div>
                </div>             
                <div class="col-auto">
                  <div class="form-floating">
                  <input type="number" class="form-control form-control-sm" name="niveldevoo" placeholder="Nivel de voo (Opcional)">
                    <label for="floatingPassword">Nível de voo <span class="text-muted" style="font-size:10px;"><i class="fa fa-info-circle" aria-hidden="true"></i> </span></label>
                  </div>
                </div>            

                <div class="col-auto">                  
                    <div class="form-floating">
                      <input type="date" class="form-control" name="datadovoo" placeholder="Data do voo" required>
                      <label for="inputDate">Data do voo</label>
                    
                  </div>
                </div>

                  <div class="col-auto">
                  <div class="form-floating">
                    <input type="time" class="form-control" name="horadep" placeholder="Horário" required>
                    <label for="inputTime">DEP (UTC)</label>
                  </div>
                </div>

                <div class="col-auto">
                  <div class="form-floating">
                  <input type="text" class="form-control form-control-sm" name = "icao_aeronave" value="<?php echo  $aeronave_selec?>" disabled="">
                    <label for="floatingPassword">Aeronave</label>
                  </div>
                </div>    
                
                <div class="col-auto" >
                  <div class="form-floating mb-3" >
                    <select class="form-select" name="operador" id="floatingSelect" aria-label="State" required>
                      
                    <option> Selecione</option>
                    <?php foreach($queryop as $operadores) { ?>
                    <option value="<?php echo $operadores['operador'];?>"><?php echo $operadores['operador'];?> </option>
                  <?php } ?>                   
                    </select>
                    <label for="floatingSelect">Operador <span class="text-muted" style="font-size:12px;"><i class="fa fa-info-circle" aria-hidden="true"></i></span> </label>
                  </div>
                </div>

                <div class="col-auto" >
                  <div class="form-floating mb-3" >
                    <select class="form-select" name="aeronave" id="matriculasDropdown" aria-label="State" required>
                      
                    <option>Selecione</option>
                     <!-- Atualiza_operador.php -->               
                    </select>
                    <label for="floatingSelect">Matrícula</label>
                  </div>
                </div>


              

                <p class="card-title"><span> <i class="fa fa-info-circle" aria-hidden="true"></i> Para gerar um nível de voo sugerido, deixe o respectivo campo em branco. <br> <i class="fa fa-info-circle" aria-hidden="true"></i>  Selecione um operador para obter as matrículas disponíveis.</span></p>
               
                <hr>
              
                <div class="text-center">
                                 
  <a  style="text-align: center;" class="btn btn-sm btn-primary"  data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Inserir Payload 
  </a>
                    </div>


<div class="collapse" id="collapseExample">
<!--<div class="form-floating">
                  <input type="number" class="form-control"  name="pesotrip" value="300" >
                    <label for="floatingPassword">Peso tripulação</label>
                  </div>
                  <p></p>-->
                  <div class="form-floating">
                  <input type="number" class="form-control" name="cargaprev" >
                    <label for="floatingPassword">Payload (Carga + Pax)</label>
                  </div>
                  <p></p>
                  

<!-- OUTRO CONTEUDO DE DESPACHO -->

                  
                  </div>

                </div>
                </div>
              
  
<!-- End dados loadsheet -->


                <div class="text-center">
                
                <a href="home.php"><button type="button" class="btn btn-warning">Voltar</button></a>
                <button type="reset" class="btn btn-secondary">Resetar</button>
                <button type="submit" name="submit" id="submit" class="btn btn-success">Planejar</button>                 
                  
                  
                </div> 
              </form><!-- End floating Labels Form -->  

            </div>
          </div>

        </div>
      </div>
    </section>

    <script>
    var condicao = "<?php echo $aeronave_selec ?>"; // Variável externa para armazenar a condição

    
  
</script>


<script>
  // Get references to the "Operador" and "Matrículas" dropdowns
  const operadorDropdown = document.querySelector('[name="operador"]');  
  const matriculasDropdown = document.getElementById('matriculasDropdown');

  // Add event listener to the "Operador" dropdown
  operadorDropdown.addEventListener('change', function () {
    // Get the selected "Operador" value
    const selectedOperador = operadorDropdown.value;
    const selectedAeronave = "<?php echo $aeronave_selec?>";

    // Make an AJAX request to the server to fetch the corresponding "Matrículas" for the selected "Operador"
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Clear the current options in the "Matrículas" dropdown
          matriculasDropdown.innerHTML = '<option> Selecione</option>';

          // Parse the JSON response
          const data = JSON.parse(xhr.responseText);

          // Add new options based on the fetched data
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent =  item.matricula;
            matriculasDropdown.appendChild(option);
          });
        } else {
          console.error('Erro ao selecionar as matrículas:', xhr.status, xhr.statusText);
        }
      }
    };
   
    xhr.open('GET', 'atualiza_operador.php?icao_aeronave=' + encodeURIComponent(selectedAeronave) + '&operador=' + encodeURIComponent(selectedOperador));
    xhr.send();
  });
</script>
  
 
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'includes/footer.php'   ?>

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