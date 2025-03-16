<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.js"></script>

<div class="text-center">
  <img src="assets/img/aisweb.png" style="width:120px;height:80px;text-align:center">
  <img src="https://aviationweather.gov/assets/NWS_logo-BZtavOX9.svg" style="width:120px;height:80px;text-align:center">
</div>
<h5 class="card-title" style="text-align: center;">SIGWX / Wind Aloft / Imagem Satélite<span></span></h5>
             
            <label for="tipo-carta">Carta:</label>
<select class="form-select" id="tipo-carta" >   
<option value="" disabled selected>Selecione</option>
    <option value="sigwx">SIGWX</option>
    <option value="wind">Carta de Vento</option>
</select>

<label for="nivel-voo">Nível de Voo:</label>
<select class="form-select" id="nivel-voo">
    <option value="">Selecione</option>
</select>

<label for="previsao">Previsão:</label>
    <select class="form-select" id="previsao">
        <option value="">Selecione</option>
       
    </select>
    <?php 
    $sigwxImageUrl = getsigwx();
    ?>
   
    <script>
    const tipoCarta = document.getElementById("tipo-carta");
    const nivelVoo = document.getElementById("nivel-voo");
    const previsao = document.getElementById("previsao");

    // Configuração das opções para cada tipo de carta
    const opcoes = {
      sigwx: {
        nivelVoo: [
            { value: "hi", text: "FL250-FL600" },
            { value: "api_redemet", text: "SFC-FL250" }
        ],
        previsao: [
            { value: "F24", text: "+24hr" }
        ]
    },
        wind: {
            nivelVoo: [
                { value: "050", text: "FL050" },
                { value: "100", text: "FL100" },
                { value: "180", text: "FL180" },
                { value: "240", text: "FL240" },
                { value: "300", text: "FL300" },
                { value: "340", text: "FL340" },
                { value: "390", text: "FL390" },
                { value: "450", text: "FL450" },
                { value: "630", text: "FL630" }
            ],
            previsao: [
                { value: "F06", text: "+6hr" },
                { value: "F12", text: "+12hr" },
                { value: "F18", text: "+18hr" },
                { value: "F24", text: "+24hr" }
            ]
        }
    };

    // Função para atualizar as opções de um `<select>`
    const atualizarOpcoes = (selectElement, opcoes) => {
        selectElement.innerHTML = ""; // Limpa as opções atuais
        opcoes.forEach(opcao => {
            const optionElement = document.createElement("option");
            optionElement.value = opcao.value;
            optionElement.textContent = opcao.text;
            selectElement.appendChild(optionElement);
        });
    };

    // Atualiza os menus com base no tipo de carta selecionado
    const atualizarMenus = () => {
        const tipo = tipoCarta.value;
        atualizarOpcoes(nivelVoo, opcoes[tipo].nivelVoo);
        atualizarOpcoes(previsao, opcoes[tipo].previsao);
    };

    // Eventos para atualizar os menus ao alterar o tipo de carta
    tipoCarta.addEventListener("change", atualizarMenus);

    // Inicializa as opções ao carregar a página
    atualizarMenus();   
</script>


  <p></p>
    <button class="btn btn-sm btn-outline-success" id="obter-carta"><i class="fa fa-cloud" aria-hidden="true"></i> Obter Carta</button>
    <hr>
    <label for="tipo-satelite">Tipo de imagem satélite:</label>
<select class="form-select" id="tipo-satelite">   
    <option value="" disabled selected>Selecione</option>
    <option value="realcada">Realçada</option>
    <option value="ir">Infra-vermelho</option>
    <option value="vis">Visual</option>
</select>
<p></p>
<button class="btn btn-sm btn-outline-success" id="btn-satellite"><i class="fa fa-rocket" aria-hidden="true"></i> Obter imagem satélite</button>


