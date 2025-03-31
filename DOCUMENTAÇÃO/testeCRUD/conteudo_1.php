<?php
	if(isset($_POST['nome'], $_POST['email'], $_POST['veiculo'])) {
		$obj = conecta_db();
		$nome = $obj->real_escape_string($_POST['nome']);
		$email = $obj->real_escape_string($_POST['email']);
		$veiculo = $obj->real_escape_string($_POST['veiculo']);
		$query = "INSERT INTO tb_teste(nome, email, veiculo) 
				  VALUES ('$nome', '$email', '$veiculo')";
		$resultado = $obj->query($query);
		if($resultado) {
			header("location: index.php");
		} else {
			echo "<span class='alert alert-danger'>Não funcionou!</span>";
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Meu primeiro CRUD</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2> CRUD - Insert </h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
			
			<form method="POST" action="index.php?page=1">
				<input type="text"
					   name="nome"
					   class="form-control"
					   placeholder="Digite seu nome aqui.">
				<input type="email"
					   name="email"
					   class="form-control mt-2"
					   placeholder="Digite seu email aqui.">
				<input type="text"
					   name="veiculo"
					   class="form-control mt-2"
					   placeholder="Digite o veículo aqui.">
				<button type="submit" 
						class="mt-2 btn btn-primary">Enviar</button>
			</form>
			</div>
		</div>
		
		
	</div>
</body>
</html>
