<?php 
    include 'conexao.php';

   $sql = "SELECT * from tb_admin_cond";
   $results = $conn -> query($sql);
   $adm = $results -> fetch_all(MYSQLI_ASSOC) ;

   foreach($adm as $value) {
    echo "
        <option value='{$value['id_admin']}'>
         {$value['nome']}
          </option>
        ";
   }
?>