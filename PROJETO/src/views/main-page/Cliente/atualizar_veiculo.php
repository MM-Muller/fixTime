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
    // Obtém e sanitiza os dados do formulário
    // Usa filter_input para dados numéricos
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    // Sanitiza strings usando htmlspecialchars para prevenir XSS
    $tipo = htmlspecialchars($_POST['tipo_veiculo'] ?? '', ENT_QUOTES, 'UTF-8');
    $marca = htmlspecialchars($_POST['marca'] ?? '', ENT_QUOTES, 'UTF-8');
    $modelo = htmlspecialchars($_POST['modelo'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Filtra o ano como número inteiro
    $ano = filter_input(INPUT_POST, 'ano', FILTER_SANITIZE_NUMBER_INT);
    
    // Sanitiza strings para cor e placa
    $cor = filter_input(INPUT_POST, 'cor', FILTER_SANITIZE_STRING);
    $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_STRING);
    
    // Processa a quilometragem
    // Remove pontos e vírgulas e converte para inteiro
    $quilometragemStr = $_POST['quilometragem'] ?? '0';
    $quilometragem = (int) str_replace(['.', ','], '', $quilometragemStr);

    try {
        // Prepara a query SQL usando prepared statements para prevenir SQL Injection
        $stmt = $conexao->prepare("UPDATE veiculos SET 
            tipo_veiculo = ?, 
            marca = ?, 
            modelo = ?, 
            ano = ?, 
            cor = ?, 
            placa = ?, 
            quilometragem = ? 
            WHERE id = ? AND id_usuario = ?");

        // Vincula os parâmetros à query
        // 's' para strings, 'i' para inteiros
        $stmt->bind_param("ssssssiii", $tipo, $marca, $modelo, $ano, $cor, $placa, $quilometragem, $id, $_SESSION['id_usuario']);

        // Executa a query e verifica o resultado
        if ($stmt->execute()) {
            // Sucesso: exibe mensagem de confirmação
            $_SESSION['mensagem'] = "<script>alert('Veículo atualizado com sucesso!');</script>";
        } else {
            // Erro: exibe mensagem com detalhes do erro
            $_SESSION['mensagem'] = "<script>alert('Erro ao atualizar veículo: " . addslashes($stmt->error) . "');</script>";
        }

        // Fecha o statement
        $stmt->close();
    } catch (Exception $e) {
        // Captura e exibe erros de banco de dados
        $_SESSION['mensagem'] = "<script>alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Redireciona de volta para a página de veículos
header("Location: /fixTime/PROJETO/src/views/main-page/Cliente/veiculos.php");
exit;
