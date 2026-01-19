<?php 
    include 'conexao.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validar dados obrigatórios
        if (empty($_POST['id_servico']) || empty($_POST['clientes']) || empty($_POST['data_hora']) || 
            empty($_POST['tipo']) || empty($_POST['preco'])) {
            die("Erro: Preencha todos os campos obrigatórios.");
        }

        $id_servico = intval($_POST['id_servico']);
        $id_cliente = intval($_POST['clientes']);
        $data_hora = $_POST['data_hora'] . " " . date("H:i:s");
        $id_tipo = intval($_POST['tipo']);
        $descricao = isset($_POST['descricao']) ? $conn->real_escape_string($_POST['descricao']) : NULL;
        $observacao = isset($_POST['observacao']) ? $conn->real_escape_string($_POST['observacao']) : NULL;
        $valor = floatval($_POST['preco']);
        $id_status = intval($_POST['statusPagamento']);
        $id_forma_pagamento = intval($_POST['formaPagamento']);

        // Obter dados atuais do serviço
        $sql_select = "SELECT foto, comprovante FROM tb_servico WHERE id_servico = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param("i", $id_servico);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $servico_atual = $result->fetch_assoc();
        $stmt_select->close();

        $foto = $servico_atual['foto'];
        $comprovante = $servico_atual['comprovante'];

        // Criar diretórios de upload se não existirem
        $upload_dir_foto = __DIR__ . '/../docs/uploads/servicos/fotos/';
        $upload_dir_comprovante = __DIR__ . '/../docs/uploads/servicos/comprovantes/';

        if (!is_dir($upload_dir_foto)) {
            mkdir($upload_dir_foto, 0755, true);
        }
        if (!is_dir($upload_dir_comprovante)) {
            mkdir($upload_dir_comprovante, 0755, true);
        }

        // Upload da nova foto
        if (isset($_FILES['foto-servico']) && $_FILES['foto-servico']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['foto-servico']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];
            
            if (in_array($file_ext, $allowed_ext)) {
                // Deletar foto anterior se existir
                if (!empty($foto)) {
                    $foto_path = __DIR__ . '/../docs/uploads/' . $foto;
                    if (file_exists($foto_path)) {
                        unlink($foto_path);
                    }
                }
                
                $foto_name = "foto_" . time() . "_" . uniqid() . "." . $file_ext;
                if (move_uploaded_file($_FILES['foto-servico']['tmp_name'], $upload_dir_foto . $foto_name)) {
                    $foto = "servicos/fotos/" . $foto_name;
                }
            }
        }

        // Upload do novo comprovante
        if (isset($_FILES['comprovante-servico']) && $_FILES['comprovante-servico']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['comprovante-servico']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if (in_array($file_ext, $allowed_ext)) {
                // Deletar comprovante anterior se existir
                if (!empty($comprovante)) {
                    $comprovante_path = __DIR__ . '/../docs/uploads/' . $comprovante;
                    if (file_exists($comprovante_path)) {
                        unlink($comprovante_path);
                    }
                }
                
                $comprovante_name = "comprovante_" . time() . "_" . uniqid() . "." . $file_ext;
                if (move_uploaded_file($_FILES['comprovante-servico']['tmp_name'], $upload_dir_comprovante . $comprovante_name)) {
                    $comprovante = "servicos/comprovantes/" . $comprovante_name;
                }
            }
        }

        // Atualizar serviço no banco de dados
        $sql = "UPDATE tb_servico 
                SET id_cliente = ?, id_tipo = ?, descricao = ?, observacao = ?, foto = ?, comprovante = ?, data_hora = ? 
                WHERE id_servico = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("iisssssi", $id_cliente, $id_tipo, $descricao, $observacao, $foto, $comprovante, $data_hora, $id_servico);
        
        if (!$stmt->execute()) {
            die("Erro ao atualizar serviço: " . $stmt->error);
        }

        $stmt->close();

        // Atualizar pagamento associado ao serviço
        $sql_pagamento = "UPDATE tb_pagamento 
                         SET id_forma_pagamento = ?, valor = ?, id_status = ? 
                         WHERE id_servico = ?";
        
        $stmt_pagamento = $conn->prepare($sql_pagamento);
        if (!$stmt_pagamento) {
            die("Erro ao preparar statement de pagamento: " . $conn->error);
        }

        $stmt_pagamento->bind_param("idii", $id_forma_pagamento, $valor, $id_status, $id_servico);
        
        if (!$stmt_pagamento->execute()) {
            die("Erro ao atualizar pagamento: " . $stmt_pagamento->error);
        }

        $stmt_pagamento->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=servicos&status=success&msg=Serviço atualizado com sucesso!");
        exit();
    }
?>
