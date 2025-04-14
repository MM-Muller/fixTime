<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados. Verifique o arquivo connect_bd.php.");
}

session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/login-user.php';</script>";
    exit;
}

$mensagem = '';
$veiculos = [];
$id_usuario = $_SESSION['id_usuario'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização segura dos dados de entrada
    $tipo = isset($_POST['tipo_veiculos']) ? htmlspecialchars($_POST['tipo_veiculos'], ENT_QUOTES, 'UTF-8') : '';
    $marca = isset($_POST['marca_veiculo']) ? htmlspecialchars($_POST['marca_veiculo'], ENT_QUOTES, 'UTF-8') : '';
    $modelo = isset($_POST['modelo_veiculo']) ? htmlspecialchars($_POST['modelo_veiculo'], ENT_QUOTES, 'UTF-8') : '';
    $ano = isset($_POST['ano_veiculo']) ? (int)$_POST['ano_veiculo'] : 0;
    $cor = isset($_POST['cor_veiculo']) ? htmlspecialchars($_POST['cor_veiculo'], ENT_QUOTES, 'UTF-8') : '';
    $placa = isset($_POST['placa_veiculo']) ? htmlspecialchars($_POST['placa_veiculo'], ENT_QUOTES, 'UTF-8') : '';
    $quilometragem = isset($_POST['quilometragem_veiculo']) 
    ? (int) str_replace('.', '', $_POST['quilometragem_veiculo']) 
    : 0;


    // Validação dos campos
    if (
        empty($tipo) || empty($marca) || empty($modelo) || $ano < 1900 ||
        empty($cor) || empty($placa) || $quilometragem < 0
    ) {
        $mensagem = "<script>alert('Preencha todos os campos corretamente.');</script>";
    } else {
        try {
            $stmt = $conexao->prepare("INSERT INTO veiculos (tipo_veiculo, marca, modelo, ano, cor, placa, quilometragem, id_usuario) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $tipo, $marca, $modelo, $ano, $cor, $placa, $quilometragem, $id_usuario);

            if ($stmt->execute()) {
                $mensagem = "<script>alert('Veículo cadastrado com sucesso!');</script>";
                // Recarrega a página para mostrar o novo veículo
                echo "<script>window.location.href = window.location.href;</script>";
                exit;
            } else {
                $mensagem = "<script>alert('Erro ao cadastrar veículo: " . addslashes($stmt->error) . "');</script>";
            }

            $stmt->close();
        } catch (Exception $e) {
            $mensagem = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}

// Buscar veículos do usuário
if ($id_usuario) {
    try {
        $stmt = $conexao->prepare("SELECT id, tipo_veiculo as tipo, marca, modelo, ano, cor, placa, quilometragem 
                          FROM veiculos WHERE id_usuario = ? ORDER BY id DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $veiculos[] = $row;
        }

        $stmt->close();
    } catch (Exception $e) {
        $mensagem = "<script>alert('Erro ao buscar veículos: " . addslashes($e->getMessage()) . "');</script>";
    }
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

<?php echo $mensagem; ?>

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
                    <a href="/fixTime/PROJETO/src/views/main-page/agendamentos.html" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path>
                        </svg>
                        <span class="ms-3">Meus agendamentos</span>
                    </a>
                </li>


                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/historico-servicos.html" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Histórico de serviços</span>
                    </a>
                </li>


                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/prestadores-servico.html" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Prestadores de serviços</span>
                    </a>
                </li>

                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/veiculos.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Meus veículos</span>
                    </a>
                </li>

                <li>
                    <a href="/fixTime/PROJETO/src/views/main-page/perfil.php" class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-gray-100  group">
                        <svg class="shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" data-slot="icon" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Perfil</span>
                    </a>
                </li>

            </ul>
        </div>
    </aside>

    <div class=" lg:ml-64 p-4 lg:p-14">

        <!-- cadastrar veiculos -->
        <div>
            <form action="/fixTime/PROJETO/src/views/main-page/veiculos.php" method="POST">
                <div class="grid lg:gap-6 gap-4 mb-6 md:grid-cols-6">
                    <!-- Tipo -->
                    <div class="lg:col-span-1 col-span-6">
                        <label for="tipo_veiculos" class="block mb-2 text-sm font-medium text-gray-900">Tipo de veículo</label>
                        <select name="tipo_veiculos" id="tipo_veiculos" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none cursor-pointer" required>
                            <option value="">Selecione</option>
                            <option value="carro">Carro</option>
                            <option value="moto">Moto</option>
                            <option value="caminhao">Caminhão</option>
                            <option value="van">Van</option>
                            <option value="onibus">Ônibus</option>
                        </select>
                    </div>

                    <!-- Marca -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="marca_veiculo" class="block mb-2 text-sm font-medium text-gray-900">Marca</label>
                        <input name="marca_veiculo" type="text" id="marca_veiculo" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: Honda" required>
                    </div>

                    <!-- Modelo -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="modelo_veiculo" class="block mb-2 text-sm font-medium text-gray-900">Modelo</label>
                        <input name="modelo_veiculo" type="text" id="modelo_veiculo" class=" focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: Civic" required>
                    </div>

                    <!-- Ano -->
                    <div class="lg:col-span-1 col-span-6">
                        <label for="ano_veiculo" class="block mb-2 text-sm font-medium text-gray-900">Ano</label>
                        <input name="ano_veiculo" type="number" id="ano_veiculo" min="1900" max="2099" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: 2020" required>
                    </div>

                    <!-- Cor -->
                    <div class="lg:col-span-1 col-span-6">
                        <label for="cor_veiculo" class="block mb-2 text-sm font-medium text-gray-900">Cor</label>
                        <input name="cor_veiculo" type="text" id="cor_veiculo" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: Prata" required >
                    </div>

                    <!-- Placa -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="placa_veiculo" class="block mb-2 text-sm font-medium text-gray-900">Placa</label>
                        <input name="placa_veiculo" type="text" id="placa_veiculo" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: ABC1234 ou ABC1D23" required maxlength="7">
                    </div>

                    <!-- Quilometragem -->
                    <div class="lg:col-span-2 col-span-6">
                        <label for="quilometragem_veiculo" class="block mb-2 text-sm font-medium text-gray-900">Quilometragem</label>
                        <input name="quilometragem_veiculo" type="text" id="quilometragem_veiculo" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none" placeholder="Ex: 30000" required>
                    </div>

                    <!-- Botão -->
                    <div class="lg:col-span-1 flex">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm lg:w-full w-auto px-5 py-2.5 text-center cursor-pointer mt-7">Registrar</button>

                    </div>
                </div>



            </form>



            <?php if (!empty($veiculos)): ?>
                <h2 class="text-xl font-bold mt-10 mb-4">Veículos cadastrados</h2>

                <?php foreach ($veiculos as $veiculo): ?>
                    <div class="mt-6" id="veiculo-<?= $veiculo['id'] ?>">
                        <hr class="h-px my-8 bg-gray-200 border-0">

                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <form action="atualizar_veiculo.php" method="POST" class="form-veiculo">
                                <input type="hidden" name="id" value="<?= $veiculo['id'] ?>">

                                <div class="grid lg:gap-6 gap-4 mb-6 md:grid-cols-6 grid-cols-2">

                                    <div class="col-span-1">
                                        <label class="block mb-1 text-sm font-medium text-gray-900">ID</label>
                                        <input type="text" value="<?= htmlspecialchars($veiculo['id']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 cursor-not-allowed" disabled />
                                    </div>

                                    <div class="lg:col-span-2 col-span-1">
                                        <label for="tipo-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Tipo Veículo</label>
                                        <select name="tipo_veiculo" id="tipo-<?= $veiculo['id'] ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                            <option value="carro" <?= $veiculo['tipo'] == 'carro' ? 'selected' : '' ?>>Carro</option>
                                            <option value="moto" <?= $veiculo['tipo'] == 'moto' ? 'selected' : '' ?>>Moto</option>
                                            <option value="caminhao" <?= $veiculo['tipo'] == 'caminhao' ? 'selected' : '' ?>>Caminhão</option>
                                            <option value="van" <?= $veiculo['tipo'] == 'van' ? 'selected' : '' ?>>Van</option>
                                            <option value="onibus" <?= $veiculo['tipo'] == 'onibus' ? 'selected' : '' ?>>Ônibus</option>
                                        </select>
                                    </div>

                                    <div class="lg:col-span-2 col-span-1">
                                        <label for="marca-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Marca</label>
                                        <input name="marca" type="text" id="marca-<?= $veiculo['id'] ?>" value="<?= htmlspecialchars($veiculo['marca']) ?>" class=" focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="col-span-1">
                                        <label for="modelo-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Modelo</label>
                                        <input name="modelo" type="text" id="modelo-<?= $veiculo['id'] ?>" value="<?= htmlspecialchars($veiculo['modelo']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="col-span-1">
                                        <label for="ano-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Ano</label>
                                        <input name="ano" type="number" id="ano-<?= $veiculo['id'] ?>" value="<?= htmlspecialchars($veiculo['ano']) ?>" min="1900" max="2099" class="mascara-ano focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="lg:col-span-2 col-span-1">
                                        <label for="cor-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Cor</label>
                                        <input name="cor" type="text" id="cor-<?= $veiculo['id'] ?>" value="<?= htmlspecialchars($veiculo['cor']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="lg:col-span-2 col-span-1">
                                        <label for="placa-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Placa</label>
                                        <input name="placa" type="text" id="placa-<?= $veiculo['id'] ?>" value="<?= htmlspecialchars($veiculo['placa']) ?>" class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>

                                    <div class="col-span-1">
                                        <label for="quilometragem-<?= $veiculo['id'] ?>" class="block mb-1 text-sm font-medium text-gray-900">Quilometragem</label>
                                        <input name="quilometragem" type="text" id="quilometragem-<?= $veiculo['id'] ?>" value="<?= number_format($veiculo['quilometragem'], 0, '', '.') ?>" class="mascara-quilometragem focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50  border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 outline-none cursor-not-allowed" disabled>
                                    </div>
                                </div>

                                <div class="lg:gap-6 gap-4 items-center grid grid-cols-6">
                                    <!-- Botão Editar/Salvar (alterna entre os dois estados) -->
                                    <button type="button" class="editar-btn text-white inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3" data-id="<?= $veiculo['id'] ?>">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Editar
                                    </button>

                                    <!-- Botão Excluir/Cancelar (alterna entre os dois estados) -->
                                    <button type="button" class="excluir-btn inline-flex items-center justify-center gap-2 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-pointer col-span-3" data-id="<?= $veiculo['id'] ?>">
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
                <hr class="h-px my-8 bg-gray-200 border-0">
                <div class="mt-10 p-4 rounded-lg bg-gray-100 border shadow-2xl flex items-center justify-between ">
                    <div>
                      <p class="font-medium">Nenhum veículo cadastrado.</p>
                      <p class="text-sm">Adicione seu primeiro veículo usando o formulário acima.</p>
                    </div>
                </div>


            <?php endif; ?>



    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script>
        $('#quilometragem_veiculo').mask('000.000', {reverse: true});
        $('#ano_veiculo').mask('0000');
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



                // Controle de edição de veículos
                document.querySelectorAll('.editar-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const form = this.closest('.form-veiculo');
                        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');
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
                            if (confirm('Tem certeza que deseja excluir este veículo?')) {
                                // Criar formulário temporário para exclusão
                                const deleteForm = document.createElement('form');
                                deleteForm.action = 'excluir_veiculo.php';
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