<?php
// Inicia a sessão para gerenciar dados do usuário
session_start();

// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Verifica se a conexão foi estabelecida corretamente
if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit;
}

// Obtém o ID do usuário da sessão
$id_usuario = $_SESSION['id_usuario'];
$primeiroNome = '';

// Busca o nome do usuário no banco de dados
$stmt = $conexao->prepare("SELECT nome_usuario FROM cliente WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Processa o nome do usuário para exibição
if ($row = $result->fetch_assoc()) {
    $nomeCompleto = htmlspecialchars($row['nome_usuario']);
    $primeiroNome = explode(' ', $nomeCompleto)[0];
    $primeiroNome = strlen($primeiroNome) > 16 ? substr($primeiroNome, 0, 16) . "..." : $primeiroNome;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <!-- Meta tags para configuração do documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link para o arquivo CSS do Tailwind -->
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
    <title>Fix Time</title>
</head>

<body class="">
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

                    <!-- Link para Perfil do Usuário -->
                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/perfil.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
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
    <div class=" lg:ml-64 p-10 ">
        <!-- Título da página -->
        <div class="flex justify-center items-center">
            <h1 class="mb-3 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">Oficinas Parceiras</h1>
        </div>

        <hr class=" h-px my-8 bg-gray-200 border-0">

        <?php
        // Obtém o filtro de categoria da URL
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

        // Prepara a query base para buscar oficinas
        $query = "SELECT id_oficina, nome_oficina, email_oficina, telefone_oficina, bairro_oficina, endereco_oficina, categoria, numero_oficina, complemento, cidade_oficina FROM oficina";
        if (!empty($filter)) {
            $query .= " WHERE categoria = ?";
        }

        // Executa a query com o filtro de categoria
        $stmt = $conexao->prepare($query);
        if (!empty($filter)) {
            $stmt->bind_param("s", $filter);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <!-- Formulário de filtros -->
        <form method="GET" class="mb-4">
            <div class="flex flex-wrap gap-4">
                <!-- Filtro por categoria -->
                <div class="flex-1">
                    <label for="filter" class="block mb-2 text-sm font-medium text-gray-900 ">Filtrar por categoria:</label>
                    <select name="filter" id="filter" class="block w-full p-2.5 border-gray-300 rounded-lg outline-none focus:ring-blue-500 focus:border-blue-500 border-2 cursor-pointer">
                        <option value="">Todas</option>
                        <option value="Borracharia" <?php echo $filter === 'Borracharia' ? 'selected' : ''; ?>>Borracharia</option>
                        <option value="Auto Elétrica" <?php echo $filter === 'Auto Elétrica' ? 'selected' : ''; ?>>Auto Elétrica</option>
                        <option value="Oficina Mecânica" <?php echo $filter === 'Oficina Mecânica' ? 'selected' : ''; ?>>Oficina Mecânica</option>
                        <option value="Lava Car" <?php echo $filter === 'Lava Car' ? 'selected' : ''; ?>>Lava Car</option>
                    </select>
                </div>
                <!-- Filtro por bairro -->
                <div class="flex-1">
                    <label for="bairro" class="block mb-2 text-sm font-medium text-gray-900">Filtrar por bairro:</label>
                    <input type="text" name="bairro" id="bairro" value="<?php echo isset($_GET['bairro']) ? htmlspecialchars($_GET['bairro']) : ''; ?>" class="block w-full p-2.5  border-gray-300 rounded-lg outline-none focus:ring-blue-500 focus:border-blue-500 border-2 " placeholder="Digite o bairro">
                </div>
            </div>
            <!-- Botão de filtrar -->
            <div class=" flex justify-center mt-4">
                <button type="submit" class="cursor-pointer mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Filtrar</button>
            </div>
        </form>

        <?php
        // Obtém o filtro de bairro da URL
        $bairroFilter = isset($_GET['bairro']) ? trim($_GET['bairro']) : '';

        // Adiciona o filtro de bairro à query
        if (!empty($bairroFilter)) {
            $query .= empty($filter) ? " WHERE bairro_oficina LIKE ?" : " AND bairro_oficina LIKE ?";
        }

        // Executa a query com os filtros combinados
        $stmt = $conexao->prepare($query);
        if (!empty($filter) && !empty($bairroFilter)) {
            $bairroFilter = '%' . $bairroFilter . '%';
            $stmt->bind_param("ss", $filter, $bairroFilter);
        } elseif (!empty($filter)) {
            $stmt->bind_param("s", $filter);
        } elseif (!empty($bairroFilter)) {
            $bairroFilter = '%' . $bairroFilter . '%';
            $stmt->bind_param("s", $bairroFilter);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // Exibe as oficinas encontradas
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Busca os serviços oferecidos pela oficina
                $oficina_id = $row['id_oficina'];
                $sql_servicos = "SELECT sp.nome_servico 
                                FROM oficina_servicos os 
                                JOIN servicos_padrao sp ON os.id_servico_padrao = sp.id_servico_padrao 
                                WHERE os.id_oficina = ?";
                $stmt_servicos = $conexao->prepare($sql_servicos);
                $stmt_servicos->bind_param("i", $oficina_id);
                $stmt_servicos->execute();
                $result_servicos = $stmt_servicos->get_result();
                
                // Exibe o card da oficina
                echo '<div class="mb-6 p-4 border border-gray-200 rounded-lg shadow-sm bg-white">';
                echo '<div class="grid grid-cols-2 gap-4">';
                // Coluna da esquerda com informações da oficina
                echo '<div>';
                echo '<h1 class="mb-3 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">' . htmlspecialchars($row['nome_oficina']) . '</h1>';
                echo '<p class="mb-1 text-gray-500">Categoria: ' . htmlspecialchars($row['categoria']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Email: ' . htmlspecialchars($row['email_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Telefone: ' . htmlspecialchars($row['telefone_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Endereço: ' . htmlspecialchars($row['endereco_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Número: ' . htmlspecialchars($row['numero_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Complemento: ' . htmlspecialchars($row['complemento']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Cidade: ' . htmlspecialchars($row['cidade_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Bairro: ' . htmlspecialchars($row['bairro_oficina']) . '</p>';
                echo '</div>';
                
                // Coluna da direita com serviços oferecidos
                echo '<div>';
                echo '<h2 class="mb-3 text-lg font-semibold text-gray-900">Serviços Oferecidos:</h2>';
                echo '<div class="space-y-2">';
                if ($result_servicos->num_rows > 0) {
                    while ($servico = $result_servicos->fetch_assoc()) {
                        echo '<p class="text-gray-500"><svg class="w-4 h-4 inline-block mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' . htmlspecialchars($servico['nome_servico']) . '</p>';
                    }
                } else {
                    echo '<p class="text-gray-500">Nenhum serviço cadastrado</p>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // Botão de agendamento
                echo '<button onclick="document.getElementById(\'agendarModal\').classList.remove(\'hidden\')" type="button" class="mt-2 text-white inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                    </svg>
                    Agendar
                </button>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-gray-500">Nenhuma oficina encontrada para o filtro selecionado.</p>';
        }
        ?>

    </div>

    <!-- Modal de Agendamento -->
    <div id="agendarModal" class="hidden fixed top-0 left-0 right-0 z-50 w-full h-screen flex items-center justify-center bg-gray-900/50">
        <div class="relative w-full max-w-sm mx-auto p-4">
            <!-- Conteúdo do Modal -->
            <div class="relative bg-white rounded-lg shadow mx-32 border border-gray-200">
                <!-- Cabeçalho do Modal -->
                <div class="relative p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900 text-center w-full">
                        Agendar Serviço
                    </h3>
                </div>

                <!-- Corpo do Modal -->
                <div class="p-4 space-y-4">
                    <form id="agendamentoForm" class="space-y-4">
                        <!-- Campo de Data -->
                        <div>
                            <label for="data_agendamento" class="block mb-2 text-sm font-medium text-gray-900 text-center">Data do Agendamento</label>
                            <input type="date" id="data_agendamento" name="data_agendamento" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        </div>

                        <!-- Campo de Horário -->
                        <div>
                            <label for="horario" class="block mb-2 text-sm font-medium text-gray-900 text-center">Horário</label>
                            <select id="horario" name="horario" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <option value="">Selecione um horário</option>
                                <option value="08:00">08:00</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="13:00">13:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                                <option value="17:00">17:00</option>
                            </select>
                        </div>

                        <!-- Campo de Seleção de Veículo -->
                        <div>
                            <label for="veiculo" class="block mb-2 text-sm font-medium text-gray-900 text-center">Selecione o Veículo</label>
                            <select id="veiculo" name="veiculo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <option value="">Selecione um veículo</option>
                                <?php
                                // Busca os veículos do cliente
                                $sql_veiculos = "SELECT id, tipo_veiculo, marca, modelo, ano, cor, placa, quilometragem 
                                               FROM veiculos 
                                               WHERE id_usuario = ?";
                                $stmt_veiculos = $conexao->prepare($sql_veiculos);
                                $stmt_veiculos->bind_param("i", $id_usuario);
                                $stmt_veiculos->execute();
                                $result_veiculos = $stmt_veiculos->get_result();

                                // Exibe os veículos no select
                                while ($veiculo = $result_veiculos->fetch_assoc()) {
                                    $tipo = ucfirst($veiculo['tipo_veiculo']);
                                    echo '<option value="' . $veiculo['id'] . '">' . 
                                         htmlspecialchars("$tipo - {$veiculo['marca']} {$veiculo['modelo']} ({$veiculo['ano']}) - {$veiculo['cor']} - Placa: {$veiculo['placa']}") . 
                                         '</option>';
                                }
                                $stmt_veiculos->close();
                                ?>
                            </select>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex items-center justify-center gap-2 mt-4">
                            <button type="button" onclick="document.getElementById('agendarModal').classList.add('hidden'); document.getElementById('agendamentoForm').reset();" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg text-sm px-5 py-2.5">Cancelar</button>
                            <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-sm px-5 py-2.5">Confirmar</button>
                        </div>
                    </form>
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

        // Fecha o modal ao clicar fora dele
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('agendarModal');
            if (event.target === modal) {
                modal.classList.add('hidden');
                document.getElementById('agendamentoForm').reset();
            }
        });
    </script>

</body>

</html>