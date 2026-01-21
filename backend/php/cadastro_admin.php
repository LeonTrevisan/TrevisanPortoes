<?php
    include 'conexao.php';

   if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Receber dados do formulário
        $nome = $_POST['nome-admin'];
        $telefone = $_POST['tel-admin'];
        $email = $_POST['email-admin'];

        // Inserir administrador no banco de dados
        $sql = "INSERT INTO tb_admin_cond (nome, telefone, email) 
                VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("sss", $nome, $telefone, $email);
        
        if (!$stmt->execute()) {
            die("Erro ao inserir administrador: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=administradores&status=success&msg=Administrador cadastrado com sucesso!");
        exit();
   }
?>