<?php
// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db(); // conecta com o bd

// Verifica se a conexão foi estabelecida corretamente
if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

// Inicia a sessão para gerenciar dados do usuário
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit;
}

$user_id = $_SESSION['id_usuario'];

// Busca os dados do usuário
$sql = "SELECT nome_usuario FROM cliente WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - FixTime</title>
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
</head>
<body class="bg-gray-50">
    <!-- Botão do menu hamburguer para dispositivos móveis -->
    <button id="hamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
        </svg>
    </button>

    <!-- Barra lateral (sidebar) -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0">
        <div class="h-full px-3 py-4 bg-gray-50 flex flex-col justify-between">
            <!-- Cabeçalho da sidebar -->
            <div>
                <a class="flex items-center lg:justify-center justify-between ps-3 mx-auto mb-2">
                    <!-- Botão para fechar o menu em dispositivos móveis -->
                    <button id="closeHamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <svg class="w-6 h-6 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <!-- Logo da aplicação -->
                    <img src="/fixTime/PROJETO/src/public/assets/images/fixtime-truck.png" class="lg:h-14 h-12 me-3 " />
                </a>

                <!-- Menu de navegação -->
                <ul class="space-y-2 font-medium">
                    <!-- Link para Prestadores de Serviço -->
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Prestadores de serviços</span>
                        </a>
                    </li>

                    <!-- Link para Meus Veículos -->
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Meus veículos</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z"/>
                        </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Meus agendamentos</span>
                        </a>
                    </li>
                    <!-- Link para Perfil do Usuário -->
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/perfil.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">
                                <?php
                                    // Processa o nome do usuário para exibição
                                    $nomeCompleto = htmlspecialchars($user_data['nome_usuario']);
                                    $primeiroNome = explode(' ', $nomeCompleto)[0];
                                    $nomeExibido = strlen($primeiroNome) > 16 ? substr($primeiroNome, 0, 16) . "..." : $primeiroNome;
                                    echo $nomeExibido;
                                ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Link para Logout -->
            <a href="/fixTime/PROJETO/src/views/Login/logout.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100 group">
                <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                </svg>
                <span class="flex-1 ms-3 whitespace-nowrap font-medium">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Conteúdo principal da página -->
    <div class="lg:ml-64 lg:py-10 py-4 lg:px-32 px-8">
        <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-6">Meus Agendamentos</h2>

            <!-- Lista de Agendamentos -->
            <div class="mt-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Data e Hora</th>
                                <th class="px-6 py-3">Data de Entrega</th>
                                <th class="px-6 py-3">Oficina</th>
                                <th class="px-6 py-3">Veículo</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Busca os agendamentos do usuário
                            $sql_agendamentos = "SELECT s.*, o.nome_oficina, o.categoria, v.marca, v.modelo, v.placa 
                                               FROM servico s 
                                               JOIN oficina o ON s.id_oficina = o.id_oficina 
                                               JOIN veiculos v ON s.id_veiculo = v.id 
                                               WHERE v.id_usuario = ? 
                                               ORDER BY s.data_agendada DESC, s.horario DESC";
                            $stmt_agendamentos = $conexao->prepare($sql_agendamentos);
                            $stmt_agendamentos->bind_param("i", $user_id);
                            $stmt_agendamentos->execute();
                            $result_agendamentos = $stmt_agendamentos->get_result();

                            if ($result_agendamentos->num_rows === 0) {
                                echo "<tr><td colspan='6' class='px-6 py-4 text-center'>Nenhum agendamento encontrado.</td></tr>";
                            } else {
                                while ($agendamento = $result_agendamentos->fetch_assoc()) {
                                    // Formata a data e hora para exibição
                                    $data_hora = $agendamento['data_agendada'] . ' ' . $agendamento['horario'];
                                    $data_hora_formatada = date('d/m/Y H:i', strtotime($data_hora));
                                    
                                    // Formata a data de entrega
                                    $data_entrega_formatada = $agendamento['data_entrega'] ? date('d/m/Y', strtotime($agendamento['data_entrega'])) : 'Não definida';

                                    // Define a cor do status
                                    $status_class = '';
                                    switch ($agendamento['status']) {
                                        case 'Pendente':
                                            $status_class = 'text-yellow-600 bg-yellow-100';
                                            break;
                                        case 'Em Andamento':
                                            $status_class = 'text-blue-600 bg-blue-100';
                                            break;
                                        case 'Concluído':
                                            $status_class = 'text-green-600 bg-green-100';
                                            break;
                                        case 'Cancelado':
                                            $status_class = 'text-red-600 bg-red-100';
                                            break;
                                    }

                                    echo "<tr class='bg-white border-b hover:bg-gray-50'>";
                                    echo "<td class='px-6 py-4'>" . $data_hora_formatada . "</td>";
                                    echo "<td class='px-6 py-4'>" . $data_entrega_formatada . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($agendamento['nome_oficina'] . " - " . $agendamento['categoria']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($agendamento['marca'] . " " . $agendamento['modelo'] . " - " . $agendamento['placa']) . "</td>";
                                    echo "<td class='px-6 py-4'><span class='px-2 py-1 rounded-full text-xs font-medium " . $status_class . "'>" . htmlspecialchars($agendamento['status']) . "</span></td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($agendamento['descricao_servico']) . "</td>";
                                    echo "</tr>";
                                }
                            }
                            $stmt_agendamentos->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        // Controle do menu hamburguer
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

    <!-- Scripts externos -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="/fixTime/PROJETO/src/public/assets/js/script.js"></script>
</body>
</html>