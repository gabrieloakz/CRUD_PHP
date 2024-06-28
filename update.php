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

// Verifica se o nome de utilizador foi fornecido via parâmetro GET
if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo "Nome de utilizador não fornecido.";
    exit();
}

$username = $_GET['username'];

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação básica (pode ser expandida conforme necessário)
    $errors = [];
    if (empty($_POST['username'])) {
        $errors[] = "Nome de utilizador é obrigatório.";
    }
    if (empty($_POST['email'])) {
        $errors[] = "Email é obrigatório.";
    }

    if (empty($errors)) {
        // Atualizações de nome de utilizador, palavra-passe e email
        $newUsername = $_POST['username'];
        $newEmail = $_POST['email'];

        // Verifica se a palavra-passe foi fornecida para atualização
        $updatePassword = false;
        if (!empty($_POST['password'])) {
            $updatePassword = true;
            $newPassword = $_POST['password'];
        }

        // Prepara a query de atualização
        if ($updatePassword) {
            $sql = "UPDATE utilizadores SET username = ?, password = ?, email = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt->bind_param("ssss", $newUsername, $hashedPassword, $newEmail, $username);
        } else {
            $sql = "UPDATE utilizadores SET username = ?, email = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $newUsername, $newEmail, $username);
        }

        // Executa a query de atualização
        if ($stmt->execute()) {
            echo "Utilizador atualizado com sucesso.";
        } else {
            echo "Erro ao atualizar utilizador: " . $stmt->error;
        }

        // Fecha a ligação à base de dados
        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}

// Obtém os dados atuais do utilizador para preencher o formulário
$sql = "SELECT username, email FROM utilizadores WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($currentUsername, $currentEmail);
$stmt->fetch();
$stmt->close();

// Fecha a ligação à base de dados
$conn->close();
?>

<h2>Atualizar Utilizador</h2>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?username=' . $username); ?>">
    <label for="username">Nome de Utilizador:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentUsername); ?>"><br><br>

    <label for="password">Nova Palavra-passe:</label>
    <input type="password" id="password" name="password"><br><br>

    <label for="email">Novo Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentEmail); ?>"><br><br>

    <input type="submit" value="Atualizar">
</form>

<br>
<a href="listagem.php">Voltar para a Lista de Utilizadores</a>
