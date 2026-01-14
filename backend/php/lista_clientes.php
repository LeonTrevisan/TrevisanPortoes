<?php include 'conexao.php';

    $sql = "SELECT * FROM tb_cliente c
            LEFT JOIN tb_tipo_cliente t ON c.id_tipo_cliente = t.id_tipo_cliente
            ORDER BY c.id_tipo_cliente DESC, c.nome ASC";
    $results = $conn -> query($sql);
    $clients = $results->fetch_all(MYSQLI_ASSOC);

    foreach($clients as $value) {
        echo "
        <tr>
            <td>" . $value['nome'] . "</td>
            <td>" . $value['telefone'] . "</td>";
            if (!empty($value['cnpj'])) {
            echo "<td> <a href=\"../backend/php/visualizar_cnpj.php?id={$value['id_cliente']}\"> <button class=\"btn-cnpj\"> Visualizar CNPJ </button> </a></td>";}
            else { 
            echo "<td> Não possui CNPJ </td>"; }
        echo"
            <td>" . $value['tipo_cliente'] . "</td>
            <td>
                <div class='action-buttons'>
                    <button data-id=\"<?=id={$value['id_cliente']}\" onclick=\"showPage('ficha')\" class='btn btn-primary btn-small'>Ver Ficha</button>
                    <button class='btn btn-danger btn-small'>Excluir</button>
                </div>
            </td>
        </tr>
         "; } ?>