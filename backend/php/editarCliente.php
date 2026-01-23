<?php 
    include 'conexao.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validar dados obrigatórios
        if (empty($_POST['id_cliente']) || empty($_POST['tel-cliente']) || empty($_POST['nome-cliente'])){
            die("Erro: Preencha todos os campos obrigatórios.");
        }

        $id_cliente = intval($_POST['id_cliente']);
        $id_admin = intval($_POST['admin-cliente']);
        $id_sindico = intval($_POST['sindico-cliente']);
        $id_tipo_cliente = intval($_POST['tipo-cliente']);
        $email = $conn->real_escape_string($_POST['email-cliente']);
        $telefone = $conn->real_escape_string($_POST['tel-cliente']);
        $nome = $conn->real_escape_string($_POST['nome-cliente']);
        $id_endereco = intval($_POST['id_endereco']);
        $rua = $conn->real_escape_string($_POST['rua-cliente']);
        $numero = $conn->real_escape_string($_POST['numero-cliente']);
        $bairro = $conn->real_escape_string($_POST['bairro-cliente']);
        $cidade = $conn->real_escape_string($_POST['cidade-cliente']); 

        // Obter dados atuais do serviço
        $sql_select = "SELECT cnpj FROM tb_cliente WHERE id_cliente = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param("i", $id_cliente);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $cliente_atual = $result->fetch_assoc();
        $stmt_select->close();

        $cnpj = $cliente_atual['cnpj'];

        // Criar diretórios de upload se não existirem
        $upload_dir_cnpj = __DIR__ . '/../docs/uploads/CNPJ';

        if (!is_dir($upload_dir_cnpj)) {
            mkdir($upload_dir_cnpj, 0755, true);
        }

        // Upload da nova foto
        if (isset($_FILES['cnpj']) && $_FILES['cnpj']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['cnpj']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];
            
            if (in_array($file_ext, $allowed_ext)) {
                // Deletar foto anterior se existir
                if (!empty($cnpj)) {
                    $cnpj_path = __DIR__ . '/../docs/uploads/CNPJ/' . $cnpj;
                    if (file_exists($cnpj_path)) {
                        unlink($cnpj_path);
                    }
                }
                
                $cnpj_name = "cnpj_" . time() . "_" . uniqid() . "." . $file_ext;
                if (move_uploaded_file($_FILES['cnpj']['tmp_name'], $upload_dir_cnpj . $cnpj_name)) {
                    $cnpj = "uploads/CNPJ/" . $cnpj_name;
                }
            }
        }

        // Atualizar serviço no banco de dados
        $sql = "UPDATE tb_cliente 
                SET id_cliente = ?, id_tipo_cliente = ?, id_admin = ?, id_sindico = ?, cnpj = ?, email = ?, telefone = ?, nome = ? 
                WHERE id_cliente = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }

        $stmt->bind_param("iiiissssi", $id_cliente, $id_tipo_cliente, $id_admin, $id_sindico, $cnpj, $email, $telefone, $nome, $id_cliente);
        
        if (!$stmt->execute()) {
            die("Erro ao atualizar serviço: " . $stmt->error);
        }

        $stmt->close();

        // Atualizar endereço associado ao cliente
        $sql_pagamento = "UPDATE tb_endereco
                         SET id_endereco = ?, id_cliente = ?, rua = ?, numero = ?, bairro = ?, cidade = ?
                         WHERE id_cliente = ?";
        
        $stmt_pagamento = $conn->prepare($sql_pagamento);
        if (!$stmt_pagamento) {
            die("Erro ao preparar statement de pagamento: " . $conn->error);
        }

        $stmt_pagamento->bind_param("iisissi", $id_endereco, $id_cliente, $rua, $numero, $bairro, $cidade, $id_endereco);
        
        if (!$stmt_pagamento->execute()) {
            die("Erro ao atualizar pagamento: " . $stmt_pagamento->error);
        }

        $stmt_pagamento->close();
        $conn->close();

        // Redirecionar com mensagem de sucesso
        header("Location: ../../frontend/index.php?page=clientes&status=success&msg=Cliente atualizado com sucesso!");
        exit();
    }
?>
