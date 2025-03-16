<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link collapsed" href="home.php">
      <i class="bi bi-grid"></i>
      <span>Planejar</span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="history.php">
      <i class="bi bi-table"></i>
      <span>Histórico </span>
    </a>
  </li>   

  <li class="nav-item">
    <a class="nav-link collapsed" href="aeronaves.php">
      <i class="bi bi-airplane"></i>
      <span> Aeronaves </span>
    </a>
  </li>  
 
    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span> Utilidades </span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="https://redemet.decea.mil.br/old/?i=blog&y=2016&m=10" target="_blank">
              <i class="bi bi-circle"></i><span> Leitura cartas SIGWX</span>
            </a>
          </li>
            <li>
            <a href="https://aisweb.decea.mil.br/?i=abreviaturas" target="_blank">
              <i class="bi bi-circle"></i><span> Abreviaturas AISWEB</span>
            </a>
          </li>
          <li>
            <a href="https://www.autorouter.aero/wiki/gramet/" target="_blank">
              <i class="bi bi-circle"></i><span> GRAMET Interpretação</span>
            </a>
          </li>
          <li>
            <a href="aeroinfo.php">
              <i class="bi bi-circle"></i><span> AeroInfo WF</span>
            </a>
          </li>
           <li>
            <a href="https://aisweb.decea.mil.br/eaip" target="_blank">
              <i class="bi bi-circle"></i><span> E-AIP</span>
            </a>
          </li>
         
         
        </ul>
      </li><!-- End Charts Nav -->

  <hr>
  
    <li class="nav-item">
    <a class="nav-link collapsed">
      <i class="bi bi-cloud-rain"></i>
      <span> CONSULTAR  </span>
      <i style="margin-left:5px" class="bx bx-world"></i>
    </a>
    <form class="search-form d-flex align-items-center" method="get" action="airport.php">
    <input type="text" name="infoarpt" placeholder="ICAO" style="text-transform: uppercase" title="Insira o código ICAO do aeroporto" required>
    <button type="submit" class="btn btn-sm btn-info" title="Search"><i class="bi bi-search"></i></button>
  </form>
  </li>    
  <li class="nav-item">    
      <i class="bi bi-cloud-rain"></i>
      <a 
  class="btn btn-sm btn-outline-primary" 
  href="#" 
  data-bs-toggle="modal" 
  data-bs-target="#grametModal">
  Gerar GRAMET
</a>      
      <i class="bi bi-cloud-rain"></i>
  </li>    
  <hr>
  <a href="donation.php" class="btn btn-outline-success"><b> Faça uma doação! </b><i class="fa-brands fa-pix"></i></a>
  
   
  
</aside><!-- End Sidebar-->

<!-- Modal -->
<div 
  class="modal fade" 
  id="grametModal" 
  tabindex="-1" 
  aria-labelledby="grametModalLabel" 
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="grametModalLabel">Gerar GRAMET (PDF)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" id="planner" style="text-transform: uppercase;" method="GET" action="gera_gramet.php">
          <style>
            /* Custom styles for reducing font size and input width */
            .form-control-sm {
              width: 170px;
            }
          </style>

          <div class="col-auto">
            <div class="form-floating">
              <input 
                type="text" 
                class="form-control form-control-sm" 
                name="origem" 
                placeholder="Origem" 
                style="text-transform: uppercase"
                minlength="4" 
                maxlength="4" 
                required>
              <label for="origem">Origem</label>
            </div>
          </div>

          <div class="col-auto">
            <div class="form-floating">
              <input 
                type="text" 
                class="form-control form-control-sm" 
                name="destino" 
                placeholder="Destino" 
                style="text-transform: uppercase"
                minlength="4" 
                maxlength="4" 
                required>
              <label for="destino">Destino</label>
            </div>
          </div>

          <div class="col-auto">
            <div class="form-floating">
            <input type="number" name="niveldevoo" placeholder="Nivel de Voo" style="text-transform: uppercase; width: 140px;" min="000" max="999" class="form-control mb-2" required>
              <label for="niveldevoo">Nível de voo 
                <span class="text-muted" style="font-size:10px;">
                </span>
              </label>
            </div>
          </div>

          <div class="col-auto">
            <div class="form-floating">
              <input 
                type="datetime-local" 
                class="form-control" 
                name="datetime" 
                placeholder="Data do voo" 
                required>
              <label for="datetime">Data e Hora (UTC)</label>
            </div>
          </div>

          <div class="col-auto">
            <div class="form-floating">
              <input 
                type="number"
                style="width: 120px;" 
                class="form-control" 
                name="velocidade" 
                min="000" max="999"
                placeholder="GS (Kts)" 
                required>
              <label for="velocidade">GS (Kts)</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      <a href="donation.php" style="position: absolute; left: 5px;" class="btn btn-outline-success btn-sm">
      <b>Ajude!</b> <i class="fa-brands fa-pix"></i>
    </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="submit" form="planner" class="btn btn-success">Gerar GRAMET</button>
      </div>
    </div>
  </div>
</div>
