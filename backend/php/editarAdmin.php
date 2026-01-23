<?php 
    include 'conexao.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validar dados obrigatórios
        if (empty($_POST['id_admin']) || empty($_POST['nome-admin']) || empty($_POST['tel-admin'])) {
            die("Erro: Preencha todos os campos obrigatórios.");
        }

        $id_admin = intval($_POST['id_admin']);
        $nome = $_POST['nome-admin'];
        $telefone = $_POST['tel-admin'];
        $email = $_POST['email-admin']; 

        // Atualizar serviço no banco de dados
        $sql = "UPDATE tb_admin_cond 
                SET id_admin = ?, nome = ?, telefone = ?, email = ?
                WHERE id_admin = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("isssi", $id_admin, $nome, $telefone, $email, $id_admin);
        
        if (!$stmt->execute()) {
            die("Erro ao atualizar serviço: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=admin&status=success&msg=Administrador atualizado com sucesso!");
        exit();
    }
?>
