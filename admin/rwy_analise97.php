

<?php include 'includes/header_admin.php' ?>
<?php include 'includes/sidebar_admin.php';
include('../lib/function.php');
include('lib/db_function.php'); ?>
  
<?php

include_once('../lib/conn.php');
// Fetch distinct ICAO values
$sql = "SELECT DISTINCT icao FROM rwy_analise97";
$query = mysqli_query($conexao, $sql);

$results = [];
while ($row = mysqli_fetch_assoc($query)) {
    $icao = $row['icao'];
    
    // Fetch the full row based on the unique ICAO
    $sql2 = "SELECT * FROM rwy_analise97 WHERE icao = '$icao'";
    $query2 = mysqli_query($conexao, $sql2);
    
    if ($query2) {
        while ($row_all = mysqli_fetch_assoc($query2)) {
            $results[$icao][] = $row_all;
        }
    }
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
        <h5 class="pagetitle"> Gerenciar Performance C97 </h5>
        <a class="btn btn-sm btn-primary" href="#" data-toggle="modal" data-target="#addModal"><i class="bi bi-plus-square-fill"></i> Adicionar dados</a>
        <p></p>
        <div class="card">
            <!-- Table with stripped rows -->
            <table class="table-sm datatable">
                <thead>
                    <tr>
                        <th scope="col">ICAO</th>
                        <th scope="col">Info</th>                   
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $icao => $rows) { ?>
                    <tr>
                        <td scope="row"><?php echo $icao; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#myModal<?php echo $icao; ?>"><i class="fa fa-info-circle"></i></button>
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal<?php echo $icao; ?>">
                        <div class="modal-dialog ">
                            <div class="modal-content">
                                <!-- Modal content -->
                                <div class="modal-header">
                                    <h4 class="modal-title"><b><?php echo $icao; ?></b></h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <?php foreach ($rows as $row) { ?>
                                            <form id="editForm<?php echo $row['id']; ?>" class="edit-form">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="form-group">
                                                    <label for="rwy">RWY</label>
                                                    <input type="text" class="form-control" name="rwy" value="<?php echo $row['rwy']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="temp"><b>Temp</b></label>
                                                    <input type="text" class="form-control" name="temp" value="<?php echo $row['temp']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mtow">MTOW</label>
                                                    <input type="text" class="form-control" name="mtow" value="<?php echo $row['mtow']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mlw">MLW</label>
                                                    <input type="text" class="form-control" name="mlw" value="<?php echo $row['mlw']; ?>">
                                                </div>
                                                <button type="button" class="btn btn-primary save-btn" data-id="<?php echo $row['id']; ?>">Salvar</button>
                                            </form>
                                            <hr>
                                        <?php } ?>
                                    </div>
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


<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog ">
        <div class="modal-content">
            <!-- Modal content -->
            <div class="modal-header">
                <h4 class="modal-title"><b>Adicionar dados</b></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addForm" class="add-form">
                    <div class="form-group">
                        <label for="icao">ICAO</label>
                        <input type="text" class="form-control" name="icao" required>
                    </div>
                    <div class="form-group">
                        <label for="rwy">RWY</label>
                        <input type="text" class="form-control" name="rwy" required>
                    </div>
                    <div class="form-group">
                        <label for="temp">Temp</label>
                        <input type="text" class="form-control" name="temp" required>
                    </div>
                    <div class="form-group">
                        <label for="mtow">MTOW</label>
                        <input type="text" class="form-control" name="mtow" required>
                    </div>
                    <div class="form-group">
                        <label for="mlw">MLW</label>
                        <input type="text" class="form-control" name="mlw" required>
                    </div>
                    <button type="button" class="btn btn-primary add-btn">Adicionar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>








</main>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
$(document).ready(function() {
    $('.save-btn').on('click', function() {
        var id = $(this).data('id');
        var form = $('#editForm' + id);

        $.ajax({
            type: 'POST',
            url: 'update_rwy_analise97.php', // The PHP file to handle the update
            data: form.serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Dados atualizados com sucesso',
                    showConfirmButton: false,
                    timer: 2000
                }).then(function() {
                    window.location.reload();
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao atualizar dados',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });

    $('.add-btn').on('click', function() {
        var form = $('#addForm');

        $.ajax({
            type: 'POST',
            url: 'add_rwy_analise97.php', // The PHP file to handle the addition
            data: form.serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Dados adicionados com sucesso',
                    showConfirmButton: false,
                    timer: 2000
                }).then(function() {
                    window.location.reload();
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao adicionar dados',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });
});
</script>
</body>

</html>