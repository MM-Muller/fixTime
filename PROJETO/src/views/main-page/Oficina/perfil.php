<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db(); // conecta com o bd

if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
} // verifica se a conexão deu tudo certo

// inicia sessão
session_start();

// OBTÉM O ID DA OFICINA
$oficina_id = $_SESSION['id_oficina'];

// verifica se o form foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // verifica se o botão de excluir foi pressionado
    if (isset($_POST['excluir_perfil']) && $_POST['excluir_perfil'] === '1') {

        $sqlDelete = "DELETE FROM oficina WHERE id_oficina = ?";
        $stmtDelete = $conexao->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $oficina_id); // associa o id da oficina

        // executa a exclusão
        if ($stmtDelete->execute()) {
            session_destroy(); // encerra a sessão da oficina
            echo "<script>alert('Perfil excluído com sucesso.'); window.location.href='/fixTime/PROJETO/index.html';</script>";
            exit(); // interrompe o script
        } else {
            // caso de erro de exclusão
            echo "Erro ao excluir perfil: " . $conexao->error;
        }

        $stmtDelete->close();
    }

    // caso seja para editar
    // caso seja para editar
    else if (isset($_POST['salvar_perfil'])) {
        // recupera os dados do form
        $nome = trim($_POST['nome'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $cep = trim($_POST['cep'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $complemento = trim($_POST['complemento'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Validação básica
        $validCategorias = ['Borracharia', 'Auto Elétrica', 'Oficina Mecânica', 'Lava Car'];
        if (empty($nome) || empty($cnpj) || empty($email)) {
            echo "<script>alert('Nome, CNPJ e Email são campos obrigatórios.'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/perfil.php';</script>";
            exit();
        }
        if (!in_array($categoria, $validCategorias)) {
            echo "<script>alert('Categoria inválida.'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/perfil.php';</script>";
            exit();
        }

        // prepara a query de atualização
        $sqlUpdate = "UPDATE oficina SET nome_oficina = ?, categoria = ?, cep_oficina = ?, cnpj = ?, endereco_oficina = ?, numero_oficina = ?, complemento = ?, bairro_oficina = ?, cidade_oficina = ?, estado_oficina = ?, telefone_oficina = ?, email_oficina = ? WHERE id_oficina = ?";
        $stmtUpdate = $conexao->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssssssssssi", $nome, $categoria, $cep, $cnpj, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $telefone, $email, $oficina_id);

        // executa a atualização
        if ($stmtUpdate->execute()) {
            echo "<script>alert('Suas alterações foram salvas com sucesso!'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/perfil.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao atualizar perfil: " . addslashes($conexao->error) . "'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/perfil.php';</script>";
            exit();
        }

        $stmtUpdate->close();
    }
}

// busca os dados atuais da oficina
$sql = "SELECT nome_oficina, categoria, cep_oficina, cnpj, endereco_oficina, numero_oficina, complemento, bairro_oficina, cidade_oficina, estado_oficina, telefone_oficina, email_oficina FROM oficina WHERE id_oficina = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $oficina_id); // associa o id da oficina
$stmt->execute(); // executa a query
$result = $stmt->get_result();

// verifica se encontrou a oficina
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc(); // salva os dados em um array associativo
} else {
    die("Oficina não encontrada."); // interrompe se a oficina não existir
}

// Fecha o statement e a conexão
$stmt->close();
$conexao->close();
?>


<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
    <title>Fix Time</title>
</head>


<body class="">

    <button id="hamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
        </svg>
    </button>

    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0">
        <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50">
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
                    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/agendamentos.html" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path>
                        </svg>
                        <span class="ms-3">Meus agendamentos</span>
                    </a>
                </li>


                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/historico-servicos.html" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Histórico de serviços</span>
                    </a>
                </li>


                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.html" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Prestadores de serviços</span>
                    </a>
                </li>

                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Meus veículos</span>
                    </a>
                </li>

                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/perfil.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Perfil</span>
                    </a>
                </li>

            </ul>
        </div>
    </aside>


    <div class=" lg:ml-64 lg:py-10 py-4 lg:px-32 px-8 ">

        <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm">
            <form id="formPerfil" method="POST" action="perfil.php">

                <div class="space-y-7">
                    <div class="">
                        <label for="nome-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">Oficina</label>
                        <input type="text" id="nome-perfil" name="nome" value="<?php echo htmlspecialchars($user_data['nome_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="">
                        <label for="cnpj-perfil" class="block mb-1 text-sm font-medium text-gray-900">CNPJ</label>
                        <input type="text" id="cnpj-perfil" name="cnpj" value="<?php echo htmlspecialchars($user_data['cnpj']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class=""></div>
                    <label for="categoria-perfil" class="block mb-1 text-sm font-medium text-gray-900">Categoria</label>
                    <select id="categoria-perfil" name="categoria" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none">
                        <option value="Borracharia" <?php echo $user_data['categoria'] === 'Borracharia' ? 'selected' : ''; ?>>Borracharia</option>
                        <option value="Auto Elétrica" <?php echo $user_data['categoria'] === 'Auto Elétrica' ? 'selected' : ''; ?>>Auto Elétrica</option>
                        <option value="Oficina Mecânica" <?php echo $user_data['categoria'] === 'Oficina Mecânica' ? 'selected' : ''; ?>>Oficina Mecânica</option>
                        <option value="Lava Car" <?php echo $user_data['categoria'] === 'Lava Car' ? 'selected' : ''; ?>>Lava Car</option>
                    </select>
                </div>

                <div class="">
                    <label for="telefone-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">Número de telefone</label>
                    <input type="text" id="telefone-perfil" name="telefone" value="<?php echo htmlspecialchars($user_data['telefone_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="email-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">Email</label>
                    <input type="email" id="email-perfil" name="email" value="<?php echo htmlspecialchars($user_data['email_oficina']); ?>" class=" cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="cep-perfil" class="block mb-1 text-sm font-medium text-gray-900">CEP</label>
                    <input type="text" id="cep-perfil" name="cep" value="<?php echo htmlspecialchars($user_data['cep_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="endereco-perfil" class="block mb-1 text-sm font-medium text-gray-900">Endereço</label>
                    <input type="text" id="endereco-perfil" name="endereco" value="<?php echo htmlspecialchars($user_data['endereco_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="bairro-perfil" class="block mb-1 text-sm font-medium text-gray-900">Bairro</label>
                    <input type="text" id="bairro-perfil" name="bairro" value="<?php echo htmlspecialchars($user_data['bairro_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="numero-perfil" class="block mb-1 text-sm font-medium text-gray-900">Número</label>
                    <input type="text" id="numero-perfil" name="numero" value="<?php echo htmlspecialchars($user_data['numero_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="cidade-perfil" class="block mb-1 text-sm font-medium text-gray-900">Cidade</label>
                    <input type="text" id="cidade-perfil" name="cidade" value="<?php echo htmlspecialchars($user_data['cidade_oficina']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="">
                    <label for="complemento-perfil" class="block mb-1 text-sm font-medium text-gray-900">Complemento</label>
                    <input type="text" id="complemento-perfil" name="complemento" value="<?php echo htmlspecialchars($user_data['complemento']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

        </div>
        <input type="hidden" name="salvar_perfil" value="1">


        <div class="lg:gap-6 gap-4 items-center grid grid-cols-6 mt-6">
            <button id="editarPerfilBtn" type="button" name="salvar_perfil" value="1" class="text-white inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                </svg>
                Editar
            </button>

            <button id="excluirPerfilBtn" type="button" name="excluir_perfil" class="inline-flex items-center justify-center gap-2 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Excluir
            </button>
        </div>
        <input type="hidden" name="excluir_perfil" id="inputExcluirPerfil" value="">

        </form>

    </div>
    </div>

    <script>
        // Menu Hamburguer (Abre/Fecha)
        const hamburgerButton = document.getElementById('hamburgerButton');
        const closeHamburgerButton = document.getElementById('closeHamburgerButton');
        const sidebar = document.getElementById('sidebar');

        // Abre o menu
        hamburgerButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Fecha o menu
        closeHamburgerButton.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editarBtn = document.getElementById('editarPerfilBtn');
            const excluirBtn = document.getElementById('excluirPerfilBtn');
            const form = document.getElementById('formPerfil');
            let modoEdicao = false;

            editarBtn.addEventListener('click', function() {
                if (!modoEdicao) {
                    // Modo edição - habilita todos os campos
                    document.querySelectorAll('input, select').forEach(element => {
                        element.disabled = false;
                        element.classList.remove('cursor-not-allowed', 'bg-gray-50');
                        element.classList.add('bg-white');
                    });

                    editarBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Salvar';
                    modoEdicao = true;

                    // Aplica máscaras
                    $('#telefone-perfil').mask('(00) 00000-0000');
                    $('#cnpj-perfil').mask('00.000.000/0000-00', {
                        reverse: true
                    });
                    $('#cep-perfil').mask('00000-000');
                } else {
                    // Validação básica antes de enviar
                    if ($('#nome-perfil').val().trim() === '' ||
                        $('#cnpj-perfil').val().trim() === '' ||
                        $('#email-perfil').val().trim() === '') {
                        alert('Nome, CNPJ e Email são campos obrigatórios.');
                        return;
                    }

                    // Submeter formulário para salvar
                    form.submit();
                }
            });

            excluirBtn.addEventListener('click', function() {
                const confirmar = confirm('Tem certeza que deseja excluir seu perfil? Essa ação não pode ser desfeita.');
                if (confirmar) {
                    document.getElementById('inputExcluirPerfil').value = '1';
                    form.submit();
                }
            });
        });
    </script>




    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="/fixTime/PROJETO/src/public/assets/js/script.js"></script>
</body>

</html>