<?php
include 'conexao.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do serviço não fornecido']);
    exit;
}

$id_servico = intval($_GET['id']);

$sql = "SELECT 
    s.id_servico,
    s.id_cliente,
    s.id_tipo,
    s.descricao,
    s.observacao,
    s.foto,
    s.comprovante,
    s.data_hora,
    p.id_pagamento,
    p.id_forma_pagamento,
    p.valor,
    p.id_status
FROM tb_servico s
LEFT JOIN tb_pagamento p ON s.id_servico = p.id_servico
WHERE s.id_servico = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao preparar statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_servico);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao executar query: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$servico = $result->fetch_assoc();
$stmt->close();

if (!$servico) {
    http_response_code(404);
    echo json_encode(['erro' => 'Serviço não encontrado']);
    exit;
}

// Garantir que todos os campos existem (para evitar undefined no JavaScript)
$servico = array_merge([
    'id_servico' => null,
    'id_cliente' => null,
    'id_tipo' => null,
    'descricao' => '',
    'observacao' => '',
    'foto' => null,
    'comprovante' => null,
    'data_hora' => '',
    'id_pagamento' => null,
    'id_forma_pagamento' => null,
    'valor' => 0,
    'id_status' => null
], $servico);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($servico);
exit;
?>