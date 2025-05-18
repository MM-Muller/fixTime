<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
  die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email_funcionario = $_POST['email_funcionario'] ?? '';
  $cpf_funcionario = $_POST['cpf_funcionario'] ?? '';

  // Prepared statement seguro
  $stmt = $conexao->prepare("SELECT id_funcionario, cpf_funcionario FROM funcionarios WHERE email_funcionario = ?");
  $stmt->bind_param("s", $email_funcionario);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id_funcionario, $cpf_banco);
    $stmt->fetch();

    // Se CPF for armazenado em texto plano
    if ($cpf_funcionario === $cpf_banco) {
      $_SESSION['id_funcionario'] = $id_funcionario;
      header("Location: /fixTime/PROJETO/src/views/main-page/Funcionario/main-funcionario.php");
      exit();
    } else {
      $erro = "Email ou CPF inválidos.";
    }
  } else {
    $erro = "Email ou CPF inválidos.";
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

  <div class="lg:w-auto lg:max-w-full w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-md lg:pt-4 p-3 lg:p-10 md:p-8 mt-12 lg:mt-3 mb-2 mx-2">
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
          <label for="email_funcionario" class="block mb-1 text-sm font-medium text-gray-900">Email</label>
          <input type="email" name="email_funcionario" id="email_funcionario" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="joao@oficina.com" required />
        </div>

        <div class="" id="senha-container">
          <label for="cpf_funcionario" class="block mb-1 text-sm font-medium text-gray-900">CPF</label>
          <input type="text" name="cpf_funcionario" id="cpf_funcionario" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="123.456.789-10" required />
        </div>
      </div>

      <button type="submit" class="mt-4 cursor-pointer w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center ">Acessar</button>
      
    </form>
  </div>
</body>

</html>