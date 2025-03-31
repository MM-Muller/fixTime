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
				<h2> Exemplo CRUD fix-time </h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<a href="index.php?page=1" 
				class="btn btn-primary">Adicionar novo registro</a>
			
				<table class="table table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>ID</th>
						<th>Nome</th>
						<th>email</th>
						<th>Veiculo</th>

					</tr>
				</thead>
				<tbody>
				<?php
					$obj = conecta_db();
					$query = "SELECT * FROM tb_teste";
					$resultado = $obj->query($query);
					while($linha = $resultado->fetch_object()){
						$html = "<tr>";
						$html .= "<td>
						<a class='btn btn-danger' 
						href='index.php?page=2&id=".$linha->teste_id."'>Excluir</a>
						<a class='btn btn-success' 
						href='index.php?page=3&id=".$linha->teste_id."'>Alterar</a>
						</td>";
						$html .= "<td>".$linha->teste_id."</td>";
						$html .= "<td>".$linha->nome."</td>";
						$html .= "<td>".$linha->email."</td>";
						$html .= "<td>".$linha->veiculo."</td>";
						$html .= "</tr>";
						echo $html;
					}
				?>
				<tbody>				
				</table>
			</div>
		</div>
		
	</div>
</body>
</html>
