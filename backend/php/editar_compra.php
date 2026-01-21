<?php 
    include 'conexao.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_compra = intval($_POST['id_compra']);
        $id_distribuidora = intval($_POST['fornecedor']);
        $material = $_POST['material'];
        $data_hora = $_POST['data_hora'];
        $valor = floatval($_POST['valor']);
        $quantidade = intval($_POST['qtd']);
        $valor_total = $valor * $quantidade;

        // Atualizar serviço no banco de dados
        $sql = "UPDATE tb_compras
                SET id_compra = ?, id_distribuidora = ?, material = ?,  data_compra = ?, valor_un = ?, qtd_compra = ?, valor_total = ?
                WHERE id_compra = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("iissdidi", $id_compra, $id_distribuidora, $material, $data_hora, $valor, $quantidade, $valor_total, $id_compra);
        
        if (!$stmt->execute()) {
            die("Erro ao atualizar serviço: " . $stmt->error);
        }

        $stmt->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=servicos&status=success&msg=Compra atualizada com sucesso!");
        exit();
    }
?>
