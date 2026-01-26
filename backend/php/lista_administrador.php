<?php 
    include 'conexao.php';

   $sql = "SELECT * from tb_admin_cond ORDER BY nome ASC";
   $results = $conn -> query($sql);
   $adm = $results -> fetch_all(MYSQLI_ASSOC) ;

    function selectAdm(){
        global $adm;
        foreach($adm as $value) {
            echo "
                <option value='{$value['id_admin']}'>
                    {$value['nome']}
                </option>";
        }
   }

   function listaAdm(){
    global $adm;
    foreach($adm as $value) {
        // if ($value['deleted_at'] == NULL) {
        echo "
            <tr>
            <td>" . $value['nome'] . "</td>
            <td>" . formatarTelefone($value['telefone']) . "</td>
            <td>" . $value['email'] . "</td>
            <td>
                <div class='action-buttons'>
                    <button class='btn btn-primary btn-small' onclick=\"showPage('ficha', this); carregarFicha(<? {$value['id_admin']} ?>)\">Ficha</button>
                    <button class='btn btn-primary btn-small' onclick=\"editarAdmin(" . intval($value['id_admin']) . ")\">Editar</button>";
                    if ($value['deleted_at'] == NULL) {
                    echo "<button class='btn btn-danger btn-small' onclick=\"alterarStatus(" . intval($value['id_admin']) . ", 'admin', 'desativar')\">Desativar</button>";
                    } 
                    else {
                    echo "<button class='btn btn-success btn-small' onclick=\"alterarStatus(" . intval($value['id_admin']) . ", 'admin', 'ativar')\">Ativar</button>";
                    }
                    echo "
                </div>
            </td>
        </tr>";
        }
    }
//    }
?>