<script>
    document.getElementById("btn-satellite").addEventListener("click", async function () {
        const tipoSatelite = document.getElementById("tipo-satelite").value;

        if (!tipoSatelite) {
            Swal.fire({
                title: "Erro",
                text: "Por favor, selecione um tipo de imagem de satélite.",
                icon: "warning",
                confirmButtonText: "Fechar"
            });
            return;
        }

        try {
            const response = await fetch('async/fetchSatelite.php?fetch_satellite=' + tipoSatelite);

            if (!response.ok) {
                throw new Error("Erro ao buscar a imagem de satélite.");
            }

            const data = await response.json();

            if (!data.path) {
                throw new Error("Dados do satélite não disponíveis");
            }

            // Exibe o mapa com as imagens sobrepostas e traça rotas
            showMapWithOverlayAndRoutes(data.path, data.coordinates, data.timestamp);
        } catch (error) {
            console.error("Erro:", error);
            Swal.fire({
                title: "Erro",
                text: error.message,
                icon: "error",
                confirmButtonText: "Fechar"
            });
        }
    });

    function showMapWithOverlayAndRoutes(apiImagePath, coordinates, timestamp) {
        Swal.fire({
            title: "Imagem de Satélite",
            html: `
                <div id="map" style="width: 100%; height: 500px;"></div>
                <p style="margin-top: 10px; font-size: 9px">Última atualização (UTC): ${timestamp}</p>
            `,
            width: "100%",
            showCloseButton: true,
            didOpen: () => {
                // Inicializa o mapa Leaflet centralizado no Brasil
                const map = L.map('map', {
    crs: L.CRS.EPSG4326
}).setView([-14.235, -51.9253], 4);

                // Adiciona o tile base do mapa (exemplo OpenStreetMap)
                L.tileLayer.wms('https://basemap.nationalmap.gov/arcgis/services/USGSImageryOnly/MapServer/WMSServer', {
    layers: '0',
    format: 'image/png',
    transparent: true,
    version: '1.3.0',
    crs: L.CRS.EPSG4326,
    attribution: 'USGS Imagery Only'
}).addTo(map);
                
                // Defina os limites da imagem com base nas coordenadas fornecidas
                const bounds = [
                    [coordinates.lat_min, coordinates.lon_min],
                    [coordinates.lat_max, coordinates.lon_max]
                ];

                // Adiciona a imagem base como camada sobreposta
                //L.imageOverlay('assets/img/satelite2.png', bounds, {
                    //opacity: 1, // Base completamente visível
                    //zIndex: 1
                //}).addTo(map);

                // Adiciona a imagem da API como camada sobreposta
                L.imageOverlay(apiImagePath, bounds, {
                    opacity: 0.7, // Leve transparência para sobrepor
                    zIndex: 2
                }).addTo(map);            
             
            }
        });
    }    
</script>


  <script>
    document.getElementById('obter-carta').addEventListener('click', function () {
        // Obtendo os valores selecionados
        const tipoCarta = document.getElementById('tipo-carta').value;
        const nivelVoo = document.getElementById('nivel-voo').value;
        const previsao = document.getElementById('previsao').value;
        let linkCarta;

        // Verifica se algum campo está vazio
        if (!tipoCarta || !nivelVoo || !previsao) {
            Swal.fire({
                title: "Erro",
                text: "Por favor, selecione todos os campos para obter a carta.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }

        // Verifica se o tipo é "sigwx" e o nível é "api_redemet"
        if (tipoCarta === "sigwx" && nivelVoo === "api_redemet") {
            linkCarta = `<?php echo $sigwxImageUrl; ?>`; // Substitui o link pelo valor PHP
        } else {
            // Monta o link padrão da carta
            linkCarta = `https://aviationweather.gov/data/products/fax/${previsao}_${tipoCarta}_${nivelVoo}_a.gif`;
        }

        // Exibe o visualizador com Viewer.js
        showImageViewer(linkCarta);
    });

    function showImageViewer(imagePath) {
        // Cria o contêiner de visualização
        const viewerContainer = document.createElement('div');
        viewerContainer.id = 'image-viewer-container';
        viewerContainer.style.display = 'none';
        viewerContainer.innerHTML = `
            <img id="viewer-image" src="${imagePath}" alt="Carta de Aviação" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        `;
        document.body.appendChild(viewerContainer);

        // Inicializa o Viewer.js
        const viewer = new Viewer(viewerContainer, {
            inline: false,
            navbar: false,
            toolbar: {
                zoomIn: true,
                zoomOut: true,
                oneToOne: true,
                reset: true,
                rotateLeft: true,
                rotateRight: true,
                flipHorizontal: true,
                flipVertical: true
            },
            hidden: () => {
                // Remove o contêiner ao fechar o Viewer.js
                viewerContainer.remove();
            }
        });

        // Mostra o visualizador
        viewer.show();
    }
</script>
