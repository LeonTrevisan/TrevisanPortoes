<?php include 'conexao.php';

    $sql = "SELECT * FROM tb_cliente c
            LEFT JOIN tb_tipo_cliente t ON c.id_tipo_cliente = t.id_tipo_cliente";
    $results = $conn -> query($sql);
    $clients = $results->fetch_all(MYSQLI_ASSOC);

    foreach($clients as $value) {
        echo "
        <tr>
            <td>" . $value['nome'] . "</td>
            <td>" . $value['telefone'] . "</td>
            <td>" . $value['cnpj'] . "</td>
            <td>" . $value['tipo_cliente'] . "</td>
            <td>
                <div class='action-buttons'>
                    <a href='index.php?id={$value['id_cliente']}' class='btn btn-primary btn-small'>Ver Ficha</a>
                    <button class='btn btn-danger btn-small'>Excluir</button>
                </div>
            </td>
        </tr>
         "; } ?>