<?php 
    include 'conexao.php'; 


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

 $arquivo = $ficha['cnpj'];

    if (file_exists($arquivo)) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"".basename($arquivo)."\"");
        readfile($arquivo);
        exit;
    }

echo"
    <div class='ficha-cliente'>
        <div class='ficha-details'>
            <h2>Ficha do Cliente</h2>
            <p>Nome do cliente: " . $ficha['nome'] . "
            <br>Telefone: " . $ficha['telefone'] . "
            <br>Email: " . $ficha['email'] . "
            <br>Endereço: " . $ficha['rua'] . ", "
            . $ficha['numero'] . ", " 
            . $ficha['bairro'] . ", "
            . $ficha['cidade'] . "</p>
        </div>
        <div class='cnpj-ficha'>
            <img class=\"cnpj-img\" src=\"../backend/php/visualizar_cnpj.php?id={$ficha['id_cliente']}\"
        </div>
    </div>";
?>