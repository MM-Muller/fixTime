<?php
// Inicia a sessão PHP para manter o estado do usuário
session_start();

// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Obtém o ID da oficina da sessão
$oficina_id = $_SESSION['id_oficina'] ?? null;

// Verifica se o usuário está autenticado
if (!$oficina_id) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-company.php';</script>";
    exit();
}

// Verifica se a requisição é POST e se existem serviços selecionados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['servicos'])) {
    // Armazena os serviços selecionados do formulário
    $servicos_selecionados = $_POST['servicos'];

    // Remove todos os serviços antigos da oficina
    $sqlDelete = "DELETE FROM oficina_servicos WHERE id_oficina = ?";
    $stmtDelete = $conexao->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $oficina_id);
    $stmtDelete->execute();

    // Prepara a query para inserir os novos serviços
    $sqlInsert = "INSERT INTO oficina_servicos (id_oficina, id_servico_padrao) VALUES (?, ?)";
    $stmtInsert = $conexao->prepare($sqlInsert);

    // Insere cada serviço selecionado no banco de dados
    foreach ($servicos_selecionados as $servico_id) {
        $stmtInsert->bind_param("ii", $oficina_id, $servico_id);
        $stmtInsert->execute();
    }

    // Exibe mensagem de sucesso e redireciona para a página de registro de serviços
    echo "<script>alert('Serviços registrados com sucesso!'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/registrar-servicos.php';</script>";
    exit();
} else {
    // Exibe mensagem de erro se nenhum serviço foi selecionado
    echo "<script>alert('Por favor, selecione pelo menos um serviço.'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/registrar-servicos.php';</script>";
    exit();
}
?>
