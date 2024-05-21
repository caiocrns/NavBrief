<?php include 'includes/header.php' ?>
<?php include 'includes/sidebar.php' ?>
  
<?php
include_once('lib/conn.php');


$sql = "SELECT LPAD(niveldevoo, 3, '0') AS 0_niveldevoo, voos.*
        FROM voos ORDER BY criado DESC";
$queryvoos = mysqli_query($conexao, $sql);
$row_voos = mysqli_fetch_assoc($queryvoos);

 ?>


<main id="main" class="main">
  
  
  <div class="pagetitle text-center">
    <h1>Histórico de planejamentos</h1>
    
    <hr>      
 
    <div class="card recent-sales overflow-auto">

                <!-- Table with stripped rows -->
                <table class="table-sm datatable" >
            <thead>
                <tr>
                <th scope="col"></th>
                  <th scope="col">Origem</th>
                  <th scope="col">Destino</th>
                  <th scope="col">Alternativo</th>
                  <th scope="col">FL</th>
                  <th scope="col">Data do voo</th> 
                  <th scope="col">Aeronave</th> 
                  <th scope="col">Hora DEP</th> 
                  <!--<th scope="col">Peso Trip</th>-->
                  <th scope="col">Payload (kg)</th> 
                  <!--<th scope="col">Eqp</th> -->
                  <th scope="col">Criado em:</th>                                      
                </tr>
              </thead>
              <tbody>
              <?php if (mysqli_num_rows($queryvoos) > 0) {
                foreach ($queryvoos as $lista_voos) { ?>
    <tr>
        <td><a class="btn btn-sm btn-primary" href="editplanner.php?id=<?php echo $lista_voos['id']; ?>"><i class="bi bi-airplane-fill"></i></td>
        <td><?php echo $lista_voos['origem']; ?></td>
        <td><?php echo $lista_voos['destino']; ?></td>
        <td><?php echo $lista_voos['alternativo']; ?></td>
        <td><?php echo $lista_voos['0_niveldevoo']; ?></td>
        <td><?php echo date('d/m/y',strtotime($lista_voos['datadovoo'])); ?></td>
        <td>
            <?php
            // Consulta para obter os dados da aeronave
            $sql_aeronave = "SELECT icao_aeronave,matricula FROM aeronaves WHERE id = '" . $lista_voos['aeronave'] . "'";
            $result_aeronave = $conexao->query($sql_aeronave);
            $aeronave = $result_aeronave->fetch_assoc();
            $num = mysqli_num_rows($result_aeronave);
            if($num > 0 ) {
              echo $aeronave[ 'icao_aeronave']; echo "|"; echo $aeronave['matricula']; 
            } else {
              echo 'Aeronave Indisponível.';
            }          
            
            ?>
        </td>
        <td><?php echo date('H:i', strtotime($lista_voos['horadep'])); ?></td>
        <!--<td><?php echo $lista_voos['pesotrip']; ?></td>-->
        <td><?php echo $lista_voos['cargaprev']; ?></td>
        <!--<td><?php echo $lista_voos['kitbasico'] + $lista_voos['kitmar'] + $lista_voos['kitselva']; ?></td>-->
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

<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        "paging": true, // Ativar paginação
        "lengthChange": false, // Desabilitar seleção do número de registros por página
        "searching": true, // Ativar pesquisa
        "ordering": true, // Ativar ordenação        
        "info": true, // Exibir informações sobre a paginação
        "autoWidth": false, // Desabilitar largura automática das colunas
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });
});
</script>

<?php include 'includes/footer.php'   ?>


<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <!--<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>-->
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- datatable -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
