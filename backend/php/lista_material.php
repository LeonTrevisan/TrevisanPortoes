<?php
    include 'conexao.php';

        $mes = $_POST['filtro-mes'] ?? date('m');
            $sql = "SELECT * FROM tb_compras c
            LEFT JOIN tb_distribuidora t ON c.id_distribuidora = t.id_distribuidora
            WHERE MONTH(c.data_compra) = '$mes'
            ORDER BY c.material ASC";

    $results = $conn -> query($sql);
    $material = $results->fetch_all(MYSQLI_ASSOC);

    foreach($material as $value) {
        echo "
        <tr>
            <td>" . $value['data_compra'] . "</td>
            <td>" . $value['material'] . "</td>
            <td>". $value['nome_distribuidora'] . "</td>
            <td>". $value['qtd_compra'] . "</td>
            <td>" . $value['valor_un'] . "</td>
            <td>" . $value['valor_total'] . "</td>
            <td>
                <div class='action-buttons'>
                    <button class='btn btn-primary btn-small' onclick=\"editarCompra(" . intval($value['id_compra']) . ")\" >Editar</button>
                    <button class='btn btn-danger btn-small' onclick=\"confirmarExclusao(" . intval($value['id_compra']) . ", 'compra')\">Excluir</button>
                </div>
            </td>
        </tr>
         "; } 
    
?>

