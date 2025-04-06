<?php 
  $db_name = "fixTime";
  $user = "root";
  $pass = "";
  $server = "localhost:3306";

  // Criar conexão
  $conexao = new mysqli($server, $user, $pass, $db_name);

  // Verificar conexão
  if ($conexao->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_usuario = $_POST['email'] ?? '';
    $senha_usuario = $_POST['senha'] ?? '';

    // Usar prepared statements para evitar SQL Injection
    $stmt = $conexao->prepare("SELECT senha_usuario FROM cliente WHERE email_usuario = ?");
    $stmt->bind_param("s", $email_usuario);
    $stmt->execute();
    $stmt->store_result();

    // Verificar se encontrou o usuário
    if ($stmt->num_rows > 0) {
      $stmt->bind_result($hash_senha);
      $stmt->fetch();

      // Verificar senha
      if (password_verify($senha_usuario, $hash_senha)) {
        echo "<script>window.location.href = '/fixTime/PROJETO/src/views/main-page/main.html';</script>";
        exit();
      } else {
        $erro = "Email ou senha inválidos.";
      }
    } else {
      $erro = "Email ou senha inválidos.";
    }

    $stmt->close();
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
        <a href="/fixTime/PROJETO/src/views/Login/choice-login.html" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Voltar</a>
    </div>

    <div class="lg:w-auto lg:max-w-full w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-md lg:pt-4 p-3 lg:p-6 md:p-8 mt-12 lg:mt-3 mb-2 mx-2">
        <div class="mb-2 flex flex-col items-center text-center">
            <img src="/fixTime/PROJETO/src/public/assets/images/fixtime-truck.png" class="h-16 w-auto">
        </div>

        <form method="POST" id="loginForm" class="space-y-3"> 
            <div class="lg:space-y-4 space-y-3">
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-900">Email</label>
                    <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="seuemail@exemplo.com.br" required />
                </div> 

                <div id="senha-container">
                    <label for="senha" class="block mb-1 text-sm font-medium text-gray-900">Senha</label>
                    <input type="password" name="senha" id="senha" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="••••••••••••" required />
                </div>
            </div>

            <button id="loginButton" type="submit" class="mt-4 cursor-pointer w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Acessar</button>

            <?php if (isset($erro)) : ?>
                <p class='text-red-500 text-sm mt-2'><?= $erro ?></p>
            <?php endif; ?>

            <div>
                <p class="text-sm font-light text-gray-500">
                    Ainda não tem conta? <a href="/fixTime/PROJETO/src/views/Login/cadastro-user.html" class="font-medium hover:underline text-blue-500">Crie seu cadastro.</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
