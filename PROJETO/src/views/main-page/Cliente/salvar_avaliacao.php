<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Você precisa estar logado para avaliar.'); history.back();</script>";
    exit;
}

$id_cliente = $_SESSION['id_usuario'];
$id_oficina = $_POST['id_oficina'] ?? null;
$estrelas = $_POST['estrelas'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');

if (!$id_oficina || !$id_servico || !$estrelas) {
    echo "<script>alert('Dados incompletos para avaliação.'); history.back();</script>";
    exit;
}

$sql = "INSERT INTO avaliacao (id_usuario, id_oficina, estrelas) VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("iii", $id_cliente, $id_oficina, $estrelas);

if ($stmt->execute()) {
    echo "<script>alert('Avaliação enviada com sucesso!'); location.href='meus-agendamentos.php';</script>";
} else {
    echo "<script>alert('Erro ao salvar avaliação.'); history.back();</script>";
}
?>
