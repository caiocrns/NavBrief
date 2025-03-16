<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php';
include_once('../lib/conn.php'); ?>

<?php 

 if(!empty($_GET['id'])) {


$id = $_GET['id'];

$sqlSelect = "SELECT * FROM rotas WHERE id='$id'";
$result = $conexao->query($sqlSelect);

if($result->num_rows > 0)
{
  while($row_rotas = mysqli_fetch_assoc($result))
{

  $origem = $row_rotas['origem'];  
  $rota =$row_rotas['rota'];
  $destino = $row_rotas['destino'];
  $tipo = $row_rotas['espaco_aereo'];
  $aerovia = $row_rotas['aerovia'];
}
 
}

else {
  header ('location: rotas_db.php');  
  
 }
}

if(isset($_POST['update']))               // ATUALIZAR AERONAVE
{

$origem = $_POST['origem'];  
$rota = $_POST['rota'];
$destino = $_POST['destino'];
$tipo = $_POST['tipo'];
$aerovia = $_POST['aerovia'];
 
 $sqlupdate = "UPDATE rotas SET origem = '$origem', rota = '$rota', destino = '$destino', espaco_aereo = '$tipo', aerovia = '$aerovia' WHERE id = '$id'";
$update = $conexao->query($sqlupdate);

}
  
 ?>  

  <main id="main" class="main">
  
  <div class="pagetitle">
    <h1>Editar rota</h1>

    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="homeadmin.php">Home</a></li>
        <li class="breadcrumb-item"><a href="rotas_db.php">Rotas</a></li>
        <li class="breadcrumb-item active">Editar</li>
      </ol>
    </nav>   
  </div> 
  <?php if (isset($update) && $update == true) { ?>
    <script>
       Swal.fire({ 
  icon: 'success',
  title: 'Rota editada!',
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
      <input type="text" class="form-control" name="origem" value="<?php echo $origem ?>">
    </div>

    <div class="col-4">
      <label class="form-label">Destino</label>
      <input type="text" class="form-control" name="destino" value="<?php echo $destino ?>">
    </div>
 <br>
   <Style>
   .input-rota {
width:500px
} </style> 

<div class="col-4">
      <label  class="form-label">Tipo (Espaço Aéreo)</label>
      <select class="form-select" name="tipo"  id="floatingSelect" aria-label="State" value="<?php echo $tipo ?>" required>
                    <option value= "<?php echo $tipo ?>" selected><?php echo $tipo ?></option> 
                    <option value="L"> L (Inferior)</option>
                    <option value="H"> H (Superior)</option>                   
                  </select>
</div>

    <div class="col-4">
      <label class="form-label">Rota</label>
      <input type="text" class="form-control input-rota" name="rota" value="<?php echo $rota ?>">
    </div>

    </div>
    <div class="col-4">
      <label  class="form-label">Possui Aerovia?</label>
      <select class="form-select" name="aerovia"  id="floatingSelect" aria-label="State" required>
                    <option value="<?php echo $aerovia ?>" selected > <?php echo $aerovia == 0 ? "Não":"Sim"; ?> </option>
                    <option value="1"> Sim</option>
                    <option value="0"> Não</option>                   
                  </select>
                  <p></p>
    </div>
     
    <input type="hidden" class="form-control" name="id" value="<?php echo $id ?>">
          
</div>

<div class="text-center">
      <button type="submit" name="update" class="btn btn-primary">Editar</button>        
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