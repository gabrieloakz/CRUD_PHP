<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Configurações de conexão com a base de dados
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'users';

// Tenta conectar à base de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de ligação: " . $conn->connect_error);
}

// Verifica se a conexão foi perdida e reconecta se necessário
if (!$conn->ping()) {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Erro de reconexão: " . $conn->connect_error);
    }
}

// Consulta SQL para obter todos os utilizadores
$sql = "SELECT username, password, email FROM utilizadores";
$result = $conn->query($sql);

// Verifica se há resultados
if ($result->num_rows > 0) {
    echo "<h2>Lista de Utilizadores</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nome de Utilizador</th><th>Palavra-passe</th><th>Email</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['password']) . "</td>"; // Exibição da senha (não recomendado)
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum utilizador encontrado.";
}

// Fecha a ligação à base de dados
$conn->close();
?>

<br>
<a href="logout.php">Sair</a>
