<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php' ?>
  
<?php
include_once('../lib/conn.php');


$sql = "SELECT DISTINCT origem, destino, alternativo, LPAD(niveldevoo, 3, '0') AS 0_niveldevoo, voos.*
        FROM voos WHERE rota != '' ORDER BY criado DESC";
$queryvoos = mysqli_query($conexao, $sql);
$row_voos = mysqli_fetch_assoc($queryvoos);

 ?>


<main id="main" class="main">
  
  
  <div class="pagetitle text-center">
    <h1>Histórico de planejamentos (Rotas)</h1>
    
    <hr>      
 
    <div class="card recent-sales overflow-auto">

                <!-- Table with stripped rows -->
                <table class="table-sm datatable">
            <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">Origem</th>
                  <th scope="col">Destino</th>
                  <th scope="col">Alternativo</th>
                  <th scope="col">FL</th>
                  <th scope="col">Rota</th> 
                  <th scope="col">Aeronave</th>                  
                  <th scope="col">Criado em:</th>                                      
                </tr>
              </thead>
              <tbody>
              <?php if (mysqli_num_rows($queryvoos) > 0) {
                foreach ($queryvoos as $lista_voos) { ?>
        <tr>
        <td scope="row"><a href="https://skyvector.com/?ll=-15.860957599564097,-49.18029785623944&chart=302&zoom=11&fpl=%20<?php echo $lista_voos['origem']; ?>%20<?php echo $lista_voos['rota'] ?>%20<?php echo $lista_voos['destino']; ?>" class="button-skyvector" target="_blank"><img src="../assets/img/skyvector1.png" style="height:15px;"></img></a></td>
        <td><?php echo $lista_voos['origem']; ?></td>
        <td><?php echo $lista_voos['destino']; ?></td>
        <td><?php echo $lista_voos['alternativo']; ?></td>
        <td><?php echo $lista_voos['0_niveldevoo']; ?></td>
        <td><?php echo $lista_voos['rota']; ?></td>
        <td>
            <?php
            // Consulta para obter os dados da aeronave
            $sql_aeronave = "SELECT icao_aeronave,matricula FROM aeronaves WHERE id = '" . $lista_voos['aeronave'] . "'";
            $result_aeronave = $conexao->query($sql_aeronave);
            $aeronave = $result_aeronave->fetch_assoc();
            echo $aeronave[ 'icao_aeronave']; echo "|"; echo $aeronave['matricula']; 
            
            ?>
        </td>  
        <td><?php echo date('d/m/y',strtotime($lista_voos['criado'])); ?></td>
        
    </tr>
<?php }  ?>
<?php } else { echo "Não há voos realizados";} ?>

               
              </tbody>            
             

            </table> 
            
            <!-- End Table with stripped rows -->
</div>




            <!-- End Table with stripped rows -->
</div>


</main><!-- End #main -->

<?php include '../includes/footer.php'   ?>

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


