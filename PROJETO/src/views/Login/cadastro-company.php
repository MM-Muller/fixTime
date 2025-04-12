<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
  die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitização dos dados
  $categoria = $conexao->real_escape_string($_POST['categoria']);
  $nome_oficina = $conexao->real_escape_string($_POST['nome_oficina']);
  $cnpj = $conexao->real_escape_string($_POST['cnpj']);
  $cep_oficina = $conexao->real_escape_string($_POST['cep_oficina']);
  $endereco_oficina = $conexao->real_escape_string($_POST['endereco_oficina']);
  $numero_oficina = $conexao->real_escape_string($_POST['numero_oficina']);
  $complemento = $conexao->real_escape_string($_POST['complemento'] ?? '');
  $bairro_oficina = $conexao->real_escape_string($_POST['bairro_oficina']);
  $cidade_oficina = $conexao->real_escape_string($_POST['cidade_oficina']);
  $estado_oficina = $conexao->real_escape_string($_POST['estado_oficina']);
  $telefone_oficina = $conexao->real_escape_string($_POST['telefone_oficina']);
  $email_oficina = $conexao->real_escape_string($_POST['email_oficina']);
  $senha_oficina = $conexao->real_escape_string($_POST['senha_oficina']);

  // Verifica CNPJ
  $verificaCnpj = "SELECT cnpj FROM oficina WHERE cnpj = '$cnpj'";
  $resultadoCnpj = $conexao->query($verificaCnpj);

  if ($resultadoCnpj->num_rows > 0) {
    echo "<script>alert('CNPJ já cadastrado. Faça login ou use outro CNPJ.'); window.location.href='/fixTime/PROJETO/src/views/Login/cadastro-company.php';</script>";
    exit();
  }

  // Verifica email
  $verificaEmail = "SELECT email_oficina FROM oficina WHERE email_oficina = '$email_oficina'";
  $resultadoEmail = $conexao->query($verificaEmail);
  if ($resultadoEmail->num_rows > 0) {
    echo "<script>alert('E-mail já cadastrado. Faça login ou use outro e-mail.'); window.location.href='/fixTime/PROJETO/src/views/Login/cadastro-company.php';</script>";
    exit();
  }

  // Hash da senha
  $senha_hash = password_hash($senha_oficina, PASSWORD_DEFAULT);

  // Inserir dados no banco
  $sql = "INSERT INTO oficina (
                categoria, 
                nome_oficina, 
                cnpj, 
                cep_oficina, 
                endereco_oficina, 
                numero_oficina, 
                complemento, 
                bairro_oficina, 
                cidade_oficina, 
                estado_oficina, 
                telefone_oficina, 
                email_oficina, 
                senha_oficina
            ) VALUES (
                '$categoria', 
                '$nome_oficina', 
                '$cnpj', 
                '$cep_oficina', 
                '$endereco_oficina', 
                '$numero_oficina', 
                '$complemento', 
                '$bairro_oficina', 
                '$cidade_oficina', 
                '$estado_oficina', 
                '$telefone_oficina', 
                '$email_oficina', 
                '$senha_hash'
            )";

  if ($conexao->query($sql) === TRUE) {
    header("Location: /fixTime/PROJETO/src/views/Login/login-company.php");
    echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    exit();
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
    <a href="/fixTime/PROJETO/src/views/Login/choice-cadastro.html" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Voltar</a>
  </div>

  <div class="lg:w-auto lg:max-w-full w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-md lg:pt-4 p-3 lg:p-6 md:p-8 mt-12 lg:mt-3 mb-2 mx-2">
    <form class="space-y-3" method="POST" action="#">

      <h1 class="block mb-2 text-md font-medium text-gray-900">Selecione a categoria do seu negócio:</h1>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="flex items-center">
          <input id="borracharia" type="radio" name="categoria" value="Borracharia" class="cursor-pointer w-4 h-4 text-blue-600 bg-gray-100 border-gray-300" required>
          <label for="borracharia" class="ms-1 text-sm font-medium text-gray-900">Borracharia</label>
        </div>

        <div class="flex items-center">
          <input id="mecanica" type="radio" name="categoria" value="Oficina Mecânica" class="cursor-pointer w-4 h-4 text-blue-600 bg-gray-100 border-gray-300">
          <label for="mecanica" class="ms-1 text-sm font-medium text-gray-900">Oficina Mecânica</label>
        </div>

        <div class="flex items-center">
          <input id="auto_eletrica" type="radio" name="categoria" value="Auto Elétrica" class="cursor-pointer w-4 h-4 text-blue-600 bg-gray-100 border-gray-300">
          <label for="auto_eletrica" class="ms-1 text-sm font-medium text-gray-900">Auto Elétrica</label>
        </div>

        <div class="flex items-center">
          <input id="lava_car" type="radio" name="categoria" value="Lava Car" class="cursor-pointer w-4 h-4 text-blue-600 bg-gray-100 border-gray-300">
          <label for="lava_car" class="ms-1 text-sm font-medium text-gray-900">Lava Car</label>
        </div>
      </div>

      <div class="lg:grid lg:grid-cols-4 lg:gap-x-6 lg:gap-y-2 lg:space-y-0 space-y-3">
        <div class="col-span-2">
          <label for="nome_oficina" class="block mb-1 text-sm font-medium text-gray-900">Nome da oficina</label>
          <input type="text" name="nome_oficina" id="nome_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="Oficina Bacacheri" required />
        </div>

        <div class="col-span-2">
          <label for="cnpj" class="block mb-1 text-sm font-medium text-gray-900">CNPJ</label>
          <input type="tel" name="cnpj" id="cnpj" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="12.345.678/0001-95" required />
        </div>

        <div class="col-span-2">
          <label for="cep_oficina" class="block mb-1 text-sm font-medium text-gray-900">CEP</label>
          <input type="tel" name="cep_oficina" id="cep_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="01001-000" required onblur="consultarCep()" />
        </div>

        <div class="col-span-2">
          <label for="telefone_oficina" class="block mb-1 text-sm font-medium text-gray-900">Telefone</label>
          <input type="tel" name="telefone_oficina" id="telefone_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="(41) 99988-7766" required />
        </div>

        <div class="col-span-2">
          <label for="endereco_oficina" class="block mb-1 text-sm font-medium text-gray-900">Endereço</label>
          <input type="text" name="endereco_oficina" id="endereco_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="Rua Holanda" required />
        </div>

        <div class="col-span-1">
          <label for="numero_oficina" class="block mb-1 text-sm font-medium text-gray-900">Número</label>
          <input type="text" name="numero_oficina" id="numero_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="812" required />
        </div>

        <div class="col-span-1">
          <label for="complemento" class="block mb-1 text-sm font-medium text-gray-900">Complemento</label>
          <input type="text" name="complemento" id="complemento" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="Sala 12" />
        </div>

        <div class="col-span-1">
          <label for="estado_oficina" class="block mb-1 text-sm font-medium text-gray-900">Estado</label>
          <input type="text" name="estado_oficina" id="estado_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="PR" required />
        </div>

        <div class="col-span-1">
          <label for="bairro_oficina" class="block mb-1 text-sm font-medium text-gray-900">Bairro</label>
          <input type="text" name="bairro_oficina" id="bairro_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="Cristo Rei" required />
        </div>

        <div class="col-span-2">
          <label for="cidade_oficina" class="block mb-1 text-sm font-medium text-gray-900">Cidade</label>
          <input type="text" name="cidade_oficina" id="cidade_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="Curitiba" required />
        </div>

        <div class="col-span-4">
          <label for="email_oficina" class="block mb-1 text-sm font-medium text-gray-900">Email</label>
          <input type="email" name="email_oficina" id="email_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="contato@oficina.com" required />
        </div>

        <div class="col-span-2" id="senha-container">
          <label for="senha_oficina" class="block mb-1 text-sm font-medium text-gray-900">Senha</label>
          <input type="password" name="senha_oficina" id="senha_oficina" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="••••••••••••" required />
        </div>

        <div class="col-span-2" id="confirma-senha-container">
          <label for="confirma_senha" class="block mb-1 text-sm font-medium text-gray-900">Confirmar senha</label>
          <input type="password" id="confirma_senha" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none focus:ring-2 block w-full p-2" placeholder="••••••••••••" required />
          <p id="error-message" class="text-red-500 text-sm mt-2 hidden">As senhas não coincidem. Tente novamente.</p>
        </div>
      </div>

      <button type="submit" class="mt-4 cursor-pointer w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Cadastrar</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
  <script src="/fixTime/PROJETO/src/public/assets/js/script.js"></script>

  <script>
    // Adicione máscaras para os campos
    $(document).ready(function() {
      $('#cnpj').mask('00.000.000/0000-00');
      $('#cep_oficina').mask('00000-000');
      $('#telefone_oficina').mask('(00) 00000-0000');

      // Validação de senha
      $('#confirma_senha').on('keyup', function() {
        if ($('#senha_oficina').val() != $('#confirma_senha').val()) {
          $('#error-message').removeClass('hidden');
          $('button[type="submit"]').prop('disabled', true);
        } else {
          $('#error-message').addClass('hidden');
          $('button[type="submit"]').prop('disabled', false);
        }
      });
    });
  </script>
</body>

</html>