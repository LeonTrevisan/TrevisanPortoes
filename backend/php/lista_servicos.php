<?php
include 'conexao.php';

$sql = "SELECT 
    s.id_servico, 
    s.data_hora, 
    c.nome as cliente,
    ts.tipo_servico,
    p.valor,
    sp.status_pagamento
FROM tb_servico s
LEFT JOIN tb_cliente c ON s.id_cliente = c.id_cliente
LEFT JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
LEFT JOIN tb_pagamento p ON s.id_servico = p.id_servico
LEFT JOIN tb_status_pagamento sp ON p.id_status = sp.id_status
ORDER BY s.data_hora DESC";

$results = $conn->query($sql);
if (!$results) {
    die("Erro na query: " . $conn->error);
}

$servicos = $results->fetch_all(MYSQLI_ASSOC);

foreach ($servicos as $servico) {
    $data_formatada = date('d/m/Y', strtotime($servico['data_hora']));
    
    // Determinar a classe de status
    $status_class = 'status-agendado';
    if (strtotime($servico['data_hora']) < time()) {
        $status_class = 'status-concluido';
    }
    
    echo "
    <tr>
        <td>" . htmlspecialchars($data_formatada) . "</td>
        <td>" . htmlspecialchars($servico['cliente']) . "</td>
        <td>" . htmlspecialchars($servico['tipo_servico']) . "</td>
        <td>R$ " . number_format($servico['valor'], 2, ',', '.') . "</td>
        <td><span class=\"status " . $status_class . "\">" . htmlspecialchars($servico['status_pagamento']) . "</span></td>
        <td>
            <div class='action-buttons'>
                <button class='btn btn-primary btn-small' onclick=\"editarServico(" . intval($servico['id_servico']) . ")\">Editar</button>
                <button class='btn btn-danger btn-small' onclick=\"confirmarExclusao(" . intval($servico['id_servico']) . ", 'servico')\">Excluir</button>
            </div>
        </td>
    </tr>
    ";
}
?>
