<?php

include __DIR__ . '/conexao.php';

$sql = "SELECT * From tb_forma_pagamento ORDER BY id_forma_pagamento ASC";
$results = $conn->query($sql);
$pagamentos = $results->fetch_all(MYSQLI_ASSOC);

function formaPagamento($pagamentos) {
foreach ($pagamentos as $value) {
    echo "<option id=\"formaPag\" value=\"" . htmlspecialchars($value['id_forma_pagamento']) . "\">
        " . htmlspecialchars($value['forma_pagamento']) . "
    </option>";
    }
}


?>