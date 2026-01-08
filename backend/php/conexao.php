<?php
$conn = new mysqli("localhost", "root", "", "bd_trevisanportoes");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>