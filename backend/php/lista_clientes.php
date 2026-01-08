<?php include 'conexao.php';

    $sql = "SELECT * from tb_cliente";
    $results = $conn -> query($sql);
    $clients = $results->fetch_all(MYSQLI_ASSOC);

    foreach($clients as $value) {
        echo "
        <tr>
            <td>" . $value['nome'] . "</td>
            <td>" . $value['telefone'] . "</td>
            <td>" . $value['cpf_cnpj'] . "</td>
            <td>
                <div class='action-buttons'>
                    <a href='../backend/php/ficha_cliente.php?id={$value['id_cliente']}' class='btn btn-primary btn-small'>Ver Ficha</a>
                    <button class='btn btn-danger btn-small'>Excluir</button>
                </div>
            </td>
        </tr>
         "; } ?>