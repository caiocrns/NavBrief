<?php 

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'google.com') !== false) {
        header("Location: /index.php");
        exit();
    }
}

      include 'includes/header.php';
      include 'includes/sidebar.php';
      include 'lib/conn.php';  
      include 'lib/function.php'; 


 $sql = "SELECT DISTINCT icao_aeronave FROM aeronaves";          // buscar anv banco dados
 $queryanv = mysqli_query($conexao,$sql);
 $lista_aeronaves = mysqli_fetch_assoc($queryanv);

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
    echo "
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: '$error_message',
            confirmButtonText: 'Ok'
        });
    </script>
    ";
}
 
 ?>
 

  <main id="main" class="main">

    <div class="pagetitle">
      <h1></h1>   
  
</div>

 
 <section class="section">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-6">

      <div class="alert alert-info" role="alert">
  <i class='fa fa-exclamation' style='font-size:20px'></i> <a href="#" id="showChangelogBtn"><b>Apoie </b></a> o projeto
</div>
          <div class="card">
            <div class="card-body">              
              <div class= "card-title text-center">
              <a><img src="assets/img/nbicon.png" style="width:120px;height:120px" alt=""></a>
                <h4 class="alert-heading"></h4>
                <p> Planeje o seu voo e tenha acesso ao METAR, TAF, NOTAM, Cartas, dentre outras informações necessárias.</p>
                <!--<p><span><b>Planejar voo</b> para obter briefing personalizado para sua aeronave</span></p>
                <p><span><b> Info Aeródromos</b> para obter todas as informações do local de origem, destino e alternativa.</span></p>-->
                <hr>
                <button type="button" class="btn btn-outline-success mb-2 me-2" data-bs-toggle="modal" data-bs-target="#selecairports">
    <i class="bi bi-journal-check"></i> Briefing Aeródromos
</button>
<button type="button" class="btn btn-outline-warning mb-2 me-2" data-bs-toggle="modal" data-bs-target="#selecprojeto">
    <i class="fa fa-plane"></i> Planejar voo
</button>
<button type="button" class="btn btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#grametModal">
    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Gerar GRAMET
</button>

               

<!-- Briefing aerodromos -->
<!-- Modal -->
<div class="modal fade" id="selecairports" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Solicitar Briefing dos Aeródromos (Código ICAO)</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

      <!-- CONTEUDO MODAL -->      
      <form method="get" action="dashairport.php">
  <div class="col-auto">
    <div class="form-floating mb-3">
      <div id="inputs-container">
        <input type="text" name="dep" placeholder="Origem" style="text-transform: uppercase;"   
  minlength="4" 
  maxlength="4" class="form-control mb-2" required>
        <input type="text" name="arr" placeholder="Destino" style="text-transform: uppercase;"    
  minlength="4" 
  maxlength="4" class="form-control mb-2" required>
        <input type="text" name="altn" placeholder="Alternativo" style="text-transform: uppercase;"  
  minlength="4" 
  maxlength="4" class="form-control mb-2" required>
        <input type="number" name="fl" placeholder="Nivel de Voo" style="text-transform: uppercase;" min="000" max="999" class="form-control mb-2" required>
        
      </div>
      
    </div>
  </div>
  <div class="modal-footer">
    <a href="donation.php" style="position: absolute; left: 5px;" class="btn btn-outline-success btn-sm">
      <b>Ajude!</b> <i class="fa-brands fa-pix"></i>
    </a>
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
    <button type="submit" name="submit" class="btn btn-success">Prosseguir</button>
  </div>
</form>

      </div>
    </div>
  </div>
</div>
<!-- END BRIEFING AERODROMOS -->            
              

</div>
</div>
</div>
</div>


                <!-- PLANEJAR VOO -->

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


  <style>
        /* Custom styling for the backdrop */
        .swal2-container.swal2-backdrop-show {
            background: rgba(0, 0, 76, 0.4);
            backdrop-filter: blur(10px);
        }

        /* Custom styling for the content */
        .swal2-html-container {
            text-align: left;
        }

        .swal2-html-container ul {
            padding-left: 20px;
            list-style-type: disc;
            list-style-position: outside;
            margin: 10px 0;
        }

        .swal2-html-container ul li {
            margin-bottom: 8px;
            font-size: 16px;
            color: #444;
        }

        .swal2-html-container a {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        .swal2-html-container a:hover {
            text-decoration: underline;
        }

        .swal2-html-container hr {
            margin: 20px 0;
            border: 0;
            height: 1px;
            background: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
        }

        .swal2-html-container p {
            color: green;
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
        }
         .badge-green {
      color: white;
      background-color: green;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.9em;
      margin-right: 5px;
    }
    .badge-yellow {
      color: black;
      background-color: yellow;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.9em;
      margin-right: 5px;
    }
    </style>

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
 <script>
    document.getElementById('showChangelogBtn').addEventListener('click', () => {
      Swal.fire({
        title: "",
        html: `
          <div style="justify-content:left;">
            <ul>
            <li><span class="badge badge-pill badge-info">Versão</span> 25/01/2025</li>             
            </ul>
            <hr>
            Caso encontre algum erro ou tenha sugestões, envie-nos uma <a href="https://www.instagram.com/navbrief" target="_blank">mensagem no instagram</a>.
            <hr>
            <p>Considere fazer uma doação para manter o NavBrief online!</p>
          </div>
        `,
        width: 600,
        padding: "3em",
        color: "#007aff",
        background: "#fff",
        backdrop: `
          rgba(0,0,76,0.4)
          left top
          no-repeat
        `,
        icon: "info",
        showCancelButton: true,
        confirmButtonText: 'Doar Agora',
        confirmButtonColor: 'green',
        cancelButtonText: 'Fechar'
      });
    });
  </script>

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