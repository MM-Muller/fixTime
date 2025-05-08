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
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
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

        <!-- cadastrar funcionários -->
        <div>
            <form action="/fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php" method="POST">
                <div class="grid lg:gap-6 gap-4 mb-6 md:grid-cols-6">
                    <!-- Nome -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="nome_funcionario" class="block mb-2 text-sm font-medium text-gray-900">Nome Completo</label>
                        <input name="nome_funcionario" type="text" id="nome_funcionario" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: João Silva" required>
                    </div>

                    <!-- Cargo -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="cargo_funcionario" class="block mb-2 text-sm font-medium text-gray-900">Cargo</label>
                        <select name="cargo_funcionario" id="cargo_funcionario" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none cursor-pointer" required>
                            <option value="">Selecione</option>
                            <option value="Borracheiro">Borracheiro</option>
                            <option value="Eletricista">Eletricista</option>
                            <option value="Mecânico">Mecânico</option>
                            <option value="Lavador de automoveis">Lavador de automoveis</option>
                        </select>
                    </div>

                    <!-- Telefone -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="telefone_funcionario" class="block mb-2 text-sm font-medium text-gray-900">Telefone</label>
                        <input name="telefone_funcionario" type="text" id="telefone_funcionario" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: (11) 99999-9999" required>
                    </div>

                    <!-- Email -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="email_funcionario" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input name="email_funcionario" type="email" id="email_funcionario" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: funcionario@email.com" required>
                    </div>

                    <!-- Data Admissão -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="data_admissao" class="block mb-2 text-sm font-medium text-gray-900">Data Admissão</label>
                        <input name="data_admissao" type="date" id="data_admissao" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" required>
                    </div>

                    <!-- Botão -->
                    <div class="lg:col-span-2 flex col-span-6">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:w-full w-auto px-5 py-2.5 text-center cursor-pointer mt-7">Registrar</button>
                    </div>
                </div>
            </form>

            <?php if (!empty($funcionarios)): ?>
                <hr class="h-px my-8 bg-gray-200 border-0">
                <h1 class="text-xl font-bold mt-10 mb-4 text-center">Funcionários cadastrados</h1>

                <?php foreach ($funcionarios as $funcionario): ?>
                    <div class="mt-6" id="funcionario-<?= $funcionario['id_funcionario'] ?>">

                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <form action="atualizar_funcionario.php" method="POST" class="form-funcionario">
                                <input type="hidden" name="id" value="<?= $funcionario['id_funcionario'] ?>">

                                <div class="grid lg:gap-6 gap-4 mb-6 lg:grid-cols-6 grid-cols-2">

                                    <div class="col-span-1">
                                        <label class="block mb-1 text-sm font-medium text-gray-900">ID</label>
                                        <input type="text" value="<?= htmlspecialchars($funcionario['id_funcionario']) ?>" class="campo-id focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 cursor-not-allowed" disabled />
                                    </div>

                                    <div class="lg:col-span-3 col-span-1">
                                        <label for="nome-<?= $funcionario['id_funcionario'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Nome</label>
                                        <input name="nome" type="text" id="nome-<?= $funcionario['id_funcionario'] ?>" value="<?= htmlspecialchars($funcionario['nome']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="lg:col-span-2 col-span-1">
                                        <label for="cargo-<?= $funcionario['id_funcionario'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Cargo</label>
                                        <select name="cargo" id="cargo-<?= $funcionario['id_funcionario'] ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                            <option value="Borracheiro" <?= $funcionario['cargo'] == 'Borracheiro' ? 'selected' : '' ?>>Borracheiro</option>
                                            <option value="Eletricista" <?= $funcionario['cargo'] == 'Eletricista' ? 'selected' : '' ?>>Eletricista</option>
                                            <option value="Mecânico" <?= $funcionario['cargo'] == 'Mecânico' ? 'selected' : '' ?>>Mecânico</option>
                                            <option value="Lavador de automoveis" <?= $funcionario['cargo'] == 'Lavador de automoveis' ? 'selected' : '' ?>>Lavador de automoveis</option>
                                        </select>
                                    </div>

                                    <div class="lg:col-span-1 col-span-1">
                                        <label for="telefone-<?= $funcionario['id_funcionario'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Telefone</label>
                                        <input name="telefone" type="text" id="telefone-<?= $funcionario['id_funcionario'] ?>" value="<?= htmlspecialchars($funcionario['telefone']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="lg:col-span-3 col-span-1">
                                        <label for="email-<?= $funcionario['id_funcionario'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Email</label>
                                        <input name="email" type="email" id="email-<?= $funcionario['id_funcionario'] ?>" value="<?= htmlspecialchars($funcionario['email']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="lg:col-span-2 col-span-1">
                                        <label for="data_admissao-<?= $funcionario['id_funcionario'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Data Admissão</label>
                                        <input name="data_admissao" type="date" id="data_admissao-<?= $funcionario['id_funcionario'] ?>" value="<?= htmlspecialchars($funcionario['data_admissao']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>
                                </div>

                                <div class="lg:gap-6 gap-4 items-center grid grid-cols-6">
                                    <!-- Botão Editar/Salvar (alterna entre os dois estados) -->
                                    <button type="button" class="editar-btn text-white inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3" data-id="<?= $funcionario['id_funcionario'] ?>">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Editar
                                    </button>

                                    <!-- Botão Excluir/Cancelar (alterna entre os dois estados) -->
                                    <button type="button" class="excluir-btn inline-flex items-center justify-center gap-2 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3" data-id="<?= $funcionario['id_funcionario'] ?>">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Excluir
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <hr class="h-px my-8 bg-gray-300 border-0">
                <div class="mt-10 p-4 rounded-lg bg-gray-100 border-2 border-gray-300 shadow-xl flex items-center justify-between ">
                    <div>
                        <p class="font-medium">Nenhum funcionário cadastrado.</p>
                        <p class="text-sm">Adicione seu primeiro funcionário usando o formulário acima.</p>
                    </div>
                </div>
            <?php endif; ?>

            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
            <script>
                $(document).ready(function() {
                    // Máscara para telefone
                    $('#telefone_funcionario').mask('(00) 00000-0000');

                    // Configurar máscaras para os campos de edição
                    $('input[id^="telefone-"]').each(function() {
                        $(this).mask('(00) 00000-0000');
                    });
                });
            </script>


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



                // Controle de edição de funcionários
                document.querySelectorAll('.editar-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const form = this.closest('.form-funcionario');
                        const inputs = form.querySelectorAll('input:not([type="hidden"]):not(.campo-id), select');
                        const isEditing = this.textContent.trim() === 'Editar';

                        if (isEditing) {
                            inputs.forEach(input => {
                                input.disabled = false;
                                input.classList.remove('cursor-not-allowed'); // remove o cursor bloqueado
                            });



                            // Habilitar edição
                            inputs.forEach(input => input.disabled = false);
                            this.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Salvar
            `;
                            this.classList.remove('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-blue-300');
                            this.classList.add('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-blue-300');

                            // Mudar botão Excluir para Cancelar
                            const excluirBtn = form.querySelector('.excluir-btn');
                            excluirBtn.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Cancelar
            `;
                            excluirBtn.classList.remove('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-blue-300');
                            excluirBtn.classList.add('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-blue-300');
                        } else {
                            // Enviar formulário para salvar
                            form.submit();
                        }
                    });
                });

                // Controle de exclusão/cancelamento
                document.querySelectorAll('.excluir-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const form = this.closest('.form-veiculo');

                        if (this.textContent.trim() === 'Excluir') {
                            if (confirm('Tem certeza que deseja excluir este funcionário?')) {
                                // Criar formulário temporário para exclusão
                                const deleteForm = document.createElement('form');
                                deleteForm.action = 'excluir_funcionario.php';
                                deleteForm.method = 'POST';

                                const inputId = document.createElement('input');
                                inputId.type = 'hidden';
                                inputId.name = 'id';
                                inputId.value = id;

                                deleteForm.appendChild(inputId);
                                document.body.appendChild(deleteForm);
                                deleteForm.submit();
                            }
                        } else {
                            // Cancelar edição
                            const inputs = form.querySelectorAll('input:not([type="hidden"]), select');
                            inputs.forEach(input => input.disabled = true);

                            // Resetar botão Editar
                            const editarBtn = form.querySelector('.editar-btn');
                            editarBtn.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                </svg>
                Editar
            `;
                            editarBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                            editarBtn.classList.add('bg-blue-700', 'hover:bg-blue-800');

                            // Resetar botão Cancelar para Excluir
                            this.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Excluir
            `;
                            this.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                            this.classList.add('bg-red-600', 'hover:bg-red-700');

                            // Recarregar o formulário para descartar alterações
                            form.reset();
                        }
                    });
                });
            </script>

</body>

</html>