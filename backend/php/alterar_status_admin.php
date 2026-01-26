<?php 
    include 'conexao.php';

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../../frontend/index.php?page=admin&status=error&msg=ID inválido!");
        exit();
    }

    $id_admin = intval($_GET['id']);

    function desativarAdmin($conn, $id_admin) {
        $sql = "UPDATE tb_admin_cond SET deleted_at = NOW() WHERE id_admin = ? AND deleted_at IS NULL";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_admin); // i = integer
        $stmt->execute();
        return $stmt->affected_rows > 0;

        if (!$stmt) {
            header("Location: ../../frontend/index.php?page=admin&status=error&msg=Erro ao preparar operação!");
            exit();
        }

        else{
              
            if (!$stmt->execute()) {
                header("Location: ../../frontend/index.php?page=admin&status=error&msg=Erro ao deletar administrador!");
                exit();
            }

            $stmt->close();

            // Redirecionar com mensagem de sucesso
            header("Location: ../../frontend/index.php?page=sindico&status=success&msg=Administrador desativado com sucesso!");
            exit();
        }
    }
?>