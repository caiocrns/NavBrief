<?php 
$dep = strtoupper($_GET['dep']); 
$arr = strtoupper($_GET['arr']); 
$altn = strtoupper($_GET['altn']); 
$fl = $_GET['fl']; 

?>
<head>

  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
  <link href="assets/css/style.css" rel="stylesheet"> 

  </head>
  <!-- Mensagem carregamento -->
   <div id="overlay">
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <span class="loading-text">Consultando informações de: <?php echo $dep . " / " . $arr . " / " . $altn; ?></span>

    </div>
    <!-- Mensagem carregamento -->

<?php 
include 'lib/function.php'; 
include 'lib/conn.php';   
include 'lib/config.php'; 

// PEGAR URL DA PAGINA
$protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
$url = '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 

$linkarpt = $protocolo.$url;

?>

  <main id="main" style="display:none;" class="main">
   <?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; ?>
  <div class="pagetitle text-center">
      <h1> Briefing Aeródromos </h1>

      <nav class="p-3 border-bottom">
  <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
    <!-- Left Menu -->
    <div class="leftmenu mb-2 mb-md-0">
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">
          <form method="post" action="gera_pdf_airport.php" target="_blank">
            <?php include 'inputpdf_airport.php'; ?>
            <button type="submit" class="btn btn-outline-danger btn-sm">
              <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Briefing PDF
            </button>
          </form>
        </li>
      </ol>
    </div>

    <!-- Right Menu -->
    <div class="rightmenu d-flex flex-column flex-md-row align-items-center gap-2">
      <button id="copyLinkButton" class="btn btn-outline-success btn-sm">
        <i class="fa fa-link" aria-hidden="true"></i> Compartilhar
      </button>     
    </div>
  </div>
</nav>
      
      
    </div><!-- End Page Title -->   
   
    <section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <div class="col-lg-8">
      <div class="row">

        <?php
        // Array contendo os códigos ICAO dos aeródromos solicitados
        $icao_arpt_list = [$dep, $arr,$altn]; // Substitua com os valores reais dinamicamente

        foreach ($icao_arpt_list as $icaoarpt) { ?>
          <!-- Aeródromo Card -->
          <div class="card mb-4">
            <div class="card-body overflow-auto">
              <div class="text-center">
                <h5 class="card-title">
                  <img src="assets/img/aisweb.png" style="width:80px;height:50px;" alt="Logo">
                  Informações do Aeroporto - <?php echo strtoupper($icaoarpt); ?>
                </h5>
              </div>

              <div class="accordion accordion-flush" id="faq-group-<?php echo $icaoarpt; ?>">
<!-- CARTAS -->
<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" data-bs-target="#cartas-<?php echo $icaoarpt; ?>" type="button" data-bs-toggle="collapse">
      <h5 class="card-title"><i class="bi bi-map"></i> CARTAS </h5>
    </button>
  </h2>
  <div id="cartas-<?php echo $icaoarpt; ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-<?php echo $icaoarpt; ?>">
    <div class="accordion-body">
      <table class="table text-center" style="width: 70%;">
        <tr>
        <th><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pdc-<?php echo $icaoarpt; ?>"><i class="fa fa-folder-o"></i> PDC</button></th>
          <th><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adc-<?php echo $icaoarpt; ?>"><i class="fa fa-folder-o"></i> ADC</button></th>
          <th><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sid-<?php echo $icaoarpt; ?>"><i class="fa fa-folder-o"></i> SID</button></th>
          <th><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#iac-<?php echo $icaoarpt; ?>"><i class="fa fa-folder-o"></i> IAC</button></th>
          <th><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#star-<?php echo $icaoarpt; ?>"><i class="fa fa-folder-o"></i> STAR</button></th>
          <th><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vac-<?php echo $icaoarpt; ?>"><i class="fa fa-folder-o"></i> VAC</button></th>
        </tr>
      </table>
    </div>
  </div>
</div>

<!-- Modais -->
<?php foreach (['PDC', 'ADC', 'SID', 'IAC', 'STAR','VAC'] as $type): ?>
  <div class="modal fade" id="<?php echo strtolower($type) . '-' . $icaoarpt; ?>" tabindex="-1" aria-labelledby="<?php echo strtolower($type) . '-' . $icaoarpt; ?>-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Cartas Aeronáuticas - <?php echo $type; ?></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Carregando Cartas...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<!-- END CARTAS -->


                <!-- ROTAER -->
<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" data-bs-target="#rotaer-<?php echo $icaoarpt; ?>" type="button" data-bs-toggle="collapse">
      <h5 class="card-title"><i class="bi bi-journal-check"></i> ROTAER </h5>
    </button>
  </h2>
  <div id="rotaer-<?php echo $icaoarpt; ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-<?php echo $icaoarpt; ?>">
    <div class="accordion-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" id="rotaer-<?php echo $icaoarpt; ?>-content" role="alert">
        Buscando dados...
      </div>
    </div>
  </div>
</div>


                <!-- NOTAM -->
<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" data-bs-target="#notam-<?php echo $icaoarpt; ?>" type="button" data-bs-toggle="collapse">
      <h5 class="card-title"><i class="bx bx-world"></i> NOTAM </h5>
    </button>
  </h2>
  <div id="notam-<?php echo $icaoarpt; ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-<?php echo $icaoarpt; ?>">
    <div class="accordion-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" id="notam-<?php echo $icaoarpt; ?>-content" role="alert">
        Buscando dados...
      </div>
    </div>
  </div>
</div>
<!-- END NOTAM -->

<!-- INFOTEMP -->
<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" data-bs-target="#infotemp-<?php echo $icaoarpt; ?>" type="button" data-bs-toggle="collapse">
      <h5 class="card-title"><i class="bx bx-world"></i> INFOTEMP </h5>
    </button>
  </h2>
  <div id="infotemp-<?php echo $icaoarpt; ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-<?php echo $icaoarpt; ?>">
    <div class="accordion-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" id="infotemp-<?php echo $icaoarpt; ?>-content" role="alert">
        Buscando dados...
      </div>
    </div>
  </div>
</div>
<!-- END INFOTEMP -->

<!-- SUP AIP -->
<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" data-bs-target="#supaip-<?php echo $icaoarpt; ?>" type="button" data-bs-toggle="collapse">
      <h5 class="card-title"><i class="bx bx-world"></i> SUP AIP </h5>
    </button>
  </h2>
  <div id="supaip-<?php echo $icaoarpt; ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-<?php echo $icaoarpt; ?>">
    <div class="accordion-body">
      <div class="alert alert-primary alert-dismissible fade show overflow-auto" id="supaip-<?php echo $icaoarpt; ?>-content" role="alert">
        Buscando dados...
      </div>
    </div>
  </div>
</div>
<!-- END SUP AIP -->


              </div>
            </div>
          </div>
        <?php } ?>

      </div>
    </div>
    <!-- End Left side columns -->


    <script src="https://api.checkwx.com/widget?key=<?php echo $checkwx_key;?>
