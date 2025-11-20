<?php
session_start();
$host = 'localhost';
$db = 'jo'; 
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erreur de connexion: ' . $conn->connect_error);
}

$message_connexion = '';
$message_inscription = '';
$message_suppression = '';
$message_ajout = '';

if (isset($_POST['delete_account']) && isset($_SESSION['username'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $sql = "DELETE FROM user WHERE username = '$username'";
    if ($conn->query($sql)) {
        $message_suppression = "Compte supprimé avec succès.";
        unset($_SESSION['username']);
    } else {
        $message_suppression = "Erreur lors de la suppression du compte.";
    }
}

if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username === '') {
        $message_connexion = "Le champ username de la connexion est vide.";
    } elseif ($password === '') {
        $message_connexion = "Le champ password de la connexion est vide.";
    } else {
        $username = $conn->real_escape_string($username);
        $sql = "SELECT password FROM user WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $message_connexion = 'Connexion réussie !';
                $_SESSION['username'] = $username;
            } else {
                $message_connexion = 'Le mot de passe est invalide.';
            }
        } else {
            $message_connexion = "Le username n’existe pas dans la base de données.";
        }
    }
}

if (isset($_POST['register'])) {
    $username = isset($_POST['reg_username']) ? trim($_POST['reg_username']) : '';
    $password = isset($_POST['reg_password']) ? $_POST['reg_password'] : '';
    $name = isset($_POST['reg_name']) ? trim($_POST['reg_name']) : '';

    if ($username === '') {
        $message_inscription = "Le champ username de l’inscription est vide.";
    } elseif ($password === '') {
        $message_inscription = "Le champ password de l’inscription est vide.";
    } elseif ($name === '') {
        $message_inscription = "Le champ nom de l’inscription est vide.";
    } else {
        $username = $conn->real_escape_string($username);
        $name = $conn->real_escape_string($name);
        $sql = "SELECT id FROM user WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $message_inscription = "Ce username est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user (username, password, name) VALUES ('$username', '$hash', '$name')";
            if ($conn->query($sql)) {
                $message_inscription = "Inscription réussie !";
            } else {
                $message_inscription = "Erreur lors de l'inscription.";
            }
        }
    }
}

// Ajout d'un message pour utilisateur connecté
if (isset($_POST['add_message']) && isset($_SESSION['username'])) {
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    if ($content === '') {
        $message_ajout = "Le message ne peut pas être vide.";
    } else {
        $username = $conn->real_escape_string($_SESSION['username']);
        $sql = "SELECT id, name FROM user WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows === 1) {
            $user_row = $result->fetch_assoc();
            $user_id = $user_row['id'];
            $user_name = $user_row['name'];
            $content = $conn->real_escape_string($content);
            $sql = "INSERT INTO message (user_id, content) VALUES ($user_id, '$content')";
            if ($conn->query($sql)) {
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $message_ajout = "Erreur lors de l'ajout du message.";
            }
        } else {
            $message_ajout = "Utilisateur non trouvé.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="9.css" >
    <meta charset="UTF-8">
    <title>Connexion & Inscription</title>
</head>
<body>
    <h2>Connexion</h2>
    <form method="post">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" id="username"><br>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password"><br>
        <button type="submit" name="login">Se connecter</button>
    </form>
    <p style="color:red;"><?= htmlspecialchars($message_connexion) ?></p>

    <h2>Messages existants</h2>
    <div style="margin-bottom:20px;">
    <?php
    $sql = "SELECT m.content, u.name FROM message m JOIN user u ON m.user_id = u.id ORDER BY m.created_at ASC";
    $result = $conn->query($sql);
    if (!$result) {
        echo '<p style="color:red;">Erreur SQL : ' . $conn->error . '</p>';
    } elseif ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div style="border-bottom:1px solid #ccc; margin-bottom:8px; padding-bottom:4px;">';
            echo '<span style="color:#0077cc;font-weight:bold;">' . htmlspecialchars($row['name']) . '</span><br>';
            echo '<span>' . htmlspecialchars($row['content']) . '</span>';
            echo '</div>';
        }
    } else {
        echo '<p>Aucun message.</p>';
    }
    ?>
    </div>

    <?php if (isset($_SESSION['username'])) : ?>
        <form method="post">
            <h4>Supprimer votre compte</h4>
            <button type="submit" name="delete_account">Supprimer le compte</button>
        </form>
        <p style="color:green;"><?= htmlspecialchars($message_suppression) ?></p>

        <h2>Ajouter un message</h2>
        <form method="post">
            <textarea name="content" rows="4" cols="50" placeholder="Votre message..."></textarea><br>
            <button type="submit" name="add_message">Envoyer</button>
        </form>
        <p style="color:red;"><?= htmlspecialchars($message_ajout) ?></p>
    <?php endif; ?>

    <h2>Inscription</h2>
    <form method="post">
        <label for="reg_username">Nom d'utilisateur :</label>
        <input type="text" name="reg_username" id="reg_username"><br>
        <label for="reg_password">Mot de passe :</label>
        <input type="password" name="reg_password" id="reg_password"><br>
        <label for="reg_name">Nom :</label>
        <input type="text" name="reg_name" id="reg_name"><br>
        <button type="submit" name="register">S'inscrire</button>
    </form>
    <p style="color:red;><?= htmlspecialchars($message_inscription) ?></p>
</body>
</html>