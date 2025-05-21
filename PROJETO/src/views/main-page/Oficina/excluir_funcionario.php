<?php
// Inclui o arquivo de conexão com o banco de dados
include $_SERVER['DOCUMENT_ROOT'] . '/fixTime/PROJETO/src/views/connect_bd.php';
$conexao = connect_db();

// Inicia a sessão PHP para manter o estado do usuário
session_start();

// Verifica se o usuário está autenticado como oficina
if (!isset($_SESSION['id_oficina'])) {
    die("Acesso não autorizado.");
}

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza o ID do funcionário recebido do formulário
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    try {
        // Prepara a query SQL para excluir o funcionário
        // Usa prepared statements para prevenir SQL injection
        // Verifica tanto o ID do funcionário quanto o ID da oficina para garantir segurança
        $stmt = $conexao->prepare("DELETE FROM funcionarios WHERE id_funcionario = ? AND id_oficina = ?");
        
        // Vincula os parâmetros à query
        // "ii" indica que ambos os parâmetros são inteiros
        $stmt->bind_param("ii", $id, $_SESSION['id_oficina']);

        // Executa a query e verifica o resultado
        if ($stmt->execute()) {
            // Sucesso na exclusão
            $_SESSION['mensagem'] = "<script>alert('Funcionário excluído com sucesso!');</script>";
        } else {
            // Erro na exclusão
            $_SESSION['mensagem'] = "<script>alert('Erro ao excluir funcionário: " . addslashes($stmt->error) . "');</script>";
        }

        // Fecha o statement para liberar recursos
        $stmt->close();
    } catch (Exception $e) {
        // Tratamento de exceções do banco de dados
        $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Redireciona de volta para a página de funcionários após a operação
header("Location: /fixTime/PROJETO/src/views/main-page/Oficina/funcionarios.php");
exit;
