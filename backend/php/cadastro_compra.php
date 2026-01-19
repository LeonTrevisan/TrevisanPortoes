<?php
    include 'conexao.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Receber dados do formulário
        $data_hora = $_POST['data_hora'];
        $material = $_POST['material'];
        $fornecedor = intval($_POST['fornecedor']);
        $qtd = $_POST['qtd'];
        $valor = floatval($_POST['valor']);
        $valorTotal = $qtd * $valor;

        // Inserir compra no banco de dados
        $sql = "INSERT INTO tb_compras (data_compra, material, id_distribuidora, qtd_compra, valor_un, valor_total) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("ssiddd", $data_hora, $material, $fornecedor, $qtd, $valor, $valorTotal);
        
        if (!$stmt->execute()) {
            die("Erro ao inserir compra: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=compras&status=success&msg=Compra registrada com sucesso!");
        exit();
    }