<?php 
    include 'conexao.php';

    $sql = "SELECT * from tb_sindico order by nome ASC, deleted_at ASC";
    $results = $conn -> query($sql);
    $sindico = $results -> fetch_all(MYSQLI_ASSOC) ;

    function selectSindico(){
            global $sindico;
            foreach($sindico as $value) {
                if ($value['deleted_at'] === NULL) {
                    echo "
                        <option value='{$value['id_sindico']}'>
                            {$value['nome']}
                        </option>";
                }
            }
        }

    function listaSindico(){
        global $sindico;
        foreach($sindico as $value) {
            echo "
                <tr>
                    <td>" . $value['nome'] . "</td>
                    <td>" . formatarTelefone($value['telefone']) . "</td>
                    <td>
                        <div class='action-buttons'>
                            <button class='btn btn-primary btn-small' onclick=\"showPage('ficha', this); carregarFicha(<? {$value['id_sindico']} ?>)\">Ficha</button>
                            <button class='btn btn-primary btn-small' onclick=\"editarSindico(" . intval($value['id_sindico']) . ")\">Editar</button>";
                            if ($value['deleted_at'] == NULL) {
                            echo "<button class='btn btn-danger btn-small' onclick=\"alterarStatus(" . intval($value['id_sindico']) . ", 'sindico', 'desativar')\">Desativar</button>";
                            } 
                            else {
                            echo "<button class='btn btn-success btn-small' onclick=\"alterarStatus(" . intval($value['id_sindico']) . ", 'sindico', 'ativar')\">Ativar</button>";
                            }
                            echo "
                        </div>
                    </td>
                </tr>";
        }
    }
?>