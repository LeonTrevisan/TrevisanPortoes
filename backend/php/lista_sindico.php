<?php 
    include 'conexao.php';

   $sql = "SELECT * from tb_sindico";
   $results = $conn -> query($sql);
   $sindico = $results -> fetch_all(MYSQLI_ASSOC) ;

   foreach($sindico as $value) {
    echo "
        <option value='{$value['id_sindico']}'>
         {$value['nome']}
          </option>
        ";
   }
?>