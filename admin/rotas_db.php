

<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php';
include('../lib/function.php');
include('lib/db_function.php'); ?>
  
<?php

include_once('../lib/conn.php');

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
$id = $_POST['id'];

 
 $sqlupdate = "UPDATE aeronaves SET icao_aeronave = '$icao_aeronave', nome_aeronave = '$nome_aeronave', matricula = '$matricula', motor = '$motor', comentario = '$comentario', cod_performance = '$cod_performance',  categoria = '$categoria',  icao_eqp = '$icao_eqp', icao_xpdr = '$icao_xpdr', pbn = '$pbn', extra_fpl = '$extra_fpl', operador = '$operador', max_pax = '$max_pax', peso_basico = '$peso_basico', mzfw = '$mzfw', mtow = '$mtow', mlw = '$mlw', mfuel = '$mfuel', mtw = '$mtw', teto_ft = '$teto_ft', velocidade_media = '$velocidade_media', modelo = '$modelo',consumo = '$consumo' WHERE id = '$id'";
$update = $conexao->query($sqlupdate);

}

$sql = "SELECT * FROM aeronaves";                          //BUSCAR AERONAVES BANCO DE DADOS
$queryanv = mysqli_query($conexao,$sql);
$row_aeronaves = mysqli_fetch_assoc($queryanv);

$id = $row_aeronaves['id'];

                          //BUSCAR AERONAVES BANCO DE DADOS
$queryrotas = mysqli_query($conexao,"SELECT * FROM rotas");
$row_rotas = mysqli_fetch_assoc($queryrotas);
$conexao->close();

if (isset($_GET['rotadeletada']) && $_GET['rotadeletada'] === 'sim') {
  echo "<script>";
  echo "Swal.fire({ 
    icon: 'success',
    title: 'Rota excluída com sucesso',
    showConfirmButton: false,
    timer: 2000
  }).then(function() {               
    window.location.href = 'rotas_db.php';
});";
  echo "</script>";
  
}
?>

<style>
        p {
            margin-bottom: 0.2em; /* Ajuste o valor conforme necessário */
        }
        a {
          margin-left: 0.6em;
        }
    </style>

  <main id="main" class="main">
  
    <div class="pagetitle">
      <h1>Banco de dados</h1>
      
      <hr>
     

<h5 class="pagetitle"> Gerenciar rotas </h5>
      <a  class="btn btn-sm btn-primary" href="addroute.php"><i class="bi bi-plus-square-fill"></i> Adicionar rota</a>
      <p></p>
  
     <p></p>
     
   
      <div class="card">
                  <!-- Table with stripped rows -->
              <table class="table-sm datatable">
              <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Origem</th>
                    <th scope="col">Rota</th> 
                    <th scope="col">Destino</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Aerovia?</th> 
                    <th scope="col">AIRAC</th>    
                    <th scope="col">Ações</th>                    
                  
                                                         
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($queryrotas as $row_rotas) { ?>
                  <tr>
                    <td scope="row"><?php echo $row_rotas['id']; ?></td>
                    <td><?php echo $row_rotas['origem']; ?></td>
                    <td><?php echo strtoupper($row_rotas['rota']); ?></td> 
                    <td><?php echo $row_rotas['destino']; ?></td>     
                    <td><?php echo $row_rotas['espaco_aereo']; ?></td>
                    <td><?php echo $row_rotas['aerovia'] == 0 ? "Não":"Sim"; ?></td> 
                    <td><?php verifica_rota( $row_rotas['rota'])?></td>              
                  
                    <td><a class= "btn btn-sm btn-primary" href="editroute.php?id=<?php echo $row_rotas['id']; ?>"><i class="bi bi-pencil"></i></a>
                    <a class="btn btn-sm btn-danger btnExcluir" href="#" data-id="<?php echo $row_rotas['id']; ?>"><i class="bi bi-trash"></i></a></td>
                    
                  </tr>
    
                  <?php } ?>              
                 
                 </tbody>
                 
               </table>
               
</div>
    </section>
  
  </main><!-- End #main -->
  
  <?php include '../includes/footer.php' ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
  <script> 
  document.addEventListener('DOMContentLoaded', function() {   
    const botoesExcluir = document.querySelectorAll('.btnExcluir');    
    botoesExcluir.forEach(function(botaoExcluir) {
      botaoExcluir.addEventListener('click', function(event) {       
        event.preventDefault();
        const idRota = botaoExcluir.getAttribute('data-id');     
        Swal.fire({
          title: 'Excluir Rota',
          text: 'Tem certeza de que deseja excluir esta rota?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sim, excluir',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {          
            window.location.href = 'deleteroute.php?id=' + idRota;
          }
        });
      });
    });
  });
</script>

</body>

</html>