<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php' ?>

<?php 

 if(!empty($_GET['id']))
 {
include_once('../lib/conn.php');

$id = $_GET['id'];

$sqlSelect = "SELECT * FROM aeronaves WHERE id='$id'";
$result = $conexao->query($sqlSelect);

if($result->num_rows > 0)
{
  while($row_aeronaves = mysqli_fetch_assoc($result))
{

  $icao_aeronave = $row_aeronaves['icao_aeronave'];  
  $nome_aeronave = $row_aeronaves['nome_aeronave'];
  $matricula = $row_aeronaves['matricula'];
  $motor = $row_aeronaves['motor'];
  $comentario = $row_aeronaves['comentario'];
  $cod_performance = $row_aeronaves['cod_performance'];
  $categoria = $row_aeronaves['categoria'];
  $icao_eqp = $row_aeronaves['icao_eqp'];
  $icao_xpdr = $row_aeronaves['icao_xpdr'];
  $pbn = $row_aeronaves['pbn'];
  $extra_fpl = $row_aeronaves['extra_fpl'];  
  $operador = $row_aeronaves['operador']; 
  $max_pax = $row_aeronaves['max_pax'];
  $peso_basico = $row_aeronaves['peso_basico'];
  $mzfw = $row_aeronaves['mzfw'];
  $mtow = $row_aeronaves['mtow'];
  $mlw = $row_aeronaves['mlw'];
  $mfuel = $row_aeronaves['mfuel'];
  $mtw = $row_aeronaves['mtw'];
  $teto_ft = $row_aeronaves['teto_ft'];
  $velocidade_media = $row_aeronaves['velocidade_media'];
  $modelo = $row_aeronaves['modelo'];
  $consumo = $row_aeronaves['consumo'];
  $consumo2 = $row_aeronaves['consumo2'];
  $unidade = $row_aeronaves['unidade'];
}
 
}

else {
  header ('location: aeronaves_db.php');  
  
 }
}
if(isset($_POST['update']))               // ATUALIZAR AERONAVE
{
include_once('../lib/conn.php');
 
$icao_aeronave = $_POST['icao_aeronave'];  
$nome_aeronave = $_POST['nome_aeronave'];
$matricula = $_POST['matricula'];
$motor = $_POST['motor'];
$comentario = $_POST['comentario'];
$cod_performance = $_POST['cod_performance'];
$categoria = $_POST['categoria'];
$icao_eqp = $_POST['icao_eqp'];
$icao_xpdr = $_POST['icao_xpdr'];
$pbn = $_POST['pbn'];
$extra_fpl = $_POST['extra_fpl'];  
$operador = $_POST['operador']; 
$max_pax = $_POST['max_pax'];
$peso_basico = $_POST['peso_basico'];
$mzfw = $_POST['mzfw'];
$mtow = $_POST['mtow'];
$mlw = $_POST['mlw'];
$mfuel = $_POST['mfuel'];
$mtw = $_POST['mtw'];
$teto_ft = $_POST['teto_ft'];
$velocidade_media = $_POST['velocidade_media'];
$modelo = $_POST['modelo'];
$consumo = $_POST['consumo'];
$consumo2 = $_POST['consumo2'];
$unidade = $_POST['unidade'];
$id = $_POST['id'];

 
 $sqlupdate = "UPDATE aeronaves SET icao_aeronave = '$icao_aeronave', nome_aeronave = '$nome_aeronave', matricula = '$matricula', motor = '$motor', comentario = '$comentario', cod_performance = '$cod_performance',  categoria = '$categoria',  icao_eqp = '$icao_eqp', icao_xpdr = '$icao_xpdr', pbn = '$pbn', extra_fpl = '$extra_fpl', operador = '$operador', max_pax = '$max_pax', peso_basico = '$peso_basico', mzfw = '$mzfw', mtow = '$mtow', mlw = '$mlw', mfuel = '$mfuel', mtw = '$mtw', teto_ft = '$teto_ft', velocidade_media = '$velocidade_media', modelo = '$modelo',consumo = '$consumo',consumo2 = '$consumo2',unidade = '$unidade' WHERE id = '$id'";
$update = $conexao->query($sqlupdate);

}
  
 ?>

  <main id="main" class="main">
  
    <div class="pagetitle">
      <h1>Adicionar aeronave</h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="homeadmin.php">Home</a></li>
          <li class="breadcrumb-item"><a href="bancodados.php">Registro de aeronaves</a></li>
          <li class="breadcrumb-item active">Editar</li>
        </ol>
      </nav>   
    </div> 
  

