<?php 
    include 'conexao.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validar dados obrigatórios
        var_dump($_POST);
        if (empty($_POST['id_sindico']) || empty($_POST['nome-sindico']) || empty($_POST['tel-sindico'])) {
            die("Erro: Preencha todos os campos obrigatórios.");
        }

        $id_sindico = intval($_POST['id_sindico']);
        $nome = $_POST['nome-sindico'];
        $telefone = $_POST['tel-sindico'];

        // Atualizar serviço no banco de dados
        $sql = "UPDATE tb_sindico 
                SET id_sindico = ?, nome = ?, telefone = ? 
                WHERE id_sindico = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("issi", $id_sindico, $nome, $telefone, $id_sindico);
        
        if (!$stmt->execute()) {
            die("Erro ao atualizar serviço: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=sindicos&status=success&msg=SÍndico atualizado com sucesso!");
        exit();
    }
?>
