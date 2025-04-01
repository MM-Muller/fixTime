<?php
// Configurações do banco de dados
$db_name = "fixTime";
$user = "root";
$pass = "";
$server = "localhost:3306";

// Criar conexão
$conexao = new mysqli($server, $user, $pass, $db_name);

// Verificar conexão (corrigido para usar $conexao)
if ($conexao->connect_error) {
  die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = $conexao->real_escape_string($_POST['first_name']);
  $cpf = $conexao->real_escape_string($_POST['cpf']);
  $telefone = $conexao->real_escape_string($_POST['telefone']);
  $email = $conexao->real_escape_string($_POST['email']);
  $senha = $conexao->real_escape_string($_POST['senha']);

  // Hash da senha
  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

  // Inserir dados no banco
  $sql = "INSERT INTO cliente (nome, cpf, telefone, email, senha) VALUES ('$nome', '$cpf', '$telefone', '$email', '$senha_hash')";

  if ($conexao->query($sql) === TRUE) {
  echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
  } else {
  echo "Erro: " . $sql . "<br>" . $conexao->error;
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix-Time</title>
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen lg:p-0 p-3">
    <div class="absolute top-0 left-0 p-4">     
        <a href="/fixTime/PROJETO/src/views/Login/choice-cadastro.html" class=" text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5  focus:outline-none">Voltar</a>
    </div>

  <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm p-3 md:p-8 lg:p-4 ">
    
    <form class="lg:space-y-2 space-y-3" action="/fixTime/PROJETO/src/views/Login/cadastro-user.php" method="POST">

        <div>
          <label for="first_name" class="block mb-1 text-sm font-medium text-gray-900 ">Nome completo</label>
          <input type="text" id="first_name" name="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2 " placeholder="Marcos da Silva" required/>
        </div>

        <div>
          <label for="cpf" class="block mb-1 text-sm font-medium text-gray-900 ">CPF</label>
          <input type="tel" id="cpf" name="cpf" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="123.456.789-09"  required />
        </div>

        <div>
          <label for="telefone" class="block mb-1 text-sm font-medium text-gray-900 ">Número de telefone</label>
          <input type="tel" id="telefone" name="telefone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="(41) 99988-7766"  required />
        </div>

        <div>
          <label for="email" class="block mb-1 text-sm font-medium text-gray-900 ">Email</label>
          <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="john.doe@company.com" required />
        </div> 

        <div class="col-span-2" id="senha-container">
          <label for="senha" class="block mb-1 text-sm font-medium text-gray-900">Senha</label>
          <input type="password" id="senha" name="senha" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="••••••••••••" required />
        </div>
        
        <div class="col-span-2" id="confirma-senha-container">
          <label for="confirma_senha" class="block mb-1 text-sm font-medium text-gray-900">Confirmar senha</label>
          <input type="password" id="confirma_senha" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="••••••••••••" required />
          <p id="error-message" class="text-red-500 text-sm mt-1 hidden ">As senhas não coincidem. Tente novamente.</p>
        </div>
        
        <button type="submit" class=" mt-4 cursor-pointer w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Cadastrar</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
  
  <script src="/fixTime/PROJETO/src/public/assets/js/script.js"></script>
</body>
</html>