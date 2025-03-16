

<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php';
include('../lib/function.php');
include('lib/db_function.php'); ?>
  
<?php

include_once('../lib/conn.php');

$sql = "SELECT * FROM aeronaves";                          //BUSCAR AERONAVES BANCO DE DADOS
$queryanv = mysqli_query($conexao,$sql);
$row_aeronaves = mysqli_fetch_assoc($queryanv);

$id = $row_aeronaves['id'];

if (isset($_GET['anvdeletada']) && $_GET['anvdeletada'] === 'sim') {
  echo "<script>";
  echo "Swal.fire({ 
    icon: 'success',
    title: 'Aeronave excluída com sucesso',
    showConfirmButton: false,
    timer: 2000
  }).then(function() {               
    window.location.href = 'aeronaves_db.php';
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
      <h5 class="pagetitle"> Gerenciar aeronaves </h5>
      <a  class="btn btn-sm btn-primary" href="addanv.php"><i class="bi bi-plus-square-fill"></i> Adicionar aeronave</a>
      <p></p>
     
   
      <div class="card">
                  <!-- Table with stripped rows -->
              <table class="table-sm datatable">
              <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">ICAO</th>
                    <th scope="col">Aeronave</th> 
                    <th scope="col">Matrícula</th>  
                    <th scope="col">Ações</th>                    
                  
                                                         
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($queryanv as $row_aeronaves) { ?>
                  <tr>
                    <td scope="row"><?php echo $row_aeronaves['id']; ?></td>
                    <td><?php echo $row_aeronaves['icao_aeronave']; ?></td>
                    <td><?php echo $row_aeronaves['nome_aeronave']; ?></td> 
                    <td><?php echo $row_aeronaves['matricula']; ?></td>                
                  
                    <td> <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#myModal<?php echo $row_aeronaves['id']; ?>"><i class="fa fa-info-circle"></i></button>
                     <a class= "btn btn-sm btn-primary" href="editanv.php?id=<?php echo $row_aeronaves['id']; ?>"><i class="bi bi-pencil"></i>
                     <a class="btn btn-sm btn-danger btnExcluir" href="#" data-id="<?php echo $row_aeronaves['id']; ?>"><i class="bi bi-trash"></i></a></td>
                    
                  </tr>
                      <!-- Modal -->
                <div class="modal fade" id="myModal<?php echo $row_aeronaves['id']; ?>">
                    <div class="modal-dialog ">
                        <div class="modal-content">
                            <!-- Conteúdo do modal -->
                            <div class="modal-header">
                                <h4 class="modal-title"><b><?php echo $row_aeronaves['icao_aeronave']; ?> | <?php echo $row_aeronaves['matricula']; ?></b></h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
      <h7 class="card-title">Info</h7>
      
      <p><b>ID:</b> <?php echo $row_aeronaves['id']; ?></p>      
      <p><b>ICAO:</b> <?php echo $row_aeronaves['icao_aeronave']; ?></p>
      <p><b>Nome:</b> <?php echo $row_aeronaves['nome_aeronave']; ?></p>
      <p><b>Motor:</b> <?php echo $row_aeronaves['motor']; ?></p>
      <p><b>Modelo:</b> <?php echo $row_aeronaves['modelo']; ?></p>     
      <p><b>Operador:</b> <?php echo $row_aeronaves['operador']; ?></p>    
      <hr>
      <h7 class="card-title">Equipamento</h7>
      <p><b>PBN:</b> <?php echo $row_aeronaves['pbn']; ?></p>
      <p><b>Performance:</b> <?php echo $row_aeronaves['cod_performance']; ?></p>
      <p><b>Cat Peso:</b> <?php echo $row_aeronaves['categoria']; ?></p>
      <p><b>Equip:</b> <?php echo $row_aeronaves['icao_eqp']; ?></p>
      <p><b>XPDR:</b> <?php echo $row_aeronaves['icao_xpdr']; ?></p>  
      <p><b>RMK FPL:</b> <?php echo $row_aeronaves['extra_fpl']; ?></p>              


      </div>
      <div class="col-md-4 ml-auto">

      <h7 class="card-title">Pesos (Kg)</h7>
      <p><b>Peso Básico:</b> <?php echo $row_aeronaves['peso_basico']; ?></p>
      <p><b>Max Dep:</b> <?php echo $row_aeronaves['mtow']; ?></p>
      <p><b>Máx LDG:</b> <?php echo $row_aeronaves['mlw']; ?></p>
      <p><b>Máx TAXI:</b> <?php echo $row_aeronaves['mtw']; ?></p>
      <p><b>Máx ZFW:</b> <?php echo $row_aeronaves['mzfw']; ?></p>
      <p><b>Máx FUEL:</b> <?php echo $row_aeronaves['mfuel']; ?></p>
      <p><b>Máx PAX:</b> <?php echo $row_aeronaves['max_pax']; ?></p>
      <hr>
      <h7 class="card-title">Performance</h7>      
      <p><b>Teto de serviço:</b> <?php echo $row_aeronaves['teto_ft']; ?></p>
      <p><b>Velocidade média:</b> <?php echo $row_aeronaves['velocidade_media']; ?></p>
      <p><b>Consumo médio (LB/H):</b> <?php echo $row_aeronaves['consumo']; ?></p>      
     

      </div>      
    </div>    
</div>
<hr>
<div class="col-8 col-sm-6">
<b>Comentários:</b> <?php echo $row_aeronaves['comentario']; ?>
            
          </div>  

                          

                                <!-- Adicione aqui mais informações do modal com base nos dados do banco de dados -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>              
                 
                </tbody>
                
              </table>
              <!-- End Table with stripped rows -->
</div>






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
          title: 'Excluir aeronave',
          text: 'Tem certeza de que deseja excluir esta aeronave?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sim, excluir',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {          
            window.location.href = 'deleteanv.php?id=' + idRota;
          }
        });
      });
    });
  });
</script>
</body>

</html>