<?php
// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Inicia a sessão
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['error'] = 'Usuário não autenticado. Faça login novamente.';
    header("Location: /fixTime/PROJETO/src/views/Login/login-user.php");
    exit();
}

// Verifica se o ID do veículo foi fornecido
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $_SESSION['error'] = 'ID do veículo inválido.';
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php");
    exit();
}

$id_veiculo = (int)$_POST['id'];
$id_usuario = $_SESSION['id_usuario'];

try {
    // Verifica se o veículo pertence ao usuário
    $stmt = $conexao->prepare("SELECT id FROM veiculos WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_veiculo, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Veículo não encontrado ou não pertence ao seu usuário.';
        header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php");
        exit();
    }

    // Exclui o veículo
    $stmt = $conexao->prepare("DELETE FROM veiculos WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_veiculo, $id_usuario);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Veículo excluído com sucesso!';
    } else {
        $_SESSION['error'] = 'Erro ao excluir veículo: ' . $stmt->error;
    }

    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = 'Erro ao excluir veículo: ' . $e->getMessage();
}

header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php");
exit();
