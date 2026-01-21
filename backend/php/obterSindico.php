<?php
require 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

// valida ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT id_sindico, nome, telefone
        FROM tb_sindico
        WHERE id_sindico = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Erro ao preparar SQL']);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Síndico não encontrado']);
    exit;
}

$data = $result->fetch_assoc();
echo json_encode($data);
exit;
