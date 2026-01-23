<?php 
    include 'conexao.php';

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../../frontend/index.php?page=admin&status=error&msg=ID inválido!");
        exit();
    }

    $id_admin = intval($_GET['id']);

    // Verifica se o síndico está associado a algum condomínio
    $checkSql = "SELECT id_admin FROM tb_cliente WHERE id_admin = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $id_admin); 
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkData = $checkResult->fetch_assoc();
    $checkStmt->close(); 
    
    if($checkData) { 
        // Síndico está associado a um condomínio - não pode deletar
        header("Location: ../../frontend/index.php?page=admin&status=error&msg=Não é possível deletar o administrador pois ele está associado a um condomínio!");
        exit();
    } else { 
        // Deletar o síndico do banco de dados
        $sql = "DELETE FROM tb_admin_cond WHERE id_admin = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header("Location: ../../frontend/index.php?page=admin&status=error&msg=Erro ao preparar operação!");
            exit();
        }

        $stmt->bind_param("i", $id_admin);
        
        if (!$stmt->execute()) {
            header("Location: ../../frontend/index.php?page=admin&status=error&msg=Erro ao deletar administrador!");
            exit();
        }

        $stmt->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=sindico&status=success&msg=Administrador deletado com sucesso!");
        exit();
    }
?>