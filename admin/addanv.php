<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php' ?>

<?php 

 if(isset($_POST['submit']))
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


 
  
  // Prepara a instrução SQL para inserção dos dados
  $sqladd = "INSERT INTO aeronaves(icao_aeronave, nome_aeronave, matricula, motor, comentario, cod_performance, categoria, icao_eqp, icao_xpdr, pbn, extra_fpl, operador, max_pax, peso_basico, mzfw, mtow, mlw, mfuel, mtw, teto_ft, velocidade_media, modelo,consumo,consumo2,unidade) VALUES ('$icao_aeronave', '$nome_aeronave','$matricula', '$motor', '$comentario', '$cod_performance', '$categoria', '$icao_eqp', '$icao_xpdr', '$pbn', '$extra_fpl', '$operador', '$max_pax', '$peso_basico', '$mzfw', '$mtow', '$mlw', '$mfuel', '$mtw', '$teto_ft', '$velocidade_media','$modelo','$consumo','$consumo2','$unidade')";
  $add = $conexao->query($sqladd);
  $conexao->close();
 }
 ?>  

  <main id="main" class="main">
  
    <div class="pagetitle">
      <h1>Adicionar aeronave</h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="homeadmin.php">Home</a></li>
          <li class="breadcrumb-item"><a href="aeronaves_db.php">Registro de aeronaves</a></li>
          <li class="breadcrumb-item active">Adicionar</li>
        </ol>
      </nav>   
    </div> 
    <?php if (isset($add) && $add == true) { ?>
    <script>
       Swal.fire({ 
  icon: 'success',
  title: 'Aeronave adicionada!',
  showConfirmButton: false,
  timer: 1500
}).then(function() {               
                window.location.href = "aeronaves_db.php";
            });
        
    </script>
<?php } ?>
<!--  -->

<div class="container">
    <form method="post">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Info</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">ICAO Aeronave</label>
                                <input type="text" class="form-control" name="icao_aeronave">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control" name="nome_aeronave">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Matricula</label>
                                <input type="text" class="form-control" name="matricula">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Motor</label>
                                <input type="text" class="form-control" name="motor">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Comentários</label>
                                <input type="text" class="form-control" name="comentario">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Operador</label>
                                <input type="text" class="form-control" name="operador">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Equipamentos</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Performance</label>
                                <input type="text" class="form-control" name="cod_performance">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Categoria</label>
                                <select class="form-select" name="categoria" id="floatingSelect" aria-label="Floating select">
                                    <option value=""></option>
                                    <option value="L">L (Leve)</option>
                                    <option value="M">M (Médio)</option>
                                    <option value="H">H (Pesada)</option>
                                    <option value="J">J (Jumbo)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Equipamentos</label>
                                <input type="text" class="form-control" name="icao_eqp">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Transponder</label>
                                <input type="text" class="form-control" name="icao_xpdr">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Capacidade PBN</label>
                                <input type="text" class="form-control" name="pbn">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Campo 18 (RMK) FPL</label>
                                <input type="text" class="form-control" name="extra_fpl">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pesos (Kg)</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">MTOW</label>
                                <input type="text" class="form-control" name="mtow">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">MLW</label>
                                <input type="text" class="form-control" name="mlw">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">MZFW</label>
                                <input type="text" class="form-control" name="mzfw">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">MTW</label>
                                <input type="text" class="form-control" name="mtw">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Max Comb</label>
                                <input type="text" class="form-control" name="mfuel">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">PBO</label>
                                <input type="text" class="form-control" name="peso_basico">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Max Pax</label>
                                <input type="text" class="form-control" name="max_pax">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Performance</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Velocidade Média (KT)</label>
                                <input type="text" class="form-control" name="velocidade_media">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Teto de serviço (ft)</label>
                                <input type="text" class="form-control" name="teto_ft">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Modelo</label>
                                <input type="text" class="form-control" name="modelo">
                            </div>
                            <div class="col-12 col-md-4">
                                <label>Consumo (1º hora) ou médio</label>
                                <input type="text" class="form-control" name="consumo">
                            </div>
                            <div class="col-12 col-md-4">
                                <label>Consumo (2)</label>
                                <input type="text" class="form-control" name="consumo2">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Unidade do Fuel:</label>
                                <select class="form-control" name="unidade">
                                    <option value="kg">Kg</option>
                                    <option value="lb">Lb</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-3">
            <button type="submit" name="submit" class="btn btn-primary">Adicionar aeronave</button>
        </div>
    </form>
</div>










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