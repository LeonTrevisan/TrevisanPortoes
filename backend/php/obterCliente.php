<?php
include 'conexao.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do cliente não fornecido']);
    exit;
}

$id_cliente = intval($_GET['id']);

$sql = "SELECT 
    s.id_cliente,
    s.id_admin,
    d.id_sindico,
    a.id_tipo,
    s.nome,
    s.telefone,
    s.email,
    s.cnpj,
    p.rua,
    p.numero,
    p.bairro,
    p.cidade
FROM tb_cliente s
LEFT JOIN tb_endereco p ON s.id_cliente = p.id_cliente
LEFT JOIN tb_tipo_cliente a ON s.id_tipo = a.id_tipo
LEFT JOIN tb_sindico d ON s.id_sindico = d.id_sindico
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
    'id_tipo' => null,
    'nome' => '',
    'telefone' => '',
    'rua' => '',
    'bairro' => '',
    'cidade' => '',
    'numero' => 0
], $cliente);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($cliente);
exit;
?>