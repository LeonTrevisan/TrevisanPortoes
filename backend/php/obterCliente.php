<?php
include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do cliente não fornecido']);
    exit;
}

$id_cliente = intval($_GET['id']);

$sql = "SELECT 
    s.id_cliente,
    s.id_admin,
    s.id_sindico,
    s.id_tipo_cliente,
    t.tipo_cliente,
    s.nome,
    s.telefone,
    s.email,
    s.cnpj,
    p.id_endereco,
    p.rua,
    p.numero,
    p.bairro,
    p.cidade
FROM tb_cliente s
LEFT JOIN tb_endereco p ON s.id_cliente = p.id_cliente
LEFT JOIN tb_tipo_cliente t ON s.id_tipo_cliente = t.id_tipo_cliente
WHERE s.id_cliente = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao preparar statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_cliente);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao executar query: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

if (!$cliente) {
    http_response_code(404);
    echo json_encode(['erro' => 'Cliente não encontrado']);
    exit;
}

// Garantir que todos os campos existem (para evitar undefined no JavaScript)
$cliente = array_merge([
    'id_cliente' => null,
    'id_admin' => null,
    'id_sindico' => null,
    'id_tipo_cliente' => null,
    'nome' => '',
    'telefone' => '',
    'email' => '',
    'cnpj' => '',
    'rua' => '',
    'bairro' => '',
    'cidade' => '',
    'numero' => 0
], $cliente);

echo json_encode($cliente);
exit;
?>