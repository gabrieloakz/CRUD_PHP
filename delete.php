<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

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

// Verifica se o nome de usuário foi fornecido via parâmetro GET
if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo "Nome de Utilizador não fornecido.";
    exit();
}

$username = $_GET['username'];

// Prepara e executa a query para excluir o usuário pelo nome de usuário
$sql = "DELETE FROM utilizadores WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    echo "Utilizador excluído com sucesso.";
} else {
    echo "Erro ao excluir Utilizador: " . $stmt->error;
}

// Fecha a conexão com o banco de dados
$conn->close();

echo "<br><a href='listagem.php'>Voltar para a Lista de Utilizadores</a>";
?>
