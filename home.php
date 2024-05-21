<?php include 'includes/header.php';
      include 'includes/sidebar.php';
      include 'lib/conn.php';  
      include 'lib/function.php'; 


 $sql = "SELECT DISTINCT icao_aeronave FROM aeronaves";          // buscar anv banco dados
 $queryanv = mysqli_query($conexao,$sql);
 $lista_aeronaves = mysqli_fetch_assoc($queryanv);


 
 ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1></h1>   
  
</div>

 
 <section class="section">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-6">

      
          <div class="card">
            <div class="card-body">              
              <div class= "card-title text-center">
              <a><img src="assets/img/nbicon.png" style="width:120px;height:120px" alt=""></a>
                <h4 class="alert-heading"></h4>
                <p> Planeje sua missão e tenha acesso ao METAR, NOTAM e ROTAER. Solicite também a LoadSheet do seu voo.</p>
                <p><span> LoadSheet personalizada para cada tipo de aeronave</span></p>
                <hr>
                <a href="index.php"><button type="button" class="btn btn-sm btn-danger"> Sair</button></a>
                <button type="button" class=" btn btn-primary" data-bs-toggle="modal" data-bs-target="#selecprojeto">
  Planejar voo
</button>

                <!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="selecprojeto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Selecione a sua aeronave</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

      <!-- CONTEUDO MODAL -->
      <span> Cada projeto possui um despacho de voo personalizado.</span>
      <p></p>
      <form method="post" action="planner.php">
      <div class="col-auto">
                  <div class="form-floating mb-3">
                    <select class="form-select" name="aeronaveselec" id="floatingSelect" aria-label="State" required>                    
                    <option value="" disabled selected>Selecione</option>
                    <?php foreach($queryanv as $resultado) { ?>
                    <option value="<?php echo $resultado['icao_aeronave'];?>"><?php echo $resultado['icao_aeronave'];?> </option>
                  <?php } ?>                   
                    </select>
                    <label for="floatingSelect">Aeronave</label>
                  </div>
                </div>
      </div>
      <div class="modal-footer">
      <a href="donation.php" style="position: absolute; left: 5px;" class="btn btn-outline-success btn-sm"><b> Ajude! </b><i class="fa-brands fa-pix"></i></a>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>        
        <button type="submit" name="submit" id="submit" class="btn btn-success">Prosseguir</button>   
                    </form>
      </div>
    </div>
  </div>
</div>
<!-- END CONTEUDO -->

            
              

</div>
</div>
</div>
</div>

</div>
</section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'includes/footer.php' ?>

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