<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['nome'], $_POST['email'], $_POST['veiculo'])) {
			$obj = conecta_db();
			$nome = $obj->real_escape_string($_POST['nome']);
			$email = $obj->real_escape_string($_POST['email']);
			$veiculo = $obj->real_escape_string($_POST['veiculo']);
			$id = $obj->real_escape_string($_GET['id']);
			
			$query = "UPDATE tb_teste
					  SET nome = '$nome', email = '$email', veiculo = '$veiculo'
					  WHERE teste_id = '$id'";
			
			$resultado = $obj->query($query);
			
			if ($resultado) {
				header("Location: index.php");
				exit;
			} else {
				echo "<span class='alert alert-danger'>Erro ao atualizar os dados!</span>";
			}
		} else {
			echo "<span class='alert alert-warning'>Preencha todos os campos!</span>";
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
				<h2> CRUD - UPDATE - Alterando o ID: 
				<?php echo $_GET['id'];?> </h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
			
			<form 
			method="POST" 
			action="index.php?page=3&id=<?php echo $_GET['id'];?>">
			
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
