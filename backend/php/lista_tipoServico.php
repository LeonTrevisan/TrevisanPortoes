<?php

include __DIR__ . '/conexao.php';

$sql = "SELECT * FROM tb_tipo_servico ORDER BY tipo_servico ASC";
$results = $conn->query($sql);
$tiposServico = $results->fetch_all(MYSQLI_ASSOC);

foreach ($tiposServico as $tipo) {
    echo "<option value=\"" . htmlspecialchars($tipo['id_tipo_servico']) . "\">" . htmlspecialchars($tipo['tipo_servico']) . "</option>";
}

?>