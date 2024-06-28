<?php
session_start();

// Finaliza a sessão
session_unset();
session_destroy();

// Redireciona para a página de login após o logout
header('Location: login.php');
exit();
?>
