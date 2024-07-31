<?php
session_start();
require_once './_db/dbconnect.php';
if (!isset($_SESSION['user_ID'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8');
    $mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $code_postal = filter_input(INPUT_POST, 'code_postal', FILTER_SANITIZE_NUMBER_INT);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse e-mail invalide";
    } elseif (!preg_match("/^[0-9]{5}$/", $code_postal)) {
        $error = "Code postal invalide";
    } else {
        $sql = "INSERT INTO user (nom, prenom, mail, code_postal, password) VALUES (:nom, :prenom, :mail, :code_postal, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':password', $password);
        if ($stmt->execute()) {
            header("Location: userhandling.php");
            exit();
        } else {
            $error = "Erreur lors de l'ajout de l'utilisateur";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/adduser.css">
    <link rel="icon" href="./assets/imgs/psn.svg">
    <title>Ajouter un Utilisateur</title>
</head>
<body>
    <header> <h1>Ajouter un Utilisateur</h1>
    <?php include 'nav.php'; ?></header>
    <main>
    <form method="POST" autocomplete="off" action="">
        <label for="nom">Nom</label>
        <input type="text" name="nom" required>
        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" required>
        <label for="email">Email</label>
        <input type="email" name="email" required>
        <label for="code_postal">Code Postal</label>
        <input type="text" name="code_postal" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" required>
        <button type="submit" name="add_user">Ajouter</button>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </main>
   <footer>
   <p>Footer_Copyright - 2024 - Eva_Margot</p>
   </footer>
   <img id="darkModeToggle" src="./assets/imgs/dark.svg" alt="Dark Mode Toggle">
   <script src="./assets/js/darkmode.js"></script>
</body>
</html>