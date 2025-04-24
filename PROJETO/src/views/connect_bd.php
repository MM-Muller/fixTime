<?php
function connect_db()
{
    $db_name = "fixTime";
    $user = "root";
    $pass = "";
    $server = "localhost:3306";

    // Criar conexão
    $conexao = new mysqli($server, $user, $pass, $db_name);

    // Verificar conexão
    if ($conexao->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
    }
    return $conexao;
}
?>

