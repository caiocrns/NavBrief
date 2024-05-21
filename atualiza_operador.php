<?php
include('lib/conn.php');
// Ensure that the 'operador' parameter is provided and not empty
if (isset($_GET['operador']) && !empty($_GET['operador'])) {
    $selectedOperador = $_GET['operador'];
    $selectedAeronave = $_GET['icao_aeronave'];

    // Prepare the SQL statement to fetch the corresponding "Matrículas" based on the selected "Operador"
    $sql = "SELECT id,icao_aeronave,matricula FROM aeronaves WHERE operador = ? and icao_aeronave = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ss', $selectedOperador, $selectedAeronave);
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch the data as an associative array
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Close the statement and database connection
    $stmt->close();
    $conexao->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // If 'operador' parameter is missing or empty, return an empty JSON array
    echo json_encode([]);
}
?>
