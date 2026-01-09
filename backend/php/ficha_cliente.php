<?php include 'conexao.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit;
}
$id = null;
$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tb_cliente LEFT JOIN tb_endereco ON tb_cliente.id_cliente = tb_endereco.id_cliente WHERE tb_cliente.id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$ficha = $result->fetch_assoc();

echo"Nome do cliente: " . $ficha['nome'];


?>