<?php
session_start(); // Inicia sessão para poder destruí-la

// Destrói todos os dados da sessão
session_unset();
session_destroy();

// Redireciona pro login
header("Location: /fixTime/PROJETO/index.html");
exit;