" type="text/javascript"></script>

        <!-- Right side columns -->
        <div class="col-lg-4">

       
        <nav class="p-3 border-bottom">
  <div class="container-fluid d-flex justify-content-center gap-2">
    <button class="btn btn-outline-primary btn-sm" onclick="openIframe('https://www.redemet.aer.mil.br/', 'Redemet')">
      <i class="fa fa-cloud" aria-hidden="true"></i> Redemet
    </button>
    <button class="btn btn-outline-danger btn-sm" onclick="openIframe('https://embed.windy.com/embed2.html?lat=-20.530&lon=-46.626&detailLat=-22.002&detailLon=-47.368&width=650&height=450&zoom=5&level=surface&overlay=clouds&product=ecmwf&menu=&message=&marker=&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=kt&metricTemp=%C2%B0C&radarRange=-1', 'Windy')">
      <i class="fa fa-cloud" aria-hidden="true"></i> Windy
    </button>
  </div>
</nav>

<!-- SweetAlert2 JS -->
<script>
  function openIframe(url, title) {
    Swal.fire({
      title: title,
      html: `<iframe src="${url}" frameborder="0" class="w-100" style="height: 90vh;"></iframe>`,
      showCloseButton: true,
      showConfirmButton: false,
      width: '110%',
      heightAuto: false
    });
  }
