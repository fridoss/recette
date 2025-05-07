<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "site_recette";

try {
    $connexion = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inscription'])) {
    $nom_inscription = $_POST['nom_inscription'];
    $prenom_inscription = $_POST['prenom_inscription'];
    $email_inscription = $_POST['email_inscription'];
    $mot_de_passe_inscription = password_hash($_POST['mot_de_passe_inscription'], PASSWORD_DEFAULT);
    $photo_profil_tmp = $_FILES['photo_profil']['tmp_name'];
    $photo_profil_nom = $_FILES['photo_profil']['name'];
    $photo_profil_destination = 'uploads/' . $photo_profil_nom;

    move_uploaded_file($photo_profil_tmp, $photo_profil_destination);

    try {
        $requete_inscription = $connexion->prepare("INSERT INTO connexion (nom, prenom, email, mot_de_passe, photo_profil) VALUES (:nom, :prenom, :email, :mot_de_passe, :photo_profil)");
        $requete_inscription->bindParam(':nom', $nom_inscription);
        $requete_inscription->bindParam(':prenom', $prenom_inscription);
        $requete_inscription->bindParam(':email', $email_inscription);
        $requete_inscription->bindParam(':mot_de_passe', $mot_de_passe_inscription);
        $requete_inscription->bindParam(':photo_profil', $photo_profil_destination);
        $requete_inscription->execute();

        header("Location: accueil.php");
        exit();

    } catch (PDOException $e) {
        echo "Erreur lors de l'inscription : " . $e->getMessage();
    }
}

session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['connexion'])) {
    $nom_connexion = $_POST['nom_connexion'];
    $mot_de_passe_connexion = $_POST['mot_de_passe_connexion'];

    try {
        
        $requete_connexion = $connexion->prepare("SELECT id, nom, prenom, mot_de_passe FROM connexion WHERE nom = :nom");
        $requete_connexion->bindParam(':nom', $nom_connexion);
        $requete_connexion->execute();
        $utilisateur = $requete_connexion->fetch(PDO::FETCH_ASSOC);

        
        if ($utilisateur && password_verify($mot_de_passe_connexion, $utilisateur['mot_de_passe'])) {
            

         
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['utilisateur_nom'] = $utilisateur['nom'];
            $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];

         
            header("Location: accueil.php");
            exit();
        } else {
        
            $erreur_connexion = "Nom ou mot de passe incorrect.";
        }

    } catch (PDOException $e) {
        echo "Erreur lors de la connexion : " . $e->getMessage();
    }
}



if (isset($erreur_connexion)) {
    echo "<p style='color: red; text-align: center;'>$erreur_connexion</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passionné de Cuisine - Inscription/Connexion</title>
    <style>
        body {
            background-image: url('cuisine.jpeg');
            background-size: cover;
            background-position: center;
            font-family: sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            gap: 20px;
        }

        .connexion-block {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
			height:300px;
        }

        .connexion-block h2 {
            margin-bottom: 15px;
        }

        .connexion-block .form-group {
            margin-bottom: 10px;
            text-align: left;
        }

        .connexion-block label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .connexion-block input[type="text"],
        .connexion-block input[type="password"] {
            width: calc(100% - 12px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .connexion-block button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .connexion-block button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .inscription-form {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }

        .inscription-form h1 {
            margin-bottom: 20px;
        }

        .inscription-form .personal-info {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .inscription-form .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .inscription-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .inscription-form input[type="text"],
        .inscription-form input[type="email"],
        .inscription-form input[type="password"],
        .inscription-form input[type="file"] {
            width: calc(100% - 12px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .inscription-form button[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .inscription-form button[type="submit"]:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="connexion-block">
            <h2>Se connecter</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                <div class="form-group">
                    <label for="nom_connexion">Nom :</label>
                    <input type="text" id="nom_connexion" name="nom_connexion" required placeholder="Votre nom">
                </div>
                <div class="form-group">
                    <label for="prenom_connexion">Prénom :</label>
                    <input type="text" id="prenom_connexion" name="prenom_connexion" required placeholder="Votre prénom">
                </div>
                <div class="form-group">
                    <label for="mot_de_passe_connexion">Mot de passe :</label>
                    <input type="password" id="mot_de_passe_connexion" name="mot_de_passe_connexion" required placeholder="Votre mot de passe">
                </div>
                <button type="submit" name="connexion">Se connecter</button>
            </form>
        </div>
        <div class="inscription-form">
            <h1>Rejoignez Passionné de Cuisine !</h1>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
                <div class="personal-info">
                    <h2>Informations Personnelles</h2>
                    <div class="form-group">
                        <label for="nom_inscription">Nom :</label>
                        <input type="text" id="nom_inscription" name="nom_inscription" required placeholder="Votre nom">
                    </div>
                    <div class="form-group">
                        <label for="prenom_inscription">Prénom :</label>
                        <input type="text" id="prenom_inscription" name="prenom_inscription" required placeholder="Votre prénom">
                    </div>
                    <div class="form-group">
                        <label for="email_inscription">Email :</label>
                        <input type="email" id="email_inscription" name="email_inscription" required placeholder="Votre email">
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe_inscription">Mot de passe :</label>
                        <input type="password" id="mot_de_passe_inscription" name="mot_de_passe_inscription" required placeholder="Votre mot de passe">
                    </div>
                    <div class="form-group">
                        <label for="photo_profil">Photo de Profil :</label>
                        <input type="file" id="photo_profil" name="photo_profil" accept="image/*">
                    </div>
                </div>
                <button type="submit" name="inscription">S'inscrire</button>
            </form>
        </div>
    </div>
</body>
</html>