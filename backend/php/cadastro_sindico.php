<?php  
    include 'conexao.php';

   if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Receber dados do formulário
        $nome = $_POST['nome-sindico'];
        $telefone = $_POST['tel-sindico'];

        // Inserir síndico no banco de dados
        $sql = "INSERT INTO tb_sindico (nome, telefone) 
                VALUES (?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("ss", $nome, $telefone);
        
        if (!$stmt->execute()) {
            die("Erro ao inserir síndico: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=sindicos&status=success&msg=Síndico cadastrado com sucesso!");
        exit();
   }

?>