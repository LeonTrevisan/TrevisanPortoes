<?php 
    include __DIR__ . '/conexao.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit;
}
$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tb_cliente LEFT JOIN tb_endereco ON tb_cliente.id_cliente = tb_endereco.id_cliente WHERE tb_cliente.id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$ficha = $result->fetch_assoc();

$arquivo = $ficha['cnpj'];

echo"<div class='ficha-cliente'>
    <div class='ficha-details'>
        <h2>Ficha do Cliente</h2>
        <p>Nome do cliente: " . htmlspecialchars($ficha['nome']) . "
        <br>Telefone: " . htmlspecialchars($ficha['telefone']) . "
        <br>Email: " . htmlspecialchars($ficha['email']) . "
        <br>Endereço: " . htmlspecialchars($ficha['rua']) . ", "
        . htmlspecialchars($ficha['numero']) . ", " 
        . htmlspecialchars($ficha['bairro']) . ", "
        . htmlspecialchars($ficha['cidade']) . "</p>
    </div>
    <div class='cnpj-ficha'>
        <img src=\"../backend/php/visualizar_cnpj.php?id={$ficha['id_cliente']}\">
    </div>
</div>";

?>