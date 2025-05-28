<?php
// Ativa a exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';

// Verifica se o arquivo de conexão existe
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php')) {
    die("Erro: Arquivo de conexão não encontrado.");
}

// Tenta conectar ao banco de dados
try {
    $conexao = connect_db();
    if (!$conexao) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Inicia a sessão para gerenciar dados do usuário
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit;
}

$user_id = $_SESSION['id_usuario'];

// Verifica se o id da oficina foi passado na URL
if (!isset($_GET['id_oficina'])) {
    echo "<script>alert('Oficina não especificada.'); window.location.href='prestadores-servico.php';</script>";
    exit;
}

$id_oficina = (int) $_GET['id_oficina'];

// Busca os dados da oficina
try {
    $sql_oficina = "SELECT id_oficina, nome_oficina, categoria FROM oficina WHERE id_oficina = ?";
    $stmt_oficina = $conexao->prepare($sql_oficina);
    if (!$stmt_oficina) {
        throw new Exception("Erro ao preparar consulta da oficina: " . $conexao->error);
    }
    
    $stmt_oficina->bind_param("i", $id_oficina);
    $stmt_oficina->execute();
    $result_oficina = $stmt_oficina->get_result();

    if ($result_oficina->num_rows === 0) {
        echo "<script>alert('Oficina não encontrada.'); window.location.href='prestadores-servico.php';</script>";
        exit;
    }

    $oficina = $result_oficina->fetch_assoc();
    $stmt_oficina->close();
} catch (Exception $e) {
    die("Erro ao buscar dados da oficina: " . $e->getMessage());
}

// Processa o formulário quando enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar'])) {
    try {
        // Recupera e sanitiza os dados do formulário
        $id_veiculo = filter_input(INPUT_POST, 'id_veiculo', FILTER_SANITIZE_NUMBER_INT);
        $data_hora_servico = htmlspecialchars($_POST['data_servico'], ENT_QUOTES, 'UTF-8');
        $data_entrega = htmlspecialchars($_POST['data_entrega'], ENT_QUOTES, 'UTF-8');
        $descricao = htmlspecialchars($_POST['descricao_servico'], ENT_QUOTES, 'UTF-8');

        // Valida os dados
        if (!$id_veiculo || !$data_hora_servico || !$data_entrega || !$descricao) {
            throw new Exception("Por favor, preencha todos os campos.");
        }

        // Separa a data e hora do campo datetime-local
        $data_hora = new DateTime($data_hora_servico);
        $data_agendada = $data_hora->format('Y-m-d');
        $horario = $data_hora->format('H:i:s');

        // Verifica se já existe agendamento para esta oficina no mesmo horário
        $sql_check = "SELECT id_servico FROM servico WHERE id_oficina = ? AND data_agendada = ? AND horario = ?";
        $stmt_check = $conexao->prepare($sql_check);
        if (!$stmt_check) {
            throw new Exception("Erro ao preparar verificação de horário: " . $conexao->error);
        }

        $stmt_check->bind_param("iss", $id_oficina, $data_agendada, $horario);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            throw new Exception("Já existe um agendamento para este horário. Por favor, escolha outro horário.");
        }
        $stmt_check->close();

        // Prepara a query de inserção
        $sql_insert = "INSERT INTO servico (data_agendada, horario, data_entrega, status, descricao_servico, id_veiculo, id_oficina) 
                      VALUES (?, ?, ?, 'Pendente', ?, ?, ?)";
        $stmt_insert = $conexao->prepare($sql_insert);
        if (!$stmt_insert) {
            throw new Exception("Erro ao preparar inserção: " . $conexao->error);
        }

        $stmt_insert->bind_param("ssssii", $data_agendada, $horario, $data_entrega, $descricao, $id_veiculo, $id_oficina);

        // Executa a inserção
        if (!$stmt_insert->execute()) {
            throw new Exception("Erro ao realizar agendamento: " . $stmt_insert->error);
        }

        echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='meus-agendamentos.php';</script>";
        exit();

    } catch (Exception $e) {
        echo "<script>alert('" . addslashes($e->getMessage()) . "');</script>";
    } finally {
        if (isset($stmt_insert)) {
            $stmt_insert->close();
        }
    }
}

// Busca os dados do usuário
try {
    $sql = "SELECT nome_usuario FROM cliente WHERE id_usuario = ?";
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta do usuário: " . $conexao->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();
} catch (Exception $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - FixTime</title>
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
            <h2 class="text-2xl font-bold mb-6">Agendar Serviço</h2>
            <p class="text-gray-600 mb-6">Agendando serviço para: <span class="font-semibold"><?php echo htmlspecialchars($oficina['nome_oficina'] . ' - ' . $oficina['categoria']); ?></span></p>

            <!-- Formulário de Agendamento -->
            <form method="POST" action="" class="space-y-6">
                <!-- Seleção de Veículo -->
                <div>
                    <label for="veiculo" class="block mb-2 text-sm font-medium text-gray-900">Veículo</label>
                    <select id="veiculo" name="id_veiculo" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Selecione um veículo</option>
                        <?php
                        // Busca os veículos do usuário
                        $sql_veiculos = "SELECT id, marca, modelo, placa FROM veiculos WHERE id_usuario = ?";
                        $stmt_veiculos = $conexao->prepare($sql_veiculos);
                        $stmt_veiculos->bind_param("i", $user_id);
                        $stmt_veiculos->execute();
                        $result_veiculos = $stmt_veiculos->get_result();
                        
                        while ($veiculo = $result_veiculos->fetch_assoc()) {
                            echo "<option value='" . $veiculo['id'] . "'>" . 
                                 htmlspecialchars($veiculo['marca'] . " " . $veiculo['modelo'] . " - " . $veiculo['placa']) . 
                                 "</option>";
                        }
                        $stmt_veiculos->close();
                        ?>
                    </select>
                </div>

                <!-- Data e Hora do Serviço -->
                <div>
                    <label for="data_servico" class="block mb-2 text-sm font-medium text-gray-900">Data e Hora do Serviço</label>
                    <input type="datetime-local" id="data_servico" name="data_servico" required 
                           min="<?php echo date('Y-m-d\TH:i'); ?>" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Data de Entrega -->
                <div>
                    <label for="data_entrega" class="block mb-2 text-sm font-medium text-gray-900">Data de Entrega</label>
                    <input type="date" id="data_entrega" name="data_entrega" required 
                           min="<?php echo date('Y-m-d'); ?>" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Descrição do Serviço -->
                <div>
                    <label for="descricao" class="block mb-2 text-sm font-medium text-gray-900">Descrição do Serviço</label>
                    <textarea id="descricao" name="descricao_servico" rows="4" required
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                              placeholder="Descreva o serviço que você precisa..."></textarea>
                </div>

                <!-- Botão de Agendamento -->
                <div class="mt-6">
                    <button type="submit" name="agendar" 
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Agendar Serviço
                    </button>
                </div>
            </form>
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
