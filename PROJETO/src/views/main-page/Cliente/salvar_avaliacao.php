<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['error'] = "Você precisa estar logado para avaliar.";
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_oficina = $_POST['id_oficina'] ?? null;
    $estrelas = $_POST['estrelas'] ?? null;

    if (!$id_oficina || !$estrelas) {
        $_SESSION['error'] = "Por favor, selecione uma avaliação com estrelas.";
        header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
        exit;
    }

    try {
        // Verifica se já existe uma avaliação para esta oficina e usuário
        $check_sql = "SELECT id_avaliacao FROM avaliacao WHERE id_oficina = ? AND id_usuario = ?";
        $check_stmt = $conexao->prepare($check_sql);
        $check_stmt->bind_param("ii", $id_oficina, $_SESSION['id_usuario']);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // Se existe, atualiza a avaliação existente
            $sql = "UPDATE avaliacao SET estrelas = ? WHERE id_oficina = ? AND id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("iii", $estrelas, $id_oficina, $_SESSION['id_usuario']);
        } else {
            // Se não existe, insere uma nova avaliação
            $sql = "INSERT INTO avaliacao (id_usuario, id_oficina, estrelas) VALUES (?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("iii", $_SESSION['id_usuario'], $id_oficina, $estrelas);
        }
        
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta: " . $conexao->error);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Avaliação salva com sucesso! Obrigado por avaliar nosso serviço.";
        } else {
            throw new Exception("Erro ao executar a consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Ocorreu um erro ao salvar sua avaliação. Por favor, tente novamente mais tarde.";
        error_log("Erro na avaliação: " . $e->getMessage());
    }

    $stmt->close();
    $check_stmt->close();
    $conexao->close();
    
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
    exit;
} else {
    header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/meus-agendamentos.php");
    exit;
}
?>
