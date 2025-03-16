<?php

$rota_nao_encontrada = "Rota ainda não encontrada no banco de dados. Planeje!";   //mensagem de rota não encontrada no banco de dados

/* --------------------------------------------------------- */

include_once '../lib/conn.php';

function count_from_db($conexao,$tabela) {

  $resultado = $conexao->query("SELECT COUNT(id) AS total FROM $tabela");
    
    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        return $row["total"];    
    } else {
        echo "Não há";
    }
    $conexao->close();
}

function select_rota($origem,$destino,$espaco_aereo) {
    include '../lib/conn.php';
 global $rota_nao_encontrada;

    // Executar a consulta SQL para buscar a rota
    $sql = "SELECT rota FROM rotas WHERE origem = '$origem' AND destino = '$destino' AND espaco_aereo = '$espaco_aereo'";
    $result = $conexao->query($sql);
    
    if ($result->num_rows > 0) {
       
        $row = $result->fetch_assoc();
        $rota = $row['rota'];
        return  $rota ;
    } else {
        return $rota_nao_encontrada;
    }
    $conexao->close();
}

 
function verifica_rota($rota) {
    include '../lib/conn.php';
    global $rota_nao_encontrada;
if ($rota == $rota_nao_encontrada) {
  echo "<br>";

} else {


// Transforma a string em um array de palavras
$palavras = explode(" ", $rota);

// Remove a palavra "dct" do array
$palavrafiltrada = array_filter($palavras, function ($palavra) {
    return strlen($palavra) !== 3;
});


$todasPalavrasExistem = true;

foreach ($palavrafiltrada as $palavra) {
    // Escapa a palavra para evitar injeção de SQL
    $palavra_escapada = $conexao->real_escape_string($palavra);

    // Consulta SQL para verificar se a palavra existe no banco de dados
    $sql = "SELECT ident FROM waypoint_aisweb WHERE ident = '$palavra_escapada'";

    // Executa a consulta
    $result = $conexao->query($sql);

    // Verifica se há resultados
    $existePalavra = ($result->num_rows > 0);
  
// Atualiza a variável $todasPalavrasExistem se uma palavra não existir
if (!$existePalavra) {
    $todasPalavrasExistem = false;
}
}

// Exibe a mensagem se todas as palavras existirem
if ($todasPalavrasExistem) {
echo "<span style='color: green; font-size: 10px;'>AIRAC Atualizado</span>";
}
else{
    echo"<span style='color: red; font-size: 10px;'>AIRAC Não Atualizado</span>";
}

}
$conexao->close();
}

?>