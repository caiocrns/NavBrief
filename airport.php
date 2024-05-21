
<?php include 'lib/function.php' ?>
<?php include 'lib/conn.php'   ?>
<?php include 'lib/config.php'   ?>

<?php 

$icaoarpt = $_GET['infoarpt'];

// PEGAR URL DA PAGINA
$protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
$url = '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 

$linkarpt = $protocolo.$url;

include 'includes/header.php'; 
include 'includes/sidebar.php';
?>

  <main id="main" class="main">
  <div id="overlay"><!--<img src = "../assets/img/fdicon.png">--><div class="loading-spinner"></div><span class="loading-text">Consultando as informações de: <?php echo strtoupper($icaoarpt) ?></span></div>
     <div class="pagetitle text-center">
      <h1> Informação: <?php echo strtoupper($icaoarpt) ?> </h1>
      <nav>
      <div class="rightmenu">
      <button onClick="shorturlecopiar()" class="btn btn-outline-success btn-sm"><i class="fa fa-link" aria-hidden="true" ></i> Compartilhar</button>          
        
      </div>
      <Style>
      .rightmenu { display: inline-block; float: right; padding-right: 10px;}
      .leftmenu { display: inline-block; float: left; padding-left: 10px;}
      </style>
          <ol class="breadcrumb">       
          <li class="breadcrumb-item"><a href="home.php">Planejar voo</a></li>
        </ol>
         
      </nav>
      
      
    </div><!-- End Page Title -->
    <hr>
   
    <section class="section dashboard">
      <div class="row"> 

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
          
   
   <div class="card">
            <div class="card-body overflow-auto">  
            <div class="text-center">
            <h5 class="card-title"><img src="assets/img/aisweb.png" style="width:80px;height:50px;text-align:center" ></h5>          
            </div>
              <div class="accordion accordion-flush" id="faq-group-1">

  <!-- CARTAS -->
              <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-target="#cartas" type="button" data-bs-toggle="collapse">
                    <h5 class="card-title"><i class="bi bi-map"></i> CARTAS  </h5> 
                    </button>
                  </h2>
                  <div id="cartas" class="accordion-collapse collapse" data-bs-parent="#faq-group-1">
                    <div class="accordion-body">
                    
                    <table  class="table text-center" style="width: 70%">             
              <tr> 
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#adc">
                <i class="fa fa-folder-o" aria-hidden="true"></i><b> ADC</b>
</button></th>
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#sid">
                <i class="fa fa-folder-o" aria-hidden="true"></i><b> SID</b>
</button></th>
                <th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#iac">
                <i class="fa fa-folder-o" aria-hidden="true"></i><b> IAC</b>
</button></th>
<th scope="col"><button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#star">
<i class="fa fa-folder-o" aria-hidden="true"></i><b> STAR</b>
</button></th>
              </tr>   
             </table>
                    
                    
                    </div>
                  </div>
                </div>
                
            
<!-- Modais -->
<!-- Large Modal 1 -->
<div class="modal" id="adc">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Cartas Aeronáuticas </h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      
      <?php getcartas(strtoupper($icaoarpt),"ADC") ?>
        
      </div>
    </div>
  </div>
</div>

<!-- Large Modal 2 -->
<div class="modal" id="sid">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cartas Aeronáuticas</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      
      <?php getcartas(strtoupper($icaoarpt),"SID") ?>
                  
      </div>
    </div>
  </div>
</div>

<!-- Large Modal 3 -->
<div class="modal" id="iac">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cartas Aeronáuticas</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      
      <?php getcartas(strtoupper($icaoarpt),"IAC") ?>
                  
      </div>
    </div>
  </div>
</div>    

<!-- Large Modal 4 -->
<div class="modal" id="star">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cartas Aeronáuticas</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      
      <?php getcartas(strtoupper($icaoarpt),"STAR") ?>
                 
      </div>
    </div>
  </div>
</div>  

<!-- END CARTAS -->
<!-- ROTAER -->
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-target="#rotaer" type="button" data-bs-toggle="collapse">                    
          <h5 class="card-title"><i class="bi bi-journal-check"></i> ROTAER </h5>                                
                    </button>
                  </h2>
                  <div id="rotaer" class="accordion-collapse collapse" data-bs-parent="#faq-group-1">
                    <div class="accordion-body">
                    <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert">   
                    <?php echo getrotaer($icaoarpt) ?>
                 </div>

                    </div>
                  </div>
                </div>
<!-- END ROTAER -->
<!-- NOTAM -->
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-target="#notam" type="button" data-bs-toggle="collapse">
                    <h5 class="card-title"><i class="bx bx-world"></i> NOTAM </h5> 
                    </button>
                  </h2>
                  <div id="notam" class="accordion-collapse collapse" data-bs-parent="#faq-group-1">
                    <div class="accordion-body">
                    <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert">  
                    <?php getnotam($icaoarpt) ?>
                    </div>
                    </div>
                  </div>
                </div>
