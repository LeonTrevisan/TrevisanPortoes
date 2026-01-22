<?php
include 'conexao.php';

// Receber o valor do filtro via GET ou POST
$filtro = isset($_GET['filtro-periodo']) ? $_GET['filtro-periodo'] : (isset($_POST['filtro-periodo']) ? $_POST['filtro-periodo'] : '30');

// Definir a data inicial e final baseada no filtro
$data_inicio = null;
$data_fim = date('Y-m-d');
$where_clause = '';

if (preg_match('/^\d{4}$/', $filtro)) {
    // Filtro é um ano (ex: 2025, 2024, 2023)
    $ano = intval($filtro);
    $data_inicio = $ano . '-01-01';
    $data_fim = $ano . '-12-31';
    $where_clause = "WHERE DATE(s.data_hora) >= '" . $conn->real_escape_string($data_inicio) . "' AND DATE(s.data_hora) <= '" . $conn->real_escape_string($data_fim) . "'";
} else {
    // Filtro é um período em dias
    switch($filtro) {
        case '7':
            $data_inicio = date('Y-m-d', strtotime('-7 days'));
            break;
        case '30':
            $data_inicio = date('Y-m-d', strtotime('-30 days'));
            break;
        case '365':
            $data_inicio = date('Y-m-d', strtotime('-365 days'));
            break;
        default:
            $data_inicio = date('Y-m-d', strtotime('-30 days'));
    }
    $where_clause = "WHERE DATE(s.data_hora) >= '" . $conn->real_escape_string($data_inicio) . "'";
}

// Construir a query com filtro de data
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
" . $where_clause . "
ORDER BY s.data_hora DESC";

$results = $conn->query($sql);
if (!$results) {
    die("Erro na query: " . $conn->error);
}

$servicos = $results->fetch_all(MYSQLI_ASSOC);

// Exibir mensagem se não houver serviços
if (empty($servicos)) {
    echo "<tr><td colspan='6' style='text-align: center; padding: 20px;'>Nenhum serviço encontrado neste período.</td></tr>";
} else {
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
}
?>