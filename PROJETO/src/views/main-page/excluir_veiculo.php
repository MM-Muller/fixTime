<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

session_start();
if (!isset($_SESSION['id_usuario'])) {
    die("Acesso não autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $conexao->prepare("DELETE FROM veiculos WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id, $_SESSION['id_usuario']);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "<script>alert('Veículo excluído com sucesso!');</script>";
        } else {
            $_SESSION['mensagem'] = "<script>alert('Erro ao excluir veículo: " . addslashes($stmt->error) . "');</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
    }
}

header("Location: /fixTime/PROJETO/src/views/main-page/veiculos.php");
exit;