</script>
        

          <!-- METAR ORGN-->
          <div class="card">       
       <div class="card-body overflow-auto">
              <div class="checkwx-container" data-type="METAR" data-station="<?php echo $dep; ?>"></div>             
              <div class="activity">              
             
              
                <br>
               <div class="checkwx-container" data-type="TAF" data-station="<?php echo $dep; ?>"></div>
               
              </div>
            </div>            
          </div><!-- END METAR ORGN-->

           <!-- METAR DEST-->
           <div class="card">       
       <div class="card-body overflow-auto">
              <div class="checkwx-container" data-type="METAR" data-station="<?php echo $arr; ?>"></div>             
              <div class="activity">              
             
              
                <br>
               <div class="checkwx-container" data-type="TAF" data-station="<?php echo $arr ?>"></div>
               
              </div>
            </div>            
          </div><!-- END METAR DEST-->

           <!-- METAR ALTN-->
           <div class="card">       
       <div class="card-body overflow-auto">
              <div class="checkwx-container" data-type="METAR" data-station="<?php echo $altn ?>"></div>             
              <div class="activity">              
             
              
                <br>
               <div class="checkwx-container" data-type="TAF" data-station="<?php echo $altn ?>"></div>
               
              </div>
            </div>            
          </div><!-- END METAR ALTN-->

             <!-- SIGWX-WINDS SATELITE-->
          <div class="card">       
       <div class="card-body overflow-auto">
       <?php include 'includes/menu_cartasmeteo.php'; ?>             
            </div>            
          </div><!-- END SIGWX -->
          
    <!-- DONATION -->
    <div class="card">            
  <div class="card-body overflow-auto" style="text-align:center">                     
    <p class="card-title" style="font-size: 14px;"> Ajude o NavBrief a manter os seus servidores ativos a fim de prover o melhor planejamento para o seu voo.</p>
    <a href="donation.php" target="_blank"  class="btn btn-outline-success btn-sm"><b> Ajude o NavBrief! </b><i class="fa-brands fa-pix"></i></a>
                  
  </div>            
</div>
<!-- END DONATION -->
            </div>
          </div><!-- End News & Updates -->

      

        </div><!-- End Right side columns -->


  </div>
