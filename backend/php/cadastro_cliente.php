<?php
include 'conexao.php';

$nome = filter_input(INPUT_POST, 'nome-cliente');
$doc = strval(filter_input(INPUT_POST, 'doc-cliente'));
$fone = filter_input(INPUT_POST, 'tel-cliente');
$rua = filter_input(INPUT_POST, 'rua-cliente');
$bairro = filter_input(INPUT_POST, 'bairro-cliente');
$numCasa = filter_input(INPUT_POST, 'num-cliente');
$cidade = filter_input(INPUT_POST, 'cidade-cliente');
$comp = filter_input(INPUT_POST, 'comp-cliente');


//preparação dos dados para cadastro do cliente
if (empty($nome) || empty($doc)) {
    exit('Dados inválidos');
}

// //verificação dos documentos
// if (strlen($doc) != 11 || strlen($doc) != 14){
//     return false;
// }

// elseif(strlen($doc) == 14){
//     function validaCNPJ($cnpj) {
//     // Remove caracteres não numéricos
//     $cnpj = preg_replace('/\D/', '', $cnpj);
//     // Verifica se tem 14 dígitos
//     if (strlen($cnpj) != 14) {
//         return false;
//     }
//     // Elimina CNPJs inválidos conhecidos
//     if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
//         return false;
//     }
//     // Validação do primeiro dígito
//     $pesos1 = [5,4,3,2,9,8,7,6,5,4,3,2];
//     $soma = 0;
//     for ($i = 0; $i < 12; $i++) {
//         $soma += $cnpj[$i] * $pesos1[$i];
//     }
//     $resto = $soma % 11;
//     $digito1 = ($resto < 2) ? 0 : 11 - $resto;

//     if ($cnpj[12] != $digito1) {
//         return false;
//     }
//     // Validação do segundo dígito
//     $pesos2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
//     $soma = 0;
//     for ($i = 0; $i < 13; $i++) {
//         $soma += $cnpj[$i] * $pesos2[$i];
//     }
//     $resto = $soma % 11;
//     $digito2 = ($resto < 2) ? 0 : 11 - $resto;

//     if ($cnpj[13] != $digito2) {
//         return false;
//     }
//     return true;
// }
// }

$stmt = $conn->prepare("INSERT INTO tb_cliente (nome, email, telefone, cnpj)
        VALUES (?, ?, ?, ?)");

$stmt -> bind_param("sss", $nome, $fone, $doc);

$stmt->execute();

$idCliente = $conn->insert_id;

$stmtEndereco = $conn->prepare(
    "INSERT INTO tb_endereco ( id_cliente, rua, bairro, numero, cidade, complemento)
        VALUES (?, ?, ?, ?, ?, ?)"
);

$stmtEndereco->bind_param(
    "isssss",
    $idCliente,
    $rua,
    $bairro,
    $numCasa,
    $cidade,
    $comp
);

$stmtEndereco->execute();

header("location: ../../frontend/index.php");
?>