<?php
// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db(); // Estabelece conexão com o banco de dados

// Verifica se a conexão foi estabelecida com sucesso
if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

// Inicia a sessão PHP para manter o estado do usuário
session_start();

// Obtém o ID da oficina da sessão
$oficina_id = $_SESSION['id_oficina'] ?? null;

// Verifica se o usuário está autenticado
if (!$oficina_id) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-company.php';</script>";
    exit();
}

// Busca os dados da oficina
$sql = "SELECT nome_oficina FROM oficina WHERE id_oficina = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $oficina_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se encontrou a oficina e armazena os dados
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    die("Oficina não encontrada.");
}

// Busca os agendamentos da oficina
$sql_agendamentos = "SELECT s.*, v.marca, v.modelo, v.placa, c.nome_usuario 
                    FROM servico s 
                    JOIN veiculos v ON s.id_veiculo = v.id 
                    JOIN cliente c ON v.id_usuario = c.id_usuario 
                    WHERE s.id_oficina = ? 
                    ORDER BY s.data_agendada DESC, s.horario DESC";
$stmt_agendamentos = $conexao->prepare($sql_agendamentos);
$stmt_agendamentos->bind_param("i", $oficina_id);
$stmt_agendamentos->execute();
$result_agendamentos = $stmt_agendamentos->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - FixTime</title>
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
</head>
<body class="bg-gray-50">
    <!-- Botão do menu hamburguer para dispositivos móveis -->
    <button id="hamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
        </svg>
    </button>

    <!-- Sidebar de navegação -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0">
        <div class="h-full px-3 py-4 bg-gray-50 flex flex-col justify-between">
            <div>
                <!-- Logo e botão de fechar menu -->
                <a class="flex items-center lg:justify-center justify-between ps-3 mx-auto mb-2">
                    <button id="closeHamburgerButton" type="button" class="cursor-pointer inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <svg class="w-6 h-6 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">                    
                          <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <img src="/fixTime/PROJETO/src/public/assets/images/fixtime-truck.png" class="lg:h-14 h-12 me-3 "/>
                </a>

                <!-- Menu de navegação -->
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Meus Funcionarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/perfil-oficina.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">
                                <?php
                                    // Exibe apenas as duas primeiras palavras do nome da oficina
                                    $nomeCompleto = htmlspecialchars($user_data['nome_oficina']);
                                    $partes = explode(' ', $nomeCompleto);
                                    $duasPalavras = implode(' ', array_slice($partes, 0, 2));
                                    echo $duasPalavras;
                                ?>
                            </span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/registrar-servicos.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.583 8.445h.01M10.86 19.71l-6.573-6.63a.993.993 0 0 1 0-1.4l7.329-7.394A.98.98 0 0 1 12.31 4l5.734.007A1.968 1.968 0 0 1 20 5.983v5.5a.992.992 0 0 1-.316.727l-7.44 7.5a.974.974 0 0 1-1.384.001Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Registrar serviços</span>
                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Oficina/agendamentos-oficina.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Agendamentos</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Link para Logout -->
            <a href="/fixTime/PROJETO/src/views/Login/logout.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                </svg>
                <span class="flex-1 ms-3 whitespace-nowrap font-medium">Logout</span>
            </a>
        </div>
    </aside>

    <div class="lg:ml-64 p-4 lg:p-14">
        <div class="text-center">
            <p class="text-2xl">Serviços</p>
            <hr class="mt-6 mb-6">
        </div>
        <?php if ($result_agendamentos->num_rows === 0): ?>
            <div class="text-center py-8">
                <p class="text-gray-500">Nenhum agendamento encontrado.</p>
            </div>
        <?php else: ?>
            <?php while ($agendamento = $result_agendamentos->fetch_assoc()): 
                // Formata a data e hora para exibição
                $data_hora = $agendamento['data_agendada'] . ' ' . $agendamento['horario'];
                $data_hora_formatada = date('d/m/Y H:i', strtotime($data_hora));
                
                // Formata a data de entrega
                $data_entrega_formatada = $agendamento['data_entrega'] ? date('d/m/Y', strtotime($agendamento['data_entrega'])) : 'Não definida';
            ?>
            <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-2">
                        <label for="nome-funcionario" class="block mb-1 text-sm font-medium text-gray-900">ID do Serviço</label>
                        <input type="text" id="nome-funcionario" name="nome-funcionario" value="<?php echo $agendamento['id_servico']; ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="col-span-2">
                        <label for="cpf-funcionario" class="block mb-1 text-sm font-medium text-gray-900">Data de recebimento</label>
                        <input type="text" id="cpf-funcionario" name="cpf-funcionario" value="<?php echo date('d/m/Y', strtotime($agendamento['data_agendada'])); ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>

                    <div class="col-span-2">
                        <label for="telefone-funcionario" class="block mb-1 text-sm font-medium text-gray-900">Horário de recebimento</label>
                        <input type="text" id="telefone-funcionario" name="telefone-funcionario" value="<?php echo date('H:i', strtotime($agendamento['horario'])); ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="w-48 h-px bg-gray-300 mt-10 rounded-sm mb-8"></div>
                </div>

                <div class="">
                    <form id="formPerfil" method="POST" action="">
                        <div class="grid grid-cols-6 gap-4">
                            <div class="col-span-2 space-y-4">
                                <div class="">
                                    <label for="data-entrega" class="block mb-1 text-sm font-medium text-gray-900">Data de entrega do veículo</label>
                                    <input type="date" id="data-entrega" name="data-entrega" value="<?php echo $agendamento['data_entrega']; ?>" min="<?php echo date('Y-m-d'); ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                                </div>

                                <div class="">
                                    <label for="status" class="block mb-1 text-sm font-medium text-gray-900">Status</label>
                                    <select id="status" name="status" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled>
                                        <option value="Pendente" <?php echo $agendamento['status'] == 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="Em Andamento" <?php echo $agendamento['status'] == 'Em Andamento' ? 'selected' : ''; ?>>Em andamento</option>
                                        <option value="Concluído" <?php echo $agendamento['status'] == 'Concluído' ? 'selected' : ''; ?>>Finalizado</option>
                                        <option value="Cancelado" <?php echo $agendamento['status'] == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-span-4">
                                <label for="servicos-feitos" class="block mb-1 text-sm font-medium text-gray-900">Serviços feitos</label>
                                <textarea id="servicos-feitos" name="servicos-feitos" rows="5" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none resize-none" placeholder="Descreva os serviços realizados..." disabled><?php echo htmlspecialchars($agendamento['descricao_servico']); ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="mt-4">
                    <label for="cliente" class="block mb-1 text-sm font-medium text-gray-900">Cliente</label>
                    <input type="text" id="cliente" name="cliente" value="<?php echo htmlspecialchars($agendamento['nome_usuario']); ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="mt-4">
                    <label for="veiculo" class="block mb-1 text-sm font-medium text-gray-900">Veículo</label>
                    <input type="text" id="veiculo" name="veiculo" value="<?php echo htmlspecialchars($agendamento['marca'] . ' ' . $agendamento['modelo'] . ' - ' . $agendamento['placa']); ?>" class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 outline-none" disabled />
                </div>

                <div class="lg:gap-6 gap-4 items-center mt-6">
                    <button type="button" onclick="editarAgendamento(<?php echo $agendamento['id_servico']; ?>)" class="text-white inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer w-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                        </svg>
                        Editar
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
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

        // Função para editar agendamento
        function editarAgendamento(id) {
            window.location.href = `editar-agendamento.php?id=${id}`;
        }
    </script>

    <!-- Scripts externos -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="/fixTime/PROJETO/src/public/assets/js/script.js"></script>
</body>
</html>