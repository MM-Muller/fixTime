<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($conexao) || !$conexao) {
    die("Erro ao conectar ao banco de dados.");
}

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit;
}

// Buscar veículos do cliente
$stmtVeiculos = $conexao->prepare("
    SELECT id, tipo_veiculo AS tipo, marca, modelo, ano, cor, placa, quilometragem 
    FROM veiculos 
    WHERE id_usuario = ? 
    ORDER BY id DESC
");
$stmtVeiculos->bind_param("i", $id_usuario);
$stmtVeiculos->execute();
$resultVeiculos = $stmtVeiculos->get_result();


// Buscar oficinas (com ou sem filtro por categoria)
$query = "
    SELECT id_oficina, nome_oficina, email_oficina, telefone_oficina, 
           bairro_oficina, endereco_oficina, categoria, numero_oficina, 
           complemento, cidade_oficina 
    FROM oficina
";

if (!empty($filter)) {
    $query .= " WHERE categoria = ?";
    $stmtOficinas = $conexao->prepare($query);
    $stmtOficinas->bind_param("s", $filter);
} else {
    $stmtOficinas = $conexao->prepare($query);
}

$stmtOficinas->execute();
$resultOficinas = $stmtOficinas->get_result();


?>

<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/fixTime/PROJETO/src/public/assets/css/output.css">
    <title>Fix Time - Agendamento</title>
</head>

<body class="bg-gray-50">
    <div class="absolute top-0 left-0 p-4">
    <a href="/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Voltar</a>
  </div>
    <div class="flex lg:py-14">
        <div class="mx-auto">
            <div class="max-w-5xl w-full bg-white border border-gray-200 rounded-lg shadow-sm">

                <div class="lg:py-10 lg:px-10">

                    <div>
                        <p class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Agendar Serviço</p>
                        <p class=" mb-6 text-gray-600">Preencha os dados abaixo para agendar seu serviço com <?= htmlspecialchars($row['nome_oficina']) ?></p>
                    </div>

                    

                    <!-- Formulário de Agendamento -->
                    <form method="POST" action="processa_agendamento.php" class="space-y-6">

                        <!-- Veículo -->
                        <div class="space-y-4">
                            
                            <div>
                                <label for="veiculo" class="block mb-2 text-sm font-medium text-gray-900">Veículo</label>
                                <select name="veiculo" id="veiculo" required
                                        class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none">
                                    <option value="">Selecione um veículo</option>
                                    <?php foreach ($veiculos as $veiculo): ?>
                                        <option value="<?= $veiculo['id_veiculo'] ?>">
                                            <?= htmlspecialchars($veiculo['modelo']) ?> - Placa: <?= htmlspecialchars($veiculo['placa']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Data -->
                            <div class="space-y-2">
                                <label for="data" class="block mb-2 text-sm font-medium text-gray-900">Data</label>
                                <input type="date" name="data" id="data" required min="<?= date('Y-m-d') ?>"
                                       class="focus:ring-blue-500 focus:border-blue-500 border-2 bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 outline-none">
                            </div>
                            <!-- Horário -->
                            <div class="space-y-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Horário</label>

                                <div class="grid grid-cols-2 gap-2">
                                    <?php
                                    $horarios = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
                                    foreach ($horarios as $index => $hora):
                                        $id = "hora-" . $index;
                                    ?>
                                        <div>
                                            <!-- O input precisa ser peer e vir ANTES do label -->
                                            <input type="radio" name="horario" value="<?= $hora ?>" id="<?= $id ?>" class="peer hidden" required>
                                            <label for="<?= $id ?>" class="block text-center border-2 border-gray-300 bg-gray-50 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer transition 
                                                hover:bg-gray-200 
                                                peer-checked:bg-gray-800 
                                                peer-checked:text-white 
                                                peer-checked:border-gray-800">
                                                <?= $hora ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <hr class="m-6">
                            <!-- Botão -->
                            <div class="mt-10">
                                <button type="submit"
                                        class="cursor-pointer w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-semibold rounded-lg text-sm px-5 py-2.5 text-center">
                                    Confirmar Agendamento
                                </button>
                            </div>
                        </div>
    
                                
                    </form>
                                
                </div>
            </div>
        </div>
    </div>
</body>
</html>
