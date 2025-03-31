<?php
	include 'biblioteca.php';
	include 'header.php';
	include 'conecta_db.php';

	if(isset($_GET['page'])){
		if($_GET['page'] == 1){
			include 'conteudo_1.php';
		}else if($_GET['page'] == 2){
			include 'conteudo_2.php';
		}else if($_GET['page'] == 3){
			include 'conteudo_3.php';
		}else{
			header('location: index.php');
		}
	}else{
		include 'main.php';	
	}
	
	
?>