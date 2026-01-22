<?php 
    include 'conexao.php';

   $sql = "SELECT * from tb_sindico";
   $results = $conn -> query($sql);
   $sindico = $results -> fetch_all(MYSQLI_ASSOC) ;

   function selectSindico(){
    global $sindico;
    foreach($sindico as $value) {
        echo "
            <option value='{$value['id_sindico']}'>
                {$value['nome']}
            </option>";
    }
   }

   function listaSindico(){
    global $sindico;
    foreach($sindico as $value) {
        echo "
            <tr>
            <td>" . $value['nome'] . "</td>
            <td>" . $value['telefone'] . "</td>
            <td>
                <div class='action-buttons'>
                    <button class='btn btn-primary btn-small' onclick=\"showPage('ficha', this); carregarFicha(<? {$value['id_sindico']} ?>)\">Ficha</button>
                    <button class='btn btn-primary btn-small' onclick=\"editarSindico(" . intval($value['id_sindico']) . ")\">Editar</button>
                    <button class='btn btn-danger btn-small' onclick=\"confirmarExclusao(" . intval($value['id_sindico']) . ", 'sindico')\">Excluir</button>
                </div>
            </td>
        </tr>";
    }
   }
?>