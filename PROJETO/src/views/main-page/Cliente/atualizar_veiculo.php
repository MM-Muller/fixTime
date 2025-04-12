<?php
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

session_start();
if (!isset($_SESSION['id_usuario'])) {
    die("Acesso não autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $tipo = filter_input(INPUT_POST, 'tipo_veiculo', FILTER_SANITIZE_STRING);
    $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
    $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);
    $ano = filter_input(INPUT_POST, 'ano', FILTER_SANITIZE_NUMBER_INT);
    $cor = filter_input(INPUT_POST, 'cor', FILTER_SANITIZE_STRING);
    $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_STRING);
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
