<?php
// Verifica se o formulário foi enviado
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

    // Verifica se a conexão foi perdida e reconecta se necessário
    if (!$conn->ping()) {
        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            die("Erro de reconexão: " . $conn->connect_error);
        }
    }

    // Obtém os dados do formulário
    $nome = $_POST["nome"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // Prepara e executa a query de inserção (sem hash da senha)
    $stmt = $conn->prepare("INSERT INTO utilizadores (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $password, $email);

    if ($stmt->execute()) {
        echo "Utilizador cadastrado com sucesso!";
        // Redireciona para a página de login após o cadastro
        header("Location: login.php");
        exit();
    } else {
        echo "Erro ao cadastrar utilizador: " . $stmt->error;
    }

    $stmt->close(); // Fecha o statement
    $conn->close(); // Fecha a conexão com o banco de dados
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Utilizadores</title>
</head>
<body>
    <h2>Cadastro de Utilizadores</h2>
    <form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>">
        <label for="nome">Nome de utilizador:</label>
        <input id="nome" name="nome" type="text" required><br><br>

        <label for="password">Palavra Passe:</label>
        <input id="password" name="password" type="password" required><br><br>

        <label for="email">Email:</label>
        <input id="email" name="email" type="email" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>