<!-- END NOTAM -->
<!-- INFOTEMP -->
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-target="#infotemp" type="button" data-bs-toggle="collapse">
                    <h5 class="card-title"><i class="bx bx-world"></i> INFOTEMP </h5> 
                    </button>
                  </h2>
                  <div id="infotemp" class="accordion-collapse collapse" data-bs-parent="#faq-group-1">
                    <div class="accordion-body">
                    <div class="alert alert-primary alert-dismissible fade show overflow-auto" role="alert">  
                    <?php getinfotemp(strtoupper($icaoarpt)) ?>
                    </div>
                    </div>
                  </div>
                </div>
<!-- END INFOTEMP -->
   
              </div>

            </div>
          </div><!-- End F.A.Q Group 1 -->

         <!-- WINDY -->
<div class="card">          
       <div class="card-body overflow-auto">
       
         <div class="activity">  
          <p></p>            
         <iframe style="width: 100%; height: 500px; border-radius: 8px;" src="https://embed.windy.com/embed2.html?lat=&lon=&zoom=10&level=surface&overlay=wind&menu=&message=&marker=&calendar=&pressure=&type=map&location=coordinates&detail=&detailLat=-25.988&detailLon=-46.626&metricWind=default&metricTemp=default&radarRange=-1" frameborder="0"></iframe>
         </div>
       </div>            
     </div><!-- END WINDY-->

                  
         </div>
        </div><!-- End Left side columns -->

      

   

        <script src="https://api.checkwx.com/widget?key=dc54f84fdc0d4efda425a60932
" type="text/javascript"></script>

        <!-- Right side columns -->
        <div class="col-lg-4">

          <!-- METAR ORGN-->
          <div class="card">          
       

            <div class="card-body overflow-auto">
              <div class="checkwx-container" data-type="METAR" data-station="<?php echo $icaoarpt ?>"></div>             
              <div class="activity">              
             
              <a href="https://metar-taf.com/pt/<?php echo $icaoarpt ?>" id="metartaf-tqHtSN4U" style="font-size:18px; font-weight:500; color:#000; width:350px; height:265px; display:block">METAR Aeroporto Internacional do Recife/Guararapes-Gilberto Freyre</a>
               <script async defer crossorigin="anonymous" src="https://metar-taf.com/pt/embed-js/<?php echo $icaoarpt ?>?u=2660&layout=landscape&target=tqHtSN4U"></script>
                <br>
               <div class="checkwx-container" data-type="TAF" data-station="<?php echo $icaoarpt ?>"></div>
               

              </div>
            </div>            
          </div><!-- END METAR ORGN-->

        
             <!-- METAR DECODIFICADO
             <div class="card">     
            <div class="card-body overflow-auto">
                 
              <div class="activity">              
           
           

              </div>
            </div>            
          </div>END METAR DECODIFICADO-->


            </div>
          </div><!-- End News & Updates -->

        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'includes/footer.php'   ?>

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

  <!--    PEGAR URL DA PAGINA , ENCURTAR E COPIAR -->
  <script>


        let shorturlecopiar = () => {
          var data = {
    "domain":"9u6s.short.gy",
    "originalURL": "<?php echo $linkarpt ?>" ,
    "allowDuplicates":false }; 
 fetch('https://api.short.cm/links/public', {
    method: 'post',
    headers: {
      'accept': 'application/json',
      'Content-Type': 'application/json',
      'authorization': 'pk_9m9eseL2ArAG1ikD'
    },
    body: JSON.stringify(data)
  }) .then(function(response) {
        return response.json();
    }) 
    .then(function(data){  
     
            //O texto que será copiado
            const texto = "Informação <?php echo strtoupper($icaoarpt) ?>: " + data.shortURL;
            //Cria um elemento input (pode ser um textarea)
            let inputTest = document.createElement("input");
            inputTest.value = texto;
            //Anexa o elemento ao body
            document.body.appendChild(inputTest);
            //seleciona todo o texto do elemento
            inputTest.select();
            //executa o comando copy
            //aqui é feito o ato de copiar para a area de trabalho com base na seleção
            document.execCommand('copy');
            Swal.fire({ 
  icon: 'success',
  title: 'Link Copiado! Compartilhe.',
  showConfirmButton: false,
  timer: 2000
})
            //remove o elemento
            document.body.removeChild(inputTest);
          
          })};
 
    </script>
    <script>
$(window).on('load', function() {
    var $overlay = $('#overlay');
    $overlay.fadeOut(function() {
      $overlay.removeClass('loading');
    });
  });

  function getcartas() {


  }
    </script>
    
</body>

</html>