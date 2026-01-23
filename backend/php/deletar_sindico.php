<?php 
    include 'conexao.php';

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../../frontend/index.php?page=sindico&status=error&msg=ID inválido!");
        exit();
    }

    $id_sindico = intval($_GET['id']);

    // Verifica se o síndico está associado a algum condomínio
    $checkSql = "SELECT id_sindico FROM tb_cliente WHERE id_sindico = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $id_sindico);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkData = $checkResult->fetch_assoc();
    $checkStmt->close(); // Fechar a statement
    
    if($checkData) {
        // Síndico está associado a um condomínio - não pode deletar
        header("Location: ../../frontend/index.php?page=sindico&status=error&msg=Não é possível deletar o síndico pois ele está associado a um condomínio!");
        exit();
    } else {
        // Deletar o síndico do banco de dados
        $sql = "DELETE FROM tb_sindico WHERE id_sindico = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header("Location: ../../frontend/index.php?page=sindico&status=error&msg=Erro ao preparar operação!");
            exit();
        }

        $stmt->bind_param("i", $id_sindico);
        
        if (!$stmt->execute()) {
            header("Location: ../../frontend/index.php?page=sindico&status=error&msg=Erro ao deletar síndico!");
            exit();
        }

        $stmt->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=sindico&status=success&msg=Síndico deletado com sucesso!");
        exit();
    }
?>