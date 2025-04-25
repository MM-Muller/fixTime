<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$primeiroNome = '';

$stmt = $conexao->prepare("SELECT nome_usuario FROM cliente WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

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
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/agendamentos.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path>
                            </svg>
                            <span class="ms-3">Meus agendamentos</span>
                        </a>
                    </li>


                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/historico-servicos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Histórico de serviços</span>
                        </a>
                    </li>


                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Prestadores de serviços</span>
                        </a>
                    </li>

                    <li>
                        <a href="/fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                            <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">Meus veículos</span>
                        </a>
                    </li>

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

            <a href="/fixTime/PROJETO/src/views/Login/logout.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100 group">
                <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                </svg>

                <span class="flex-1 ms-3 whitespace-nowrap font-medium">Logout</span>
            </a>
        </div>

    </aside>

    <div class=" lg:ml-64 p-10 ">
        <div class="flex justify-center items-center">
            <h1 class="mb-3 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">Oficinas Parceiras</h1>
        </div>

        <hr class=" h-px my-8 bg-gray-200 border-0">

        <?php
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

        $query = "SELECT nome_oficina, email_oficina, telefone_oficina, bairro_oficina, endereco_oficina, categoria FROM oficina";
        if (!empty($filter)) {
            $query .= " WHERE categoria = ?";
        }

        $stmt = $conexao->prepare($query);
        if (!empty($filter)) {
            $stmt->bind_param("s", $filter);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        <form method="GET" class="mb-4">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1">
                    <label for="filter" class="block mb-2 text-sm font-medium text-gray-900">Filtrar por categoria:</label>
                    <select name="filter" id="filter" class="block w-full p-2.5 border border-gray-300 rounded-lg">
                        <option value="">Todas</option>
                        <option value="Borracharia" <?php echo $filter === 'Borracharia' ? 'selected' : ''; ?>>Borracharia</option>
                        <option value="Auto Elétrica" <?php echo $filter === 'Auto Elétrica' ? 'selected' : ''; ?>>Auto Elétrica</option>
                        <option value="Oficina Mecânica" <?php echo $filter === 'Oficina Mecânica' ? 'selected' : ''; ?>>Oficina Mecânica</option>
                        <option value="Lava Car" <?php echo $filter === 'Lava Car' ? 'selected' : ''; ?>>Lava Car</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="bairro" class="block mb-2 text-sm font-medium text-gray-900">Filtrar por bairro:</label>
                    <input type="text" name="bairro" id="bairro" value="<?php echo isset($_GET['bairro']) ? htmlspecialchars($_GET['bairro']) : ''; ?>" class="block w-full p-2.5 border border-gray-300 rounded-lg" placeholder="Digite o bairro">
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Filtrar</button>
            </div>
        </form>

        <?php
        $bairroFilter = isset($_GET['bairro']) ? trim($_GET['bairro']) : '';

        if (!empty($bairroFilter)) {
            $query .= empty($filter) ? " WHERE bairro_oficina LIKE ?" : " AND bairro_oficina LIKE ?";
        }

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
        ?>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="mb-6 p-4 border border-gray-200 rounded-lg shadow-sm bg-white">';
                echo '<h1 class="mb-3 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">' . htmlspecialchars($row['nome_oficina']) . '</h1>';
                echo '<p class="mb-1 text-gray-500">Categoria: ' . htmlspecialchars($row['categoria']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Email: ' . htmlspecialchars($row['email_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Telefone: ' . htmlspecialchars($row['telefone_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Endereço: ' . htmlspecialchars($row['endereco_oficina']) . '</p>';
                echo '<p class="mb-1 text-gray-500">Bairro: ' . htmlspecialchars($row['bairro_oficina']) . '</p>';
                echo '<button id="agendar" type="button" name="agendar" value="1" class="text-white inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3">
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
</body>

</html>