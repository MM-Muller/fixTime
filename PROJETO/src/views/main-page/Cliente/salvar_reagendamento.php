<?php
// salvar_reagendamento.php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Usuário não autenticado.'); window.history.back();</script>";
    exit;
}

// Valida os dados recebidos
$id_servico = $_POST['id_servico'] ?? null;
$data_agendada = $_POST['data_agendada'] ?? null;
$horario = $_POST['horario'] ?? null;

if (!$id_servico || !$data_agendada || !$horario) {
    echo "<script>alert('Dados incompletos.'); window.history.back();</script>";
    exit;
}

// Atualiza a tabela servico
$sql = "UPDATE servico SET data_agendada = ?, horario = ? WHERE id_servico = ?";
$stmt = $conexao->prepare($sql);

if (!$stmt) {
    echo "<script>alert('Erro na preparação do SQL.'); window.history.back();</script>";
    exit;
}

$stmt->bind_param("ssi", $data_agendada, $horario, $id_servico);

if ($stmt->execute()) {
    echo "<script>alert('Agendamento atualizado com sucesso.'); window.location.href = 'meus-agendamentos.php';</script>";
    exit;
} else {
    echo "<script>alert('Erro ao reagendar.'); window.history.back();</script>";
    exit;
}
?>
