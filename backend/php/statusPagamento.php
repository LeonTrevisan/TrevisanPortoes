<?php

include __DIR__ . '/conexao.php';

$sql = "SELECT * FROM tb_status_pagamento ORDER BY id_status ASC";
$results = $conn->query($sql);
$pagamentos = $results->fetch_all(MYSQLI_ASSOC);

function statusPagamento($pagamentos) {
foreach ($pagamentos as $value) {
    echo "<option id=\"pagamentoStats\" value=\"" . htmlspecialchars($value['id_status']) . "\">
        " . htmlspecialchars($value['status_pagamento']) . "
    </option>";
    }
}
?>