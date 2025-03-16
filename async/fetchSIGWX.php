<?php
if (isset($_GET['fetch_sigwx'])) {
    $sigwxUrl = 'https://api-redemet.decea.mil.br/produtos/sigwx?api_key=MwpGmMvXuFe0AIl8gA3FbIOBpG75wiN2w0haSvso';

    // Faz a requisição à API
    $sigwxResponse = file_get_contents($sigwxUrl);

    // Verifica se houve erro na requisição
    if ($sigwxResponse === false) {
        http_response_code(500);
        echo "Erro ao buscar os dados da API SIGWX.";
        exit;
    }

    // Retorna a resposta da API como texto
    echo trim($sigwxResponse);
    exit;
}
?>
