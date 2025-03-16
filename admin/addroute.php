<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php' ?>

<?php 

 if(isset($_POST['submit']))
 {
include_once('../lib/conn.php');
  $origem = $_POST['origem'];  
  $rota = $_POST['rota'];
  $destino = $_POST['destino'];
  $tipo = $_POST['tipo'];
  $aerovia = $_POST['aerovia'];
  
  
  // Prepara a instrução SQL para inserção dos dados
  $sqladd = "INSERT INTO rotas(origem, rota, destino, espaco_aereo, aerovia) VALUES ('$origem', '$rota','$destino', '$tipo', '$aerovia')";
  $add = $conexao->query($sqladd);
 }
 
 ?>



  <main id="main" class="main">
  
    <div class="pagetitle">
      <h1>Adicionar rota</h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="homeadmin.php">Home</a></li>
          <li class="breadcrumb-item"><a href="rotas_db.php">Rotas</a></li>
          <li class="breadcrumb-item active">Adicionar</li>
        </ol>
      </nav>   
    </div> 
    <?php if (isset($add) && $add == true) { ?>
    <script>
       Swal.fire({ 
  icon: 'success',
  title: 'Rota adicionada!',
  showConfirmButton: false,
  timer: 1500
}).then(function() {               
                window.location.href = "rotas_db.php";
            });
        
    </script>
<?php } ?>

<!--  -->

    <div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body overflow-auto">
            <h5 class="card-title">Informações Rota</h5>

          <form class="row g-3" method="post" >
      <div class="col-4">
        <label class="form-label">Origem</label>
        <input type="text" class="form-control" name="origem">
      </div>

      <div class="col-4">
        <label class="form-label">Destino</label>
        <input type="text" class="form-control" name="destino">
      </div>
   <br>
     <Style>
     .input-rota {
  width:500px
} </style> 

<div class="col-4">
        <label  class="form-label">Tipo (Espaço Aéreo)</label>
        <select class="form-select" name="tipo"  id="floatingSelect" aria-label="State" required>
                      <option value = ""selected>  </option>
                      <option value="L"> L (Inferior)</option>
                      <option value="H"> H (Superior)</option>                   
                    </select>
</div>

      <div class="col-4">
        <label class="form-label">Rota</label>
        <input type="text" class="form-control input-rota" name="rota">
      </div>

      </div>
      <div class="col-4">
        <label  class="form-label">Possui Aerovia?</label>
        <select class="form-select" name="aerovia"  id="floatingSelect" aria-label="State" required>
                      <option value = ""selected>  </option>
                      <option value="1"> Sim</option>
                      <option value="0"> Não</option>                   
                    </select>
                    <p></p>
      </div>
       

            
</div>

<div class="text-center">
        <button type="submit" name="submit" class="btn btn-primary">Adicionar</button>        
      </div>
    </form>


    </section>

  </main><!-- End #main -->

  <?php include '../includes/footer.php' ?>

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