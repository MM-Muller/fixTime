<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

session_start();
if (!isset($_SESSION['id_usuario'])) {
    die("Acesso não autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $tipo = htmlspecialchars($_POST['tipo_veiculo'] ?? '', ENT_QUOTES, 'UTF-8');
    $marca = htmlspecialchars($_POST['marca'] ?? '', ENT_QUOTES, 'UTF-8');
    $modelo = htmlspecialchars($_POST['modelo'] ?? '', ENT_QUOTES, 'UTF-8');
    $ano = filter_input(INPUT_POST, 'ano', FILTER_SANITIZE_NUMBER_INT);
    $cor = htmlspecialchars($_POST['cor'] ?? '', ENT_QUOTES, 'UTF-8');
    $placa = htmlspecialchars($_POST['placa'] ?? '', ENT_QUOTES, 'UTF-8');
    $quilometragem = filter_input(INPUT_POST, 'quilometragem', FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $conexao->prepare("UPDATE veiculos SET 
            tipo_veiculo = ?, 
            marca = ?, 
            modelo = ?, 
            ano = ?, 
            cor = ?, 
            placa = ?, 
            quilometragem = ? 
            WHERE id = ? AND id_usuario = ?");

        $stmt->bind_param("ssssssiii", $tipo, $marca, $modelo, $ano, $cor, $placa, $quilometragem, $id, $_SESSION['id_usuario']);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "<script>alert('Veículo atualizado com sucesso!');</script>";
        } else {
            $_SESSION['mensagem'] = "<script>alert('Erro ao atualizar veículo: " . addslashes($stmt->error) . "');</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
    }
}

header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php");
exit;
