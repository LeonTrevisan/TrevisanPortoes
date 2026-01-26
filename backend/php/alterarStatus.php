<?php
include 'conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['tipo'], $data['id'], $data['acao'])) {
    echo json_encode(['success' => false, 'msg' => 'Dados incompletos']);
    exit();
}

$tipo = $data['tipo'];
$id   = (int) $data['id'];
$acao = $data['acao'];

$mapa = [   
    'admin'     =>  ['tabela' => 'tb_admin_cond', 'id' => 'id_admin'],
    'cliente'   =>  ['tabela' => 'tb_cliente', 'id' => 'id_cliente'],
    'sindico'   =>  ['tabela' => 'tb_sindico', 'id' => 'id_sindico']
];

if (!isset($mapa[$tipo])) {
    echo json_encode(['success' => false, 'msg' => 'Tipo inválido']);
    exit();
}

$sql = "
    UPDATE {$mapa[$tipo]['tabela']}
    SET deleted_at = IF(deleted_at IS NULL, CURDATE(), NULL)
    WHERE {$mapa[$tipo]['id']} = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode([
    'success' => $stmt->affected_rows > 0
]);
?>