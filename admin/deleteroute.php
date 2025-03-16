<?php 

if(!empty($_GET['id']))
 {
include_once('../lib/conn.php');

$id = $_GET['id'];


$sqlselect = "SELECT * FROM rotas WHERE id=$id";  
$result = $conexao->query($sqlselect);

if($result->num_rows > 0)

{

  $sqldelete = "DELETE FROM rotas WHERE id=$id";
  $deleteroute = $conexao->query($sqldelete);
}
}
  header('location: rotas_db.php?rotadeletada=sim');
  
?>
