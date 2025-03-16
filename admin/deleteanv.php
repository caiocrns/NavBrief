<?php 

if(!empty($_GET['id']))
 {
include_once('../lib/conn.php');

$id = $_GET['id'];

$sqlselect = "SELECT * FROM aeronaves WHERE id=$id";  
$result = $conexao->query($sqlselect);

if($result->num_rows > 0)

{

  $sqldelete = "DELETE FROM aeronaves WHERE id=$id";
  $resultdelete = $conexao->query($sqldelete);

  header('location: aeronaves_db.php?anvdeletada=sim');
}
}
 
?>