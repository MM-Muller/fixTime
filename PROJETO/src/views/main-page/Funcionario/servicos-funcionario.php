<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($_SESSION['id_funcionario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-funcionario.php';</script>";
    exit;
}

$id_funcionario = $_SESSION['id_funcionario'];
$primeiroNome = '';

$stmt = $conexao->prepare("SELECT nome_funcionario FROM funcionarios WHERE id_funcionario = ?");
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $nomeCompleto = htmlspecialchars($row['nome_funcionario']);
    $primeiroNome = explode(' ', $nomeCompleto)[0];
    $primeiroNome = strlen($primeiroNome) > 16 ? substr($primeiroNome, 0, 16) . "..." : $primeiroNome;
}



$sql = "SELECT 
          f.nome_funcionario, 
          o.nome_oficina 
        FROM funcionarios f
        JOIN oficina o ON f.id_oficina = o.id_oficina
        WHERE f.id_funcionario = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // nome do funcionário
    $nomeCompleto = htmlspecialchars($row['nome_funcionario']);
    $primeiroNome = explode(' ', $nomeCompleto)[0];
    $primeiroNome = strlen($primeiroNome) > 16 ? substr($primeiroNome, 0, 16) . "..." : $primeiroNome;

    // nome da oficina
    $nomeOficina = htmlspecialchars($row['nome_oficina']);
}





$stmt->close();
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
                        <a class="flex items-center p-2 text-gray-900 rounded-lg  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 " data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                            </svg>
                            <span class="flex-1 ms-3 break-words whitespace-normal"> <?php echo $nomeOficina; ?></span>

                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Funcionario/servicos-funcionario.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                              <path stroke="currentColor" stroke-linejoin="round" stroke-width="1.5" d="m20.9532 11.7634-2.0523-2.05225-2.0523 2.05225 2.0523 2.0523 2.0523-2.0523Zm-1.3681-2.73651-4.1046-4.10457L12.06 8.3428l4.1046 4.1046 3.4205-3.42051Zm-4.1047 2.73651-2.7363-2.73638-8.20919 8.20918 2.73639 2.7364 8.2091-8.2092Z"/>
                              <path stroke="currentColor" stroke-linejoin="round" stroke-width="1.5" d="m12.9306 3.74083 1.8658 1.86571-2.0523 2.05229-1.5548-1.55476c-.995-.99505-3.23389-.49753-3.91799.18657l2.73639-2.73639c.6841-.68409 1.9901-.74628 2.9229.18658Z"/>
                            </svg>


                            <span class="flex-1 ms-3 whitespace-nowrap">Serviços</span>
                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Funcionario/perfil-funcionario.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">
                                <?php echo $primeiroNome; ?>
                            </span>
                        </a>
                    </li>

                </ul>
            </div>

            <div>
                <a href="/fixTime/PROJETO/src/views/Login/logout.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                    <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap font-medium">Logout</span>
                </a>
            </div>

    </aside>


    <div class="lg:ml-64 lg:py-9 py-4 lg:px-20 px-8 ">

        <div class="flex flex-col items-center justify-center text-center">
            <p class="text-gray-400 text-2xl font-medium">
                Serviços <?php echo $nomeOficina; ?>
            </p>
            <div class="w-48 h-px bg-gray-300 mt-6 rounded-sm mb-8"></div>
        </div>

        <div >
            <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="grid grid-cols-6 gap-6 ">
                    <div class="col-span-2">
                        <label for="nome-funcionario" class="block mb-1 text-sm font-medium text-gray-900 ">ID do Serviço</label>
                        <input type="text" id="nome-funcionario" name="nome-funcionario" value="" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="col-span-2">
                        <label for="cpf-funcionario" class="block mb-1 text-sm font-medium text-gray-900">Data de recebimento</label>
                        <input type="text" id="cpf-funcionario" name="cpf-funcionario" value="" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="col-span-2">
                        <label for="telefone-funcionario" class="block mb-1 text-sm font-medium text-gray-900 ">Horário de recebimento</label>
                        <input type="text" id="telefone-funcionario" name="telefone-funcionario" value="" class="cursor-not-allowed bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="w-48 h-px bg-gray-300 mt-10 rounded-sm mb-8"></div>
                </div>

                <div class="">
                    <form id="formPerfil" method="POST" action="" >
                        <div class="grid grid-cols-6 gap-4">

                            <div class="col-span-2 space-y-4">
                                <div class="">
                                    <label for="data-entrega" class="block mb-1 text-sm font-medium text-gray-900">Data de entrega do veículo</label>
                                    <input type="date" id="data-entrega" name="data-entrega"  min="<?php echo date('Y-m-d'); ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" />
                                </div>

                                <div class="">
                                    <label for="status" class="block mb-1 text-sm font-medium text-gray-900">Status</label>
                                    <select id="status" name="status" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none">
                                        <option value="pendente">Pendente</option>
                                        <option value="em_andamento">Em andamento</option>
                                        <option value="finalizado">Finalizado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                           

                            <div class="col-span-4">
                                <label for="servicos-feitos" class="block mb-1 text-sm font-medium text-gray-900">Serviços feitos</label>
                                <textarea id="servicos-feitos" name="servicos-feitos" rows="5" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none resize-none" placeholder="Descreva os serviços realizados..."></textarea>
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Menu Hamburguer abre e fecha
        const hamburgerButton = document.getElementById('hamburgerButton');
        const closeHamburgerButton = document.getElementById('closeHamburgerButton');
        const sidebar = document.getElementById('sidebar');

        // abre o menu
        hamburgerButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // fecha o menu
        closeHamburgerButton.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="/fixTime/PROJETO/src/public/assets/js/script.js"></script>
</body>

</html>