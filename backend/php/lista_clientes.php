<?php include 'conexao.php';

    $sql = "SELECT * FROM tb_cliente c
            LEFT JOIN tb_tipo_cliente t ON c.id_tipo_cliente = t.id_tipo_cliente
            ORDER BY c.id_tipo_cliente DESC, c.nome ASC, c.deleted_at ASC";
    $results = $conn -> query($sql);
    $clients = $results->fetch_all(MYSQLI_ASSOC);

    foreach($clients as $value) {
        echo "
        <tr>
            <td>" . $value['nome'] . "</td>
            <td>" . formatarTelefone($value['telefone']) . "</td>";
            if (!empty($value['cnpj'])) {
            echo "<td> <a href=\"../backend/php/visualizar_cnpj.php?id={$value['id_cliente']}\"> <button class=\"btn-cnpj\"> Visualizar CNPJ </button> </a></td>";}
            else { 
            echo "<td> Não possui CNPJ </td>"; }
        echo "
            <td>" . $value['tipo_cliente'] . "</td>
            <td>
                <div class='action-buttons'>
                    <button class='btn btn-primary btn-small' onclick=\"editarCliente(" . intval($value['id_cliente']) . ")\">Editar</button>";
                    if ($value['deleted_at'] == NULL) {
                    echo "<button class='btn btn-danger btn-small' onclick=\"alterarStatus(" . intval($value['id_cliente']) . ", 'cliente', 'desativar')\">Desativar</button>";
                    } 
                    else {
                    echo "<button class='btn btn-success btn-small' onclick=\"alterarStatus(" . intval($value['id_cliente']) . ", 'cliente', 'ativar')\">Ativar</button>";
                    }
                    echo "
                </div>
            </td>
        </tr>
         "; } 
    ?>