<!--  -->

    <div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
            <h5 class="card-title">Info</h5>
            <?php if (isset($update) && $update == true) { ?>
    <script>
       Swal.fire({ 
  icon: 'success',
  title: 'Aeronave editada!',
  showConfirmButton: false,
  timer: 1500
}).then(function() {               
                window.location.href = "aeronaves_db.php";
            });
        
    </script>
<?php } ?>

            <form class="row g-3" method="post">
      <div class="col-4">
        <label class="form-label">ICAO Aeronave</label>
        <input type="text" class="form-control" name="icao_aeronave" value="<?php echo $icao_aeronave  ?>">
      </div>

      <div class="col-4">
        <label class="form-label">Nome</label>
        <input type="text" class="form-control" name="nome_aeronave" value="<?php echo $nome_aeronave  ?>">
      </div>

      <div class="col-4">
        <label class="form-label">Matricula</label>
        <input type="text" class="form-control" name="matricula" value="<?php echo $matricula  ?>">
      </div>
   
   
      <div class="col-4">
        <label  class="form-label">Motor</label>
        <input type="text" class="form-control" name="motor" value="<?php echo  $motor ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Comentários</label>
        <input type="text" class="form-control" name="comentario" value="<?php echo $comentario  ?>">
      </div>
      <div class="col-4">
        <label  class="form-label">Operador</label>
        <input type="text" class="form-control" name="operador" value="<?php echo $operador  ?>">
      </div>
       

            </div>
        </div>


        <div class="card mb-3">
            <div class="card-body">
            <h5 class="card-title">Equipamentos</h5>
            <div class="row">
            <div class="col-4">
        <label  class="form-label">Performance</label>
        <input type="text" class="form-control" name="cod_performance" value="<?php echo $cod_performance  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Categoria</label>
        <select class="form-select" name="categoria"  id="floatingSelect" aria-label="State" required>
                      <selected value = "<?php echo $categoria  ?>">
                      <option value="L"> L(Leve)</option>
                      <option value="M"> M(Medio)</option>
                      <option value="H"> H(Pesada)</option>
                      <option value="J"> J(Jumbo)</option>
                    </select>
      </div>

      <div class="col-4">
        <label class="form-label">Equipamentos </label>
        <input type="text" class="form-control" name="icao_eqp" value="<?php echo  $icao_eqp  ?>">
      </div>
      </div> <!-- row 1 -->
      <div class="row">
      <div class="col-4">
        <label  class="form-label">Transponder</label>
        <input type="text" class="form-control" name="icao_xpdr" value="<?php echo $icao_xpdr  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Capacidade PBN</label>
        <input type="text" class="form-control" name="pbn" value="<?php echo $pbn  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Campo 18 (RMK) FPL</label>
        <input type="text" class="form-control" name="extra_fpl" value="<?php echo $extra_fpl  ?>">
      </div>
      </div> <!-- row 2 -->

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
            <h5 class="card-title">Pesos (Kg)</h5>
            <div class="row">  
            <div class="col-4">
        <label  class="form-label">MTOW</label>
        <input type="text" class="form-control" name="mtow" value="<?php echo $mtow  ?>">
      </div>

      <div class="col-4">
        <label class="form-label">MLW</label>
        <input type="text" class="form-control" name="mlw" value="<?php echo $mlw  ?>">
      </div>

      <div class="col-4">
        <label class="form-label">MZFW</label>
        <input type="text" class="form-control" name="mzfw" value="<?php echo $mzfw  ?>">
      </div>
      </div> <!-- row 1 -->
    <div class="row">
      
      <div class="col-4">
        <label  class="form-label">MTW</label>
        <input type="text" class="form-control" name="mtw" value="<?php echo $mtw  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Max Comb</label>
        <input type="text" class="form-control" name="mfuel" value="<?php echo $mfuel  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">PBO</label>
        <input type="text" class="form-control" name="peso_basico" value="<?php echo $peso_basico  ?>">
      </div>
      </div> <!-- row 2 -->
      <div class="row">
      <div class="col-4">
        <label  class="form-label">Max Pax</label>
        <input type="text" class="form-control" name="max_pax" value="<?php echo $max_pax  ?>">
      </div>
      </div> <!-- row 3 -->
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
            <h5 class="card-title">Performance</h5>
            <div class="row">
            <div class="col-4">
        <label class="form-label">Velocidade Média</label>
        <input type="text" class="form-control" name="velocidade_media" value="<?php echo $velocidade_media  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Teto de serviço</label>
        <input type="text" class="form-control" name="teto_ft" value="<?php echo $teto_ft  ?>">
      </div>
      </div> <!-- row 1 -->
      <div class="row">
      <div class="col-4">
        <label  class="form-label">Modelo</label>
        <input type="text" class="form-control" name="modelo" value="<?php echo $modelo  ?>">
      </div>
      <div class="col-4">
        <label  class="form-label">Consumo médio ou 1º hora:</label>
        <input type="text" class="form-control" name="consumo" value="<?php echo $consumo  ?>">
      </div>

      <div class="col-4">
        <label  class="form-label">Consumo (2):</label>
        <input type="text" class="form-control" name="consumo2" value="<?php echo $consumo2  ?>">
      </div>

      <div class="col-4">
    <label class="form-label">Unidade do Fuel:</label>
    <select name="unidade" class="form-control">
        <option value="kg" <?php echo ($unidade == 'kg') ? 'selected' : ''; ?>>Kg</option>
        <option value="lb" <?php echo ($unidade == 'lb') ? 'selected' : ''; ?>>Lb</option>
        
</div>

      </div> <!-- row 2 -->
      <input type="hidden" class="form-control" name="id" value="<?php echo $id ?>">

            </div>
        </div>
    </div>
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