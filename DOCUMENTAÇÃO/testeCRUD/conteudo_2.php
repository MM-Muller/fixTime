<?php
	if(isset($_GET['id'])){
		$obj = conecta_db();
		$query = "DELETE FROM tb_teste WHERE teste_id = ".$_GET['id'];
		$resultado = $obj->query($query);
		header("location:index.php");
	}else{
		echo "Algo deu errado.";
	}
?>