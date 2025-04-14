<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db(); // conecta com o bd

if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
} // verifica se a conexão deu tudo certo

//inicia sessão
session_start();

//OBTEM O ID DO USER 
$user_id = $_SESSION['id_usuario'];


//verifica se o form foi enviado via post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // verifica se o botão de excluir foi pressionado
    if (isset($_POST['excluir_perfil']) && $_POST['excluir_perfil'] === '1') {

        $sqlDelete = "DELETE FROM cliente WHERE id_usuario = ?";
        $stmtDelete = $conexao->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $user_id); // associa o id do user


        //executa a exclusão
        if ($stmtDelete->execute()) {
            session_destroy(); // encerra a sessão do user
            echo "<script>alert('Perfil excluído com sucesso.'); window.location.href='/fixTime/PROJETO/index.html';</script>";
            exit(); // interrompe o script
        } else {
            // caso de erro de exclusão
            echo "Erro ao excluir perfil: " . $conexao->error;
        }

        $stmtDelete->close();
    }

    // caso seja para editar
    else if (isset($_POST['salvar_perfil'])) {
        // recupera os dados do form
        // usa um (vazio) ' ' caso nao tenha sido enviado no POST 
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');


        // prepara a query de atualização dos dados do usuário

        // monta a query SQL, mas com valores ?, evita sql injection, valores são passados depois, separadamente
        $sqlUpdate = "UPDATE cliente SET nome_usuario = ?, cpf = ?, telefone_usuario = ?, email_usuario = ? WHERE id_usuario = ?";
        $stmtUpdate = $conexao->prepare($sqlUpdate); //prepara a query no banco
        $stmtUpdate->bind_param("ssssi", $nome, $cpf, $telefone, $email, $user_id); //"ssssi" representa cada tipo de dado que esta sendo passado

        // executa a atualização 
        if ($stmtUpdate->execute()) {
            echo "<script>alert('Suas alterações foram salvas com sucesso!'); window.location.href='perfil.php';</script>";
            exit(); // Interrompe o script
        } else {
            // em caso de erro
            echo "Erro ao atualizar perfil: " . $conexao->error;
        }

        $stmtUpdate->close();
    }
}

//busca os dados atuais do user 
$sql = "SELECT nome_usuario, cpf, telefone_usuario, email_usuario FROM cliente WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $user_id); // associa o id do user 
$stmt->execute(); // executa a query
$result = $stmt->get_result();

// verifica se encontrou o user 
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc(); // salva os dados em um array associativo
} else {
    die("Usuário não encontrado."); // interrompe se o usuário não existir
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
                    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
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
                        <label for="nome-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">Nome</label>
                        <input type="text" id="nome-perfil" name="nome" value="<?php echo htmlspecialchars($user_data['nome_usuario']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="">
                        <label for="cpf-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">CPF</label>
                        <input type="text" id="cpf-perfil" name="cpf" value="<?php echo htmlspecialchars($user_data['cpf']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="">
                        <label for="telefone-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">Número de telefone</label>
                        <input type="text" id="telefone-perfil" name="telefone" value="<?php echo htmlspecialchars($user_data['telefone_usuario']); ?>" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="">
                        <label for="email-perfil" class="block mb-1 text-sm font-medium text-gray-900 ">Email</label>
                        <input type="email" id="email-perfil" name="email" value="<?php echo htmlspecialchars($user_data['email_usuario']); ?>" class=" cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
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
            const form = document.getElementById('formPerfil'); // supondo que seu form tenha esse ID
            let modoEdicao = false;

            editarBtn.addEventListener('click', function() {
                if (!modoEdicao) {
                    // Modo EDIÇÃO
                    document.querySelectorAll('input').forEach(input => {
                        input.disabled = false;
                        input.classList.remove('cursor-not-allowed');
                    });

                    editarBtn.textContent = 'Salvar';
                    modoEdicao = true;

                    $('#telefone-perfil').mask('(00) 00000-0000');
                    $('#cpf-perfil').mask('000.000.000-00', {
                        reverse: true
                    });
                } else {
                    // Modo SALVAR — agora sim envia o formulário
                    form.submit();
                }
            });
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
                    // Modo edição
                    document.querySelectorAll('input').forEach(input => {
                        input.disabled = false;
                        input.classList.remove('cursor-not-allowed');
                    });

                    editarBtn.textContent = 'Salvar';
                    modoEdicao = true;

                    $('#telefone-perfil').mask('(00) 00000-0000');
                    $('#cpf-perfil').mask('000.000.000-00', {
                        reverse: true
                    });
                } else {
                    // Submeter formulário para salvar
                    form.submit();
                }
            });

            excluirBtn.addEventListener('click', function() {
                const confirmar = confirm('Tem certeza que deseja excluir seu perfil? Essa ação não pode ser desfeita.');
                if (confirmar) {
                    // Define o valor do input hidden e envia o form
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