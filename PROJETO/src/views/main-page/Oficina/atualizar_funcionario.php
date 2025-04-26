<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

session_start();
if (!isset($_SESSION['id_oficina'])) {
    die("Acesso não autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $nome = htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8');
    $cargo = htmlspecialchars($_POST['cargo'] ?? '', ENT_QUOTES, 'UTF-8');
    $telefone = htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $data_admissao = htmlspecialchars($_POST['data_admissao'] ?? '', ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $conexao->prepare("UPDATE funcionarios SET 
            nome_funcionario = ?, 
            cargo_funcionario = ?, 
            telefone_funcionario = ?, 
            email_funcionario = ?, 
            data_admissao = ? 
            WHERE id_funcionario = ? AND id_oficina = ?");

        $stmt->bind_param("sssssii", $nome, $cargo, $telefone, $email, $data_admissao, $id, $_SESSION['id_oficina']);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "<script>alert('Funcionário atualizado com sucesso!');</script>";
        } else {
            $_SESSION['mensagem'] = "<script>alert('Erro ao atualizar funcionário: " . addslashes($stmt->error) . "');</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        $erro = $e->getMessage();
        if (str_contains($erro, 'Duplicate entry') && str_contains($erro, 'email_funcionario')) {
            $_SESSION['mensagem'] = "<script>alert('Este email já está cadastrado para outro funcionário.');</script>";
        } else {
            $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($erro) . "');</script>";
        }
    }
}

header("Location: /fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php");
exit;
