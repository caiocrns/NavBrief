<?php include 'includes/header.php';
      include 'includes/sidebar.php';
      include 'lib/conn.php';  
      include 'lib/function.php'; 


 $sql = "SELECT DISTINCT icao_aeronave FROM aeronaves";          // buscar anv banco dados
 $queryanv = mysqli_query($conexao,$sql);
 $lista_aeronaves = mysqli_fetch_assoc($queryanv);


 
 ?>
 <style>
       
        
        .payment-button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 14px;
            border: none;
            cursor: pointer;
            margin: 10px;
            width: 200px;
            text-align: center;
            font-size: 18px;
            border-radius: 5px;
        }
        .pix-button {
            background-color: orange;
            color: white;
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            margin: 10px;
            width: 200px;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
        }
        .pix-button:hover {
            background-color: #F5800B;
        }
        .payment-details {
            display: none;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .payment-button:hover {
            background-color: #45a049;
        }
        
        .show {
            display: block !important;
        }
    </style>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1></h1>   
  
</div>

 <style>
  .p2 { font-size: 16px;
  
  }
 
</style>
 <section class="section">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-6">

      
          <div class="card">
            <div class="card-body">
              
              <div class= "card-title text-center" >
              <a><img src="assets/img/nbicon.png" style="width:170px;height:140px" alt=""></a><p></p><h2><b style="font-size:22px"> Faça uma doação e ajude o NavBrief a se manter ativo!</b></h2><h6></h6>
                <h4 class="alert-heading"></h4>
                <p class="p2"> Caros pilotos,</p><p class="p2"> O NavBrief está dedicado a fornecer valiosos recursos para o planejamento da sua navegação aérea, de forma totalmente gratuita.

Ao fazer uma doação, você estará ajudando o NavBrief a manter os servidores ativos a fim de disponibilizar um planejamento otimizado para o seu voo!</p>
                
                <hr>
                <button class="payment-button" onclick="toggleCollapse(1)">R$ 5</button>
    <div id="collapse1" class="payment-details">
    <button onclick="shorturlecopiar(this)" data-texto="00020101021126920014br.gov.bcb.pix0136a5511a7d-543d-4c35-83f7-b2462ca66f840230Obrigado por ajudar o NavBrief52040000530398654045.005802BR5919CAIO R N DOS SANTOS6005NATAL62070503***630417AF" class="pix-button">Pix Copie e Cole</button>        
        <img src="assets/img/pix5.jpeg" style="width: 250px; height: 300px;">
    </div>

    <button class="payment-button" onclick="toggleCollapse(2)">R$ 10</button>
    <div id="collapse2" class="payment-details">
    <button onclick="shorturlecopiar(this)" data-texto="00020101021126920014br.gov.bcb.pix0136a5511a7d-543d-4c35-83f7-b2462ca66f840230Obrigado por ajudar o NavBrief520400005303986540510.005802BR5919CAIO R N DOS SANTOS6005NATAL62070503***63049B73" class="pix-button">Pix Copie e Cole</button>
        <img src="assets/img/pix10.jpeg"  style="width: 250px; height: 300px;">
    </div>

    <button class="payment-button" onclick="toggleCollapse(3)">R$ 15</button>
    <div id="collapse3" class="payment-details">
    <button onclick="shorturlecopiar(this)" data-texto="00020101021126920014br.gov.bcb.pix0136a5511a7d-543d-4c35-83f7-b2462ca66f840230Obrigado por ajudar o NavBrief520400005303986540515.005802BR5919CAIO R N DOS SANTOS6005NATAL62070503***63045B40" class="pix-button">Pix Copie e Cole</button>
        <img src="assets/img/pix15.jpeg"  style="width: 250px; height: 300px;">
    </div>

    <button class="payment-button" onclick="toggleCollapse(4)">R$ 20</button>
    <div id="collapse4" class="payment-details">
    <button onclick="shorturlecopiar(this)" data-texto="00020101021126920014br.gov.bcb.pix0136a5511a7d-543d-4c35-83f7-b2462ca66f840230Obrigado por ajudar o NavBrief520400005303986540520.005802BR5919CAIO R N DOS SANTOS6005NATAL62070503***630432A8" class="pix-button">Pix Copie e Cole</button>
        <img src="assets/img/pix20.jpeg"  style="width: 250px; height: 300px;">
    </div>

    <div class="text-center">
      <button class="payment-button" onclick="toggleCollapse(5)">Outro Valor</button>
    <div id="collapse5" class="payment-details">
    <button onclick="shorturlecopiar(this)" data-texto="00020101021126920014br.gov.bcb.pix0136a5511a7d-543d-4c35-83f7-b2462ca66f840230Obrigado por ajudar o NavBrief5204000053039865802BR5919CAIO R N DOS SANTOS6005NATAL62070503***630443D3" class="pix-button">Pix Copie e Cole</button>
        <img src="assets/img/pixoutrovalor.jpeg"  style="width: 250px; height: 300px;">
    </div>
</div>

    <script>
        function toggleCollapse(index) {
            var collapseId = 'collapse' + index;
            var collapseElement = document.getElementById(collapseId);
            collapseElement.classList.toggle('show');
        }
    </script>
    <script>

  function shorturlecopiar(button) {  
            // Obter o texto do atributo data-texto do botão clicado
            const texto = button.getAttribute("data-texto");

            // Cria um elemento input (pode ser um textarea)
            let inputTest = document.createElement("input");
            inputTest.value = texto;
            // Anexa o elemento ao body
            document.body.appendChild(inputTest);
            // Seleciona todo o texto do elemento
            inputTest.select();
            // Executa o comando copy
            // Aqui é feito o ato de copiar para a área de trabalho com base na seleção
            document.execCommand('copy');
            Swal.fire({ 
  icon: 'success',
  title: 'Código PIX copiado com sucesso!',
  showConfirmButton: false,
  timer: 2000
})
            // Remove o elemento
            document.body.removeChild(inputTest);
        }
</script>

           
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