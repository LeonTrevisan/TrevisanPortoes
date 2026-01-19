<?php
include 'conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do serviço não fornecido");
}

$id_servico = intval($_GET['id']);

// Obter dados do serviço para deletar arquivos
$sql = "SELECT foto, comprovante FROM tb_servico WHERE id_servico = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_servico);
$stmt->execute();
$result = $stmt->get_result();
$servico = $result->fetch_assoc();
$stmt->close();

if ($servico) {
    // Deletar arquivos
    if (!empty($servico['foto'])) {
        $foto_path = __DIR__ . '/../docs/uploads/' . $servico['foto'];
        if (file_exists($foto_path)) {
            unlink($foto_path);
        }
    }
    
    if (!empty($servico['comprovante'])) {
        $comprovante_path = __DIR__ . '/../docs/uploads/' . $servico['comprovante'];
        if (file_exists($comprovante_path)) {
            unlink($comprovante_path);
        }
    }
}

// Deletar pagamento associado
$sql_pagamento = "DELETE FROM tb_pagamento WHERE id_servico = ?";
$stmt_pagamento = $conn->prepare($sql_pagamento);
$stmt_pagamento->bind_param("i", $id_servico);
$stmt_pagamento->execute();
$stmt_pagamento->close();

// Deletar serviço
$sql = "DELETE FROM tb_servico WHERE id_servico = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro ao preparar statement: " . $conn->error);
}

$stmt->bind_param("i", $id_servico);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    
    header("Location: ../../frontend/index.php?page=servicos&status=success&msg=Serviço deletado com sucesso!");
    exit();
} else {
    die("Erro ao deletar serviço: " . $stmt->error);
}
?>
