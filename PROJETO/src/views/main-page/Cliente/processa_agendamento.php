<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-user.php';</script>";
    exit();
}

// Dados do formulário
$id_usuario = $_SESSION['id_usuario'];
$id_oficina = isset($_POST['id_oficina']) ? (int) $_POST['id_oficina'] : 0;
$id_veiculo = (int) $_POST['veiculo'] ?? 0;
$data_agendada = $_POST['data'] ?? null;
$horario = $_POST['horario'] ?? null;

// Verificação básica
if (!$id_oficina || !$id_veiculo || !$data_agendada || !$horario) {
    echo "<script>alert('Preencha todos os campos obrigatórios.'); window.history.back();</script>";
    exit();
}

// Inserção no banco
$stmt = $conexao->prepare("INSERT INTO servico (data_agendada, horario, id_veiculo, id_oficina) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssii", $data_agendada, $horario, $id_veiculo, $id_oficina);

if ($stmt->execute()) {
    echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='/fixTime/PROJETO/src/views/main-page/Cliente/prestadores-servico.php';</script>";
} else {
    echo "<script>alert('Erro ao agendar. Tente novamente.'); window.history.back();</script>";
}

$stmt->close();
?>