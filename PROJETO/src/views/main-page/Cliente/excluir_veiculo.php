<?php
// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Inicia a sessão para gerenciar dados do usuário
session_start();

// Verifica se o usuário está autenticado
// Se não houver ID de usuário na sessão, bloqueia o acesso
if (!isset($_SESSION['id_usuario'])) {
    die("Acesso não autorizado.");
}

// Processa o formulário quando enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém e sanitiza o ID do veículo
    // Usa filter_input para garantir que seja um número inteiro
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    try {
        // Prepara a query SQL usando prepared statements para prevenir SQL Injection
        // Verifica tanto o ID do veículo quanto o ID do usuário para garantir segurança
        $stmt = $conexao->prepare("DELETE FROM veiculos WHERE id = ? AND id_usuario = ?");
        
        // Vincula os parâmetros à query
        // 'i' indica que são parâmetros inteiros
        $stmt->bind_param("ii", $id, $_SESSION['id_usuario']);

        // Executa a query e verifica o resultado
        if ($stmt->execute()) {
            // Sucesso: exibe mensagem de confirmação
            $_SESSION['mensagem'] = "<script>alert('Veículo excluído com sucesso!');</script>";
        } else {
            // Erro: exibe mensagem com detalhes do erro
            $_SESSION['mensagem'] = "<script>alert('Erro ao excluir veículo: " . addslashes($stmt->error) . "');</script>";
        }

        // Fecha o statement para liberar recursos
        $stmt->close();
    } catch (Exception $e) {
        // Captura e exibe erros de banco de dados
        $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Redireciona de volta para a página de veículos
header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php");
exit;
