<?php
session_start();
require_once './_db/dbconnect.php';
$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8') : '';
    $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8') : '';
    $mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $code_postal = filter_input(INPUT_POST, 'code_postal', FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse e-mail invalide";
    } elseif (!preg_match("/^[0-9]{5}$/", $code_postal)) {
        $error = "Le code postal doit être composé de 5 chiffres.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (nom, prenom, mail, password, code_postal) VALUES (:nom, :prenom, :mail, :password, :code_postal)";
        try {
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête : " . $conn->errorInfo()[2]);
            }
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':prenom', $prenom);
            $stmt->bindValue(':mail', $mail);
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':code_postal', $code_postal);
            if ($stmt->execute()) {
                $success = "Inscription réussie.";
            } else {
                throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->errorInfo()[2]);
            }
        } catch (Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/imgs/psn.svg">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Connexion / Inscription</title>
</head>

<body>
    <header>
        <h1>Bon titre</h1>
    </header>
    <main>
        <?php
        if ($error) {
            echo "<p style='color: red;'>$error</p>";
        }
        if ($success) {
            echo "<p style='color: white;'>$success</p>";
        }
        ?>
        <div id="mainDiv">
            <div id="tinyDivOne">
                <h2>Connexion</h2>
                <form method="POST" autocomplete="off" action="./_db/login.php">
                    <label for="loginEmail">Email</label>
                    <input type="email" name="email" id="loginEmail" placeholder="Email" required>
                    <label for="loginPassword">Mot de passe</label>
                    <input type="password" name="password" id="loginPassword" placeholder="Mot de passe" required>
                    <button type="submit" name="login">Se connecter</button>
                </form>
            </div>
            <div id="tinyDivTwo">
                <h2>Inscription</h2>
                <form method="POST" autocomplete="off" action="" onsubmit="return validatePostalCode()">
                    <label for="registernom">Nom</label>
                    <input type="text" name="nom" id="registernom" placeholder="Nom" required>
                    <label for="registerprenom">Prénom</label>
                    <input type="text" name="prenom" id="registerprenom" placeholder="Prénom" required>
                    <label for="registerEmail">Email</label>
                    <input type="email" name="email" id="registerEmail" placeholder="Email" required>
                    <label for="registercode_postal">Code Postal</label>
                    <input type="text" name="code_postal" id="registercode_postal" placeholder="Code Postal" required onblur="validatePostalCode()">
                    <span id="postalCodeError" style="color: red;"></span>
                    <label for="registerPassword">Mot de passe</label>
                    <input type="password" name="password" id="registerPassword" placeholder="Mot de passe" required>
                    <button type="submit" name="register">S'inscrire</button>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <p>Footer_Copyright - 2024 - Eva_Margot</p>
    </footer>
    <img id="darkModeToggle" src="./assets/imgs/dark.svg" alt="Dark Mode Toggle">
    <script src="./assets/js/darkmode.js"></script>
    <script>
        function validatePostalCode() {
            var postalCode = document.getElementById('registercode_postal').value;
            var postalCodeError = document.getElementById('postalCodeError');
            if (!/^[0-9]{5}$/.test(postalCode)) {
                postalCodeError.textContent = "Le code postal doit être composé de 5 chiffres.";
                return false;
            } else {
                postalCodeError.textContent = "";
                return true;
            }
        }
    </script>
</body>

</html>