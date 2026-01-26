<?php
include 'conexao.php';

/* ============================
   1. RECEBER FILTROS
============================ */

$pagamento   = $_GET['pagamento']   ?? $_POST['pagamento']   ?? 'todos';
$tipoFiltro  = $_GET['tipoFiltro']  ?? $_POST['tipoFiltro']  ?? 'periodo';
$valorFiltro = $_GET['valorFiltro'] ?? $_POST['valorFiltro'] ?? '30';

/* ============================
   2. FILTRO DE DATA
============================ */

$where = [];
$params = [];
$types = '';

$dataHoje = date('Y-m-d');

if ($tipoFiltro === 'ano' && preg_match('/^\d{4}$/', $valorFiltro)) {
    $dataInicio = $valorFiltro . '-01-01';
    $dataFim    = $valorFiltro . '-12-31';

    $where[] = "DATE(s.data_hora) BETWEEN ? AND ?";
    $params[] = $dataInicio;
    $params[] = $dataFim;
    $types .= 'ss';

} else {
    // Período em dias
    $dias = in_array($valorFiltro, ['7','30','365']) ? $valorFiltro : '30';
    $dataInicio = date('Y-m-d', strtotime("-$dias days"));

    $where[] = "DATE(s.data_hora) >= ?";
    $params[] = $dataInicio;
    $types .= 's';
}

/* ============================
   3. FILTRO DE PAGAMENTO
============================ */

if ($pagamento === 'pago') {
    $where[] = "sp.status_pagamento = 'Pago'";
}
elseif ($pagamento === 'pendente') {
    $where[] = "sp.status_pagamento = 'Pendente'";
}

/* ============================
   4. MONTAR WHERE
============================ */

$whereSQL = '';
if (!empty($where)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}

/* ============================
   5. QUERY
============================ */

$sql = "
SELECT 
    s.id_servico,
    s.data_hora,
    c.nome AS cliente,
    ts.tipo_servico,
    p.valor,
    sp.status_pagamento
FROM tb_servico s
LEFT JOIN tb_cliente c ON s.id_cliente = c.id_cliente
LEFT JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
LEFT JOIN tb_pagamento p ON s.id_servico = p.id_servico
LEFT JOIN tb_status_pagamento sp ON p.id_status = sp.id_status
$whereSQL
ORDER BY s.data_hora DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$servicos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ============================
   6. SAÍDA HTML
============================ */

if (empty($servicos)) {
    echo "<tr>
            <td colspan='6' style='text-align:center; padding:20px'>
                Nenhum serviço encontrado.
            </td>
          </tr>";
    exit;
}

foreach ($servicos as $servico) {

    $data_formatada = date('d/m/Y', strtotime($servico['data_hora']));

    $status_class = (strtotime($servico['data_hora']) < time())
        ? 'status-concluido'
        : 'status-agendado';

    echo "
    <tr>
        <td>{$data_formatada}</td>
        <td>" . htmlspecialchars($servico['cliente']) . "</td>
        <td>" . htmlspecialchars($servico['tipo_servico']) . "</td>
        <td>R$ " . number_format($servico['valor'], 2, ',', '.') . "</td>
        <td>
            <span class='status {$status_class}'>
                " . htmlspecialchars($servico['status_pagamento']) . "
            </span>
        </td>
        <td>
            <div class='action-buttons'>
                <button class='btn btn-primary btn-small'
                    onclick='editarServico({$servico['id_servico']})'>
                    Editar
                </button>
                <button class='btn btn-danger btn-small'
                    onclick='confirmarExclusao({$servico['id_servico']}, \"servico\")'>
                    Excluir
                </button>
            </div>
        </td>
    </tr>
    ";
}
