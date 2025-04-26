<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

session_start();
if (!isset($_SESSION['id_oficina'])) {
    die("Acesso não autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $conexao->prepare("DELETE FROM funcionarios WHERE id_funcionario = ? AND id_oficina = ?");
        $stmt->bind_param("ii", $id, $_SESSION['id_oficina']);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "<script>alert('Funcionário excluído com sucesso!');</script>";
        } else {
            $_SESSION['mensagem'] = "<script>alert('Erro ao excluir funcionário: " . addslashes($stmt->error) . "');</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
    }
}

header("Location: /fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php");
exit;
