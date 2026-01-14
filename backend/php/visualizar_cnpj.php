<?php

include 'conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    http_response_code(400);
    echo 'ID inválido';
    exit;
}

// Busca o caminho do arquivo no banco de dados
$stmt = $conn->prepare("SELECT cnpj FROM tb_cliente WHERE id_cliente = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || empty($row['cnpj'])) {
    http_response_code(404);
    echo 'Nenhum documento disponível';
    exit;
    }
    $arquivo = $row['cnpj'];


// Tentativas de caminhos (suporta caminho armazenado relativo ou apenas nome de arquivo)
$candidates = [
    $arquivo,
    __DIR__ . DIRECTORY_SEPARATOR . $arquivo,
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . ltrim($arquivo, '/\\'),
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'CNPJ' . DIRECTORY_SEPARATOR . basename($arquivo),
];

$filePath = null;
foreach ($candidates as $c) {
    $real = realpath($c);
    if ($real && is_file($real) && is_readable($real)) {
        $filePath = $real;
        break;
    }
}

if (!$filePath) {
    http_response_code(404);
    echo 'Arquivo não encontrado: ' . htmlspecialchars($arquivo);
    exit;
}

$mime = mime_content_type($filePath) ?: 'application/pdf';
header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Accept-Ranges: bytes');

// Limpa qualquer saída pendente e envia o arquivo
if (ob_get_level()) ob_end_clean();
$fp = fopen($filePath, 'rb');
if ($fp) {
    while (!feof($fp)) {
        echo fread($fp, 8192);
        flush();
    }
    fclose($fp);
}
exit;
?>