<?php
include 'conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID da compra não fornecido");
}

$id_compra = intval($_GET['id']);

// Deletar serviço
$sql = "DELETE FROM tb_compras WHERE id_compra = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro ao preparar statement: " . $conn->error);
}

$stmt->bind_param("i", $id_compra);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    
    header("Location: ../../frontend/index.php?page=pecas&status=success&msg=Compra deletada com sucesso!");
    exit();
} else {
    die("Erro ao deletar compra: " . $stmt->error);
}
?>
