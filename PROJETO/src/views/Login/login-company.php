<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
  die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email_oficina = $_POST['email_oficina'] ?? '';
  $senha_oficina = $_POST['senha_oficina'] ?? '';

  // Usar prepared statements para evitar SQL Injection
  $stmt = $conexao->prepare("SELECT id_oficina, senha_oficina FROM oficina WHERE email_oficina = ?");
  $stmt->bind_param("s", $email_oficina);
  $stmt->execute();
  $stmt->store_result();

  // Verificar se encontrou o usuário
  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id_oficina, $hash_senha);
    $stmt->fetch();

    // Verificar senha
    if (password_verify($senha_oficina, $hash_senha)) {
      // Armazenar dados do usuário na sessão
      $_SESSION['id_oficina'] = $id_oficina;

      // Redirecionamento seguro
      header("Location: /fixTime/PROJETO/src/views/main-page/Oficina/main.html");
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
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fix-Time - Login Oficina</title>
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

    <?php if (isset($erro)): ?>
      <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
        <?php echo htmlspecialchars($erro); ?>
      </div>
    <?php endif; ?>

    <form class="space-y-3" method="POST" action="">
      <div class="lg:space-y-4 space-y-3">
        <div class="">
          <label for="email_oficina" class="block mb-1 text-sm font-medium text-gray-900">Email</label>
          <input type="email" name="email_oficina" id="email_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="contato@oficina.com" required />
        </div>

        <div class="" id="senha-container">
          <label for="senha_oficina" class="block mb-1 text-sm font-medium text-gray-900">Senha</label>
          <input type="password" name="senha_oficina" id="senha_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="••••••••••••" required />
        </div>
      </div>

      <button type="submit" class="mt-4 cursor-pointer w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Acessar</button>
      <div>
        <p class="text-sm font-light text-gray-500">
          Ainda não tem conta? <a href="/fixTime/PROJETO/src/views/Login/cadastro-company.php" class="font-medium hover:underline text-blue-500">Crie seu cadastro.</a>
        </p>
      </div>
    </form>
  </div>

</body>

</html>