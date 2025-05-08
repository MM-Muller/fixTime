<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

session_start();
if (!isset($_SESSION['id_oficina'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit;
}

$id_usuario = $_SESSION['id_oficina'] ?? null;
$id_oficina = $id_usuario; // Ensure $id_oficina is defined

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização segura dos dados de entrada
    $nome = isset($_POST['nome_funcionario']) ? htmlspecialchars($_POST['nome_funcionario'], ENT_QUOTES, 'UTF-8') : '';
    $cargo = isset($_POST['cargo_funcionario']) ? htmlspecialchars($_POST['cargo_funcionario'], ENT_QUOTES, 'UTF-8') : '';
    $telefone = isset($_POST['telefone_funcionario']) ? htmlspecialchars($_POST['telefone_funcionario'], ENT_QUOTES, 'UTF-8') : '';
    $email = isset($_POST['email_funcionario']) ? htmlspecialchars($_POST['email_funcionario'], ENT_QUOTES, 'UTF-8') : '';
    $data_admissao = isset($_POST['data_admissao']) ? htmlspecialchars($_POST['data_admissao'], ENT_QUOTES, 'UTF-8') : '';

    // Validação dos campos
    if (empty($nome) || empty($cargo) || empty($telefone) || empty($email) || empty($data_admissao)) {
        $mensagem = "<script>alert('Preencha todos os campos corretamente.');</script>";
    } else {
        try {
            $stmt = $conexao->prepare("INSERT INTO funcionarios (nome_funcionario, cargo_funcionario, telefone_funcionario, email_funcionario, data_admissao, id_oficina) 
                         VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $nome, $cargo, $telefone, $email, $data_admissao, $id_usuario);

            if ($stmt->execute()) {
                $mensagem = "<script>alert('Funcionário cadastrado com sucesso!');</script>";
                echo "<script>window.location.href = window.location.href;</script>";
                exit;
            } else {
                $mensagem = "<script>alert('Erro ao cadastrar funcionário: " . addslashes($stmt->error) . "');</script>";
            }

            $stmt->close();
        } catch (Exception $e) {
            $erro = $e->getMessage();

            if (str_contains($erro, 'Duplicate entry') && str_contains($erro, 'email_funcionario')) {
                $mensagem = "<script>alert('Este email já está cadastrado no sistema. Por favor, verifique os dados.');</script>";
            } else {
                $mensagem = "<script>alert('Erro no banco de dados: " . addslashes($erro) . "');</script>";
            }
        }
    }
}

// Buscar funcionários da oficina
if ($id_oficina) {
    try {
        $stmt = $conexao->prepare("SELECT id_funcionario, nome_funcionario as nome, cargo_funcionario as cargo, telefone_funcionario as telefone, 
                          email_funcionario as email, data_admissao 
                          FROM funcionarios WHERE id_oficina = ? ORDER BY id_funcionario DESC");
        $stmt->bind_param("i", $id_oficina);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $funcionarios[] = $row;
        }

        $stmt->close();
    } catch (Exception $e) {
        $mensagem = "<script>alert('Erro ao buscar funcionários: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Obtém o ID da oficina
$oficina_id = $_SESSION['id_oficina'] ?? null;

if (!$oficina_id) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-company.php';</script>";
    exit();
}

// Busca os dados atuais da oficina
$sql = "SELECT nome_oficina FROM oficina WHERE id_oficina = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $oficina_id); // associa o id da oficina
$stmt->execute();
$result = $stmt->get_result();

// verifica se encontrou a oficina
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc(); // salva os dados em um array associativo
} else {
    die("Oficina não encontrada."); // interrompe se a oficina não existir
}


?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
    <title>Fix Time</title>
</head>

<?php if (!empty($mensagem)) echo $mensagem; ?>


<body class="">

    <button id="hamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
        </svg>
    </button>

    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0">
        <div class="h-full px-3 py-4 bg-gray-50 flex flex-col justify-between">

            <div>
                <a class="flex items-center lg:justify-center justify-between ps-3 mx-auto mb-2">
                    <button id="closeHamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <svg class="w-6 h-6 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <img src="/fixTime/PROJETO/src/public/assets/images/fixtime-truck.png" class="lg:h-14 h-12 me-3 " />

                </a>

                <ul class="space-y-2 font-medium">

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>
                        </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Meus Funcionarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/perfil-oficina.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">
                                <?php
                                    $nomeCompleto = htmlspecialchars($user_data['nome_oficina']);
                                    $partes = explode(' ', $nomeCompleto);
                                    $duasPalavras = implode(' ', array_slice($partes, 0, 2));
                                    echo $duasPalavras;
                                ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/registrar-servicos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.583 8.445h.01M10.86 19.71l-6.573-6.63a.993.993 0 0 1 0-1.4l7.329-7.394A.98.98 0 0 1 12.31 4l5.734.007A1.968 1.968 0 0 1 20 5.983v5.5a.992.992 0 0 1-.316.727l-7.44 7.5a.974.974 0 0 1-1.384.001Z"/>
                        </svg>


                            <span class="flex-1 ms-3 whitespace-nowrap">Registrar serviços</span>
                        </a>
                    </li>

                </ul>
            </div>

            <a href="/fixTime/PROJETO/src/views/Login/logout.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100 group">
                <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                </svg>

                <span class="flex-1 ms-3 whitespace-nowrap font-medium">Logout</span>
            </a>
        </div>

    </aside>

    <div class=" lg:ml-64 p-4 lg:p-14">

    </div>


            <script>
                // Menu Hamburguer 
                const hamburgerButton = document.getElementById('hamburgerButton');
                const closeHamburgerButton = document.getElementById('closeHamburgerButton');
                const sidebar = document.getElementById('sidebar');

                hamburgerButton.addEventListener('click', () => {
                    sidebar.classList.toggle('-translate-x-full');
                });

                closeHamburgerButton.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                });
            
              
            </script>

</body>

</html>