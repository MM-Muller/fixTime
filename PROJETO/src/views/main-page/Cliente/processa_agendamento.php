<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Valida se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit();
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_usuario = $_SESSION['id_usuario'];
    $id_oficina = isset($_GET['id_oficina']) ? (int) $_GET['id_oficina'] : null;

    // Validação dos dados recebidos
    $id_veiculo = isset($_POST['veiculo']) ? (int) $_POST['veiculo'] : null;
    $data_agendada = isset($_POST['data']) ? $_POST['data'] : null;
    $horario = isset($_POST['horario']) ? $_POST['horario'] : null;

    if (!$id_oficina || !$id_veiculo || !$data_agendada || !$horario) {
        echo "<script>alert('Preencha todos os campos obrigatórios.'); window.history.back();</script>";
        exit();
    }

    // Inserção dos dados na tabela serviço
    $stmtServico = $conexao->prepare("INSERT INTO servico (data_agendada, horario, id_veiculo, id_oficina) VALUES (?, ?, ?, ?)");
  
    $stmtServico->bind_param("ssii", $data_agendada, $horario, $descricao_servico, $id_veiculo, $id_oficina);

    if ($stmtServico->execute()) {
        echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.php';</script>";
    } else {
        if (strpos($conexao->error, 'Duplicate entry') !== false) {
            echo "<script>alert('Esse horário já está agendado. Escolha outro horário.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Erro ao realizar agendamento: " . addslashes($conexao->error) . "'); window.history.back();</script>";
        }
    }

    $stmtServico->close();
} else {
    echo "<script>alert('Método de requisição inválido.'); window.history.back();</script>";
}

$conexao->close();
