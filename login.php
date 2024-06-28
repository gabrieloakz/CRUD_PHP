<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configurações de conexão com o banco de dados
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'users';

    // Tenta conectar ao banco de dados
    $conn = new mysqli($host, $user, $pass, $dbname);

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Obtém os dados do formulário
    $name = $_POST["nome"];
    $passe = $_POST["password"];

    // Prepara e executa a query para buscar o usuário pelo nome e senha
    $stmt = $conn->prepare("SELECT * FROM utilizadores WHERE username = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se encontrou o usuário
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        // Verifica a senha (neste exemplo, estamos comparando sem hash)
        if ($passe == $usuario['password']) {
            // Login bem sucedido
            $_SESSION['username'] = $usuario['username'];
            // Redireciona para a página de listagem de usuários
            header('Location: listagem.php');
            exit();
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "Usuário não encontrado.";
    }

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página de Login</title>
</head>
<body>
    <h2>Autenticação de Utilizadores</h2>
    <form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>">
        <label for="nome">Nome de utilizador:</label>
        <input id="nome" name="nome" type="text" required><br><br>
        
        <label for="password">Palavra Passe:</label>
        <input id="password" name="password" type="password" required><br><br>
        
        <input type="submit" value="Entrar">
    </form>
</body>
</html>