</section>

    
  <!-- ======= Footer ======= -->
    <?php include 'includes/footer.php'   ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  </main><!-- End #main -->




  

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/scripts-navbrief.js"></script>

  <!--    PEGAR URL DA PAGINA , ENCURTAR E COPIAR -->
    
  <script>
     
    document.addEventListener("DOMContentLoaded", function() {
        // URL original em PHP
        var link = "<?php echo $linkarpt ?>";
        
        // Parâmetros para a solicitação da API TinyURL
        var data = {
            "url": link
        };

        // Realiza a solicitação POST para a API TinyURL
        fetch('https://api.tinyurl.com/create', {
            method: 'POST',
            headers: {
                'accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'Bearer H84OxOsJGXPvKLdBqW9FGrlONwyURl19Ha5cb97mjkCMCrXBmBfZHe9EpgQe'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.data && data.data.tiny_url) {
                var shortLink = data.data.tiny_url;
                var copyButton = document.getElementById("copyLinkButton");

                if (copyButton) {
                    // Define o evento de clique para o botão "Copiar Link"
                    copyButton.onclick = function() {
                        // Cria um elemento input temporário para selecionar e copiar o texto
                        let inputTemp = document.createElement("input");
                        inputTemp.value = "Briefing de <?php echo htmlspecialchars($dep . '-' . $arr . '-' . $altn, ENT_QUOTES, 'UTF-8'); ?>: " + shortLink;
                        document.body.appendChild(inputTemp);

                        // Seleciona e copia o texto
                        inputTemp.select();
                        document.execCommand('copy');

                        // Remove o elemento temporário
                        document.body.removeChild(inputTemp);

                        // Exibe mensagem de sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'Link Copiado! Compartilhe.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    };
                } else {
                    console.error("Botão de cópia não encontrado.");
                }
            } else {
                throw new Error("Falha ao obter a URL encurtada.");
            }
        })
        .catch(function(error) {
            // Exibe mensagem de erro
            Swal.fire({
                icon: 'error',
                title: 'Erro ao encurtar o link!',
                text: error.message,
                showConfirmButton: false,
                timer: 3000
            });
        });
    });

    </script>
    
    <script>
      // Função que simula o progresso de carregamento
      var interval; // Variável para armazenar o intervalo da simulação

function simulateLoading() {
    var progressBar = document.getElementById('progress-bar');
    var progress = 0;

    interval = setInterval(function() {
        if (progress < 80) { // Limita o progresso a 90% enquanto a página carrega
            progress += Math.random() * 10; // Simula o progresso aleatório
            progressBar.style.width = progress + '%';
            progressBar.innerHTML = Math.floor(progress) + '%'; // Exibe a porcentagem dentro da barra
        }
    }, 100); // Atualiza a barra de progresso a cada 100ms
}

// Simula a finalização do carregamento ao completar o carregamento da página
window.onload = function() {
    var progressBar = document.getElementById('progress-bar');
    var overlay = document.getElementById('overlay');
    var conteudo = document.getElementById('main');

    // Faz o progresso ir direto para 100%
    progressBar.style.width = '100%';
    progressBar.innerHTML = '100%';

    // Aguarda um breve momento e então esconde o overlay
    setTimeout(function() {
        clearInterval(interval); // Interrompe a simulação de progresso
        overlay.style.display = 'none'; // Esconde o overlay
        conteudo.style.display = 'block'; // Exibe o conteúdo da página
    }, 500); // Adiciona um pequeno atraso antes de ocultar o overlay
};

// Inicia o carregamento assim que a página é requisitada
simulateLoading();

      </script>
    <script> 
    function fetchData(endpoint, elementId) {
        document.getElementById(elementId).innerHTML = 'Buscando dados...';
        fetch(endpoint)
            .then(response => response.text())
            .then(data => {
                document.getElementById(elementId).innerHTML = data;
            })
            .catch(error => {
                document.getElementById(elementId).innerHTML = 'Erro ao buscar dados.';
                console.error('Erro ao buscar dados:', error);
            });
    }
    </script>
    <script>
  document.addEventListener("DOMContentLoaded", function () {
    // Lista de aeródromos
    const aerodromes = <?php echo json_encode($icao_arpt_list); ?>;

    // Adiciona eventos para cada aeródromo dinamicamente
    aerodromes.forEach(icaoarpt => {
      // ROTAER
      document.getElementById(`rotaer-${icaoarpt}`).addEventListener('show.bs.collapse', function () {
        fetchData(`async/getrotaer.php?location=${icaoarpt}`, `rotaer-${icaoarpt}-content`);
      });

      // NOTAM
      document.getElementById(`notam-${icaoarpt}`).addEventListener('show.bs.collapse', function () {
        fetchData(`async/getnotam.php?location=${icaoarpt}`, `notam-${icaoarpt}-content`);
      });

      // INFOTEMP
      document.getElementById(`infotemp-${icaoarpt}`).addEventListener('show.bs.collapse', function () {
        fetchData(`async/getinfotemp.php?location=${icaoarpt}`, `infotemp-${icaoarpt}-content`);
      });
        // SUPAIP
      document.getElementById(`supaip-${icaoarpt}`).addEventListener('show.bs.collapse', function () {
        fetchData(`async/getsupaip.php?location=${icaoarpt}`, `supaip-${icaoarpt}-content`);
      });
      document.querySelectorAll('button[data-bs-toggle="modal"]').forEach(button => {
    button.addEventListener('click', function () {
        const targetId = button.getAttribute('data-bs-target').substring(1); // Remove o '#' do ID
        const [type, icaoarpt] = targetId.split('-'); // Exemplo: 'pdc-SBGR' -> ['pdc', 'SBGR']
        const modalBody = document.querySelector(`#${targetId} .modal-body`);

        // Requisição Fetch para carregar as cartas no modal
        fetch(`async/getcartas.php?location=${icaoarpt}&type=${type.toUpperCase()}`)
            .then(response => response.text())
            .then(data => {
                modalBody.innerHTML = data; // Insere o conteúdo retornado no corpo do modal
            })
            .catch(error => {
                modalBody.innerHTML = `<p class="text-danger">Erro ao carregar cartas: ${error.message}</p>`;
            });
    });
});
    });
  });

  // Função para buscar dados
  function fetchData(endpoint, elementId) {
    const element = document.getElementById(elementId);
    element.innerHTML = 'Buscando dados...';
    fetch(endpoint)
      .then(response => response.text())
      .then(data => {
        element.innerHTML = data;
      })
      .catch(error => {
        element.innerHTML = 'Erro ao buscar dados.';
        console.error('Erro ao buscar dados:', error);
      });
  }
</script>

<script>
function openViewer(url, nome) {
        // Remove o modal anterior se já existir
        let existingModal = document.getElementById("viewerModal");
        if (existingModal) {
            existingModal.remove();
        }
    
        // Criar um identificador único para evitar cache no iframe
        let uniqueUrl = url + "?nocache=" + new Date().getTime();
    
        // Criar o modal dinamicamente
        let modalHTML = `
            <div class="modal fade" id="viewerModal" tabindex="-1" aria-labelledby="viewerModalLabel" aria-hidden="true" 
                style="z-index: 9999; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="viewerModalLabel">Visualizando: ${nome}</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <iframe id="viewerFrame" src="https://docs.google.com/gview?url=${encodeURIComponent(uniqueUrl)}&embedded=true" 
                                width="100%" height="600px" style="border: none;"></iframe>
                        </div>
                        <div class="modal-footer">
                            <a id="downloadCarta" href="${url}" target="_blank" class="btn btn-primary">
                                <i class="fa fa-download"></i> Baixar Carta
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    
        // Adiciona o modal ao body
        document.body.insertAdjacentHTML("beforeend", modalHTML);
    
        // Aguarda um pequeno tempo para garantir que o modal seja carregado corretamente
        setTimeout(() => {
            var viewerModal = new bootstrap.Modal(document.getElementById("viewerModal"), { backdrop: 'static' });
            viewerModal.show();
        }, 100);
    }
  
  </script>

    
</body>

</html>