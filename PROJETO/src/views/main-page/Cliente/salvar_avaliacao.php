<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();
session_start();

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['error_message'] = 'Você precisa estar logado para avaliar.';
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
    exit;
}

$id_cliente = $_SESSION['id_usuario'];
$id_oficina = $_POST['id_oficina'] ?? null;
$estrelas = $_POST['estrelas'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');

if (!$id_oficina || !$id_servico || !$estrelas) {
    $_SESSION['error_message'] = 'Dados incompletos para avaliação.';
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
    exit;
}

$sql = "INSERT INTO avaliacao (id_usuario, id_oficina, estrelas) VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("iii", $id_cliente, $id_oficina, $estrelas);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'Avaliação enviada com sucesso!';
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
} else {
    $_SESSION['error_message'] = 'Erro ao salvar avaliação.';
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
}
?>
