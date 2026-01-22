<?php 
    include 'conexao.php';

        $id_sindico = intval($_GET['id_sindico']);

        // Verifica se o síndico está associado a algum condomínio
        $checkSql = "SELECT id_sindico FROM tb_cliente WHERE id_sindico = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $id_sindico);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkStm = $checkResult->fetch_assoc();
        
        if($checkStm) {
            // Síndico está associado a um condomínio - não pode deletar
            header("Location: ../../frontend/index.php?page=sindico&status=error&msg=Não é possível deletar o síndico pois ele está associado a um condomínio!");
            exit();
        } else {
            // Deletar o síndico do banco de dados
            $sql = "DELETE FROM tb_sindico WHERE id_sindico = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Erro ao preparar statement: " . $conn->error);
            }

            $stmt->bind_param("i", $id_sindico);
            
            if (!$stmt->execute()) {
                die("Erro ao deletar síndico: " . $stmt->error);
            }

            $stmt->close();

            // Redirecionar com mensagem de sucesso
            header("Location: ../../frontend/index.php?page=sindico&status=success&msg=Síndico deletado com sucesso!");
            exit();
        }
?>