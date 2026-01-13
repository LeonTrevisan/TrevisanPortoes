<?php
include 'conexao.php';

//dados cliente
$nome = filter_input(INPUT_POST, 'nome-cliente');
$fone = filter_input(INPUT_POST, 'tel-cliente');
// accept tipo as either numeric id or string
$tipoRaw = filter_input(INPUT_POST, 'tipo-cliente');
$idTipo = filter_var($tipoRaw, FILTER_VALIDATE_INT);
if ($idTipo === false || $idTipo === null) {
    $tipoPost = filter_input(INPUT_POST, 'tipo-cliente', FILTER_SANITIZE_STRING);
    if ($tipoPost) {
        $stmtTipo = $conn->prepare("SELECT id_tipo_cliente FROM tb_tipo_cliente WHERE tipo_cliente LIKE ? LIMIT 1");
        $like = "%$tipoPost%";
        $stmtTipo->bind_param("s", $like);
        $stmtTipo->execute();
        $res = $stmtTipo->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $idTipo = $row['id_tipo_cliente'] ?? null;
    } else {
        $idTipo = null;
    }
}

//exclusivo condomínio
$email = filter_input(INPUT_POST, 'email-cliente');
$idSindico = filter_input(INPUT_POST, 'sindico-cliente', FILTER_VALIDATE_INT);
$idAdm = filter_input(INPUT_POST, 'adm-cliente', FILTER_VALIDATE_INT);
// possible CNPJ text field
$cnpj_text = filter_input(INPUT_POST, 'cnpj-cliente', FILTER_SANITIZE_STRING);

//endereço
$rua = filter_input(INPUT_POST, 'rua-cliente');
$bairro = filter_input(INPUT_POST, 'bairro-cliente');
$numCasa = filter_input(INPUT_POST, 'num-cliente');
$cidade = filter_input(INPUT_POST, 'cidade-cliente');
$comp = filter_input(INPUT_POST, 'comp-cliente');

if (empty($fone) || empty($nome) || empty($idTipo) || empty($rua) || empty($bairro) || empty($numCasa) || empty($cidade)) {
    exit('Dados inválidos');
}

// determine CNPJ value: prefer uploaded file path, otherwise use text field
$cnpj = null;
if (isset($_FILES['cnpj-doc']) && $_FILES['cnpj-doc']['error'] === 0) {
    $arquivo = $_FILES['cnpj-doc'];
    $pasta = "../docs/uploads/CNPJ/";
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid("cnpj_") . "." . $extensao;
    $destino = $pasta . $nomeArquivo;
    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        exit('Erro ao salvar o documento');
    }
    $cnpj = $destino;
} elseif (!empty($cnpj_text)) {
    $cnpj = $cnpj_text;
}

// determine if tipo is a condomínio (by name)
$isCondominio = false;
if ($idTipo !== null) {
    $stmtTipoName = $conn->prepare("SELECT tipo_cliente FROM tb_tipo_cliente WHERE id_tipo_cliente = ? LIMIT 1");
    $stmtTipoName->bind_param("i", $idTipo);
    $stmtTipoName->execute();
    $resTipo = $stmtTipoName->get_result()->fetch_assoc();
    $tipoName = $resTipo['tipo_cliente'] ?? '';
    if (stripos($tipoName, 'cond') !== false) $isCondominio = true;
}

if($isCondominio){
    $email = $email ?: null;
    // keep $idAdm and $idSindico as provided (IDs or null)
} else {
    $email = null;
    $idSindico = null;
    $idAdm = null;
}

// cast to scalars
$idTipo = $idTipo !== null ? (int)$idTipo : null;
$idAdm = $idAdm !== null ? (int)$idAdm : null;
$idSindico = $idSindico !== null ? (int)$idSindico : null;

$stmt = $conn->prepare("INSERT INTO tb_cliente (nome, email, telefone, cnpj, id_tipo_cliente, id_admin, id_sindico)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt -> bind_param("ssssiii", $nome, $email, $fone, $cnpj, $idTipo, $idAdm, $idSindico);

if (!$stmt->execute()) {
    exit('Erro ao inserir cliente: ' . $stmt->error);
} 

$idCliente = $conn->insert_id;

$stmtEndereco = $conn->prepare(
    "INSERT INTO tb_endereco ( id_cliente, rua, bairro, numero, cidade)
        VALUES (?, ?, ?, ?, ?)"
);

$stmtEndereco->bind_param(
    "issss",
    $idCliente,
    $rua,
    $bairro,
    $numCasa,
    $cidade
);

$stmtEndereco->execute();

header("location: ../../frontend/index.php");
?>