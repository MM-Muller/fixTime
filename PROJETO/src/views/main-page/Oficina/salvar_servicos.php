<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

$oficina_id = $_SESSION['id_oficina'] ?? null;

if (!$oficina_id) {
    echo "<script>alert('Usuário não autenticado. Faça login novamente.'); window.location.href='/fixTime/PROJETO/src/views/Login/login-company.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['servicos'])) {
    $servicos_selecionados = $_POST['servicos'];

    // Remove todos os serviços antigos
    $sqlDelete = "DELETE FROM oficina_servicos WHERE id_oficina = ?";
    $stmtDelete = $conexao->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $oficina_id);
    $stmtDelete->execute();

    // Insere os novos
    $sqlInsert = "INSERT INTO oficina_servicos (id_oficina, id_servico_padrao) VALUES (?, ?)";
    $stmtInsert = $conexao->prepare($sqlInsert);

    foreach ($servicos_selecionados as $servico_id) {
        $stmtInsert->bind_param("ii", $oficina_id, $servico_id);
        $stmtInsert->execute();
    }

    echo "<script>alert('Serviços registrados com sucesso!'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/registrar-servicos.php';</script>";
    exit();
} else {
    echo "<script>alert('Por favor, selecione pelo menos um serviço.'); window.location.href='/fixTime/PROJETO/src/views/main-page/Oficina/registrar-servicos.php';</script>";
    exit();
}
?>
