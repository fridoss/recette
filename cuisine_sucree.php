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

try {
    $requete_recettes = $connexion->prepare("SELECT * FROM recettes WHERE type_cuisine = 'sucrée'");
    $requete_recettes->execute();
    $recettes_sucrees = $requete_recettes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des recettes sucrées : " . $e->getMessage();
    $recettes_sucrees = [];
}


$commentaires_par_recette = [];
foreach ($recettes_sucrees as $recette) {
    try {
        $requete_commentaires = $connexion->prepare("SELECT * FROM commentaires WHERE recette_id = :recette_id");
        $requete_commentaires->bindParam(':recette_id', $recette['id']);
        $requete_commentaires->execute();
        $commentaires_par_recette[$recette['id']] = $requete_commentaires->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des commentaires pour la recette " . $recette['nom_recette'] . " : " . $e->getMessage();
        $commentaires_par_recette[$recette['id']] = [];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recette_id']) && isset($_POST['commentaire']) && isset($_POST['type_cuisine'])) {
    $recette_id_commentaire = $_POST['recette_id'];
    $nouveau_commentaire = trim($_POST['commentaire']);
    $type_cuisine_commentaire = $_POST['type_cuisine']; 

    if (!empty($nouveau_commentaire)) {
        try {
            $requete_ajout_commentaire = $connexion->prepare("INSERT INTO commentaires (recette_id, contenu, date_creation, type_cuisine) VALUES (:recette_id, :contenu, NOW(), :type_cuisine)");
            $requete_ajout_commentaire->bindParam(':recette_id', $recette_id_commentaire);
            $requete_ajout_commentaire->bindParam(':contenu', $nouveau_commentaire);
            $requete_ajout_commentaire->bindParam(':type_cuisine', $type_cuisine_commentaire); 
            $requete_ajout_commentaire->execute();
            
            header("Location: " . $_SERVER['PHP_SELF']); 
            exit();
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout du commentaire : " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passionné de Cuisine - Cuisine Sucrée</title>

    <style>
        body {
            background-image: url('imgsucree.jpeg'); 
            background-size: cover;
            background-position: center;
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 960px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 1.1em;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #e44d26; 
        }

        #selection-recette {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .recette-block {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
            display: flex;
            align-items: flex-start;

        }

        .recette-details {
            flex-grow: 1;
        }

        .recette-details h3 {
            margin-top: 0;
            color: #e44d26; 
        }

        .recette-details p {
            margin-bottom: 10px;
        }

        .recette-block img {
            width: 200px;
            height: auto;
            border-radius: 5px;

        }

        .commentaires-section {
            margin-top: 15px;
            padding: 10px;
            border-top: 1px solid #ddd;
            color: #777;
        }

        .commentaire-list {
            list-style: none;
            padding: 0;
        }

        .commentaire-list li {
            margin-bottom: 8px;
            padding: 8px;
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .commentaire-form textarea {
            width: 100%;
            min-height: 80px;
            margin-top: 5px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: sans-serif;
            font-size: 1em;
        }

        .commentaire-form button {
            margin-top: 5px;
            padding: 8px 15px;
            background-color: #e44d26; 
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        .commentaire-form button:hover {
            background-color: #c83b1a; 
        }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li><a href="accueil.php">Accueil</a></li>
                <li><a href="cuisine_salee.php">Cuisine Salée</a></li>
                <li><a href="cuisine_sucree.php">Cuisine Sucrée</a></li>
                <li><a href="cuisine_vegetarienne.php">Cuisine Végétarienne</a></li>

            </ul>
        </nav>

        <h2>Découvrez nos Délicieuses Recettes Sucrées</h2>

        <select id="selection-recette" onchange="afficherRecette(this.value)">

            <?php foreach ($recettes_sucrees as $recette): ?>

                <option value="<?php echo str_replace(' ', '_', strtolower($recette['nom_recette'])); ?>" data-recette-id="<?php echo $recette['id']; ?>">
                    <?php echo $recette['nom_recette']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div id="recettes-container">
            <?php foreach ($recettes_sucrees as $recette): ?>
                <div class="recette-block" id="<?php echo str_replace(' ', '_', strtolower($recette['nom_recette'])); ?>" style="display:none;">
                    <div class="recette-details">
                        <h3><?php echo $recette['nom_recette']; ?></h3>
                        <p><strong>Ingrédients :</strong> <?php echo nl2br($recette['ingredients']); ?></p>
                        <p><strong>Étapes de cuisson :</strong> <?php echo nl2br($recette['etapes']); ?></p>

                        <div class="commentaires-section">
                            <h4>Commentaires :</h4>
                            <?php if (!empty($commentaires_par_recette[$recette['id']])): ?>
                                <ul class="commentaire-list">
                                    <?php foreach ($commentaires_par_recette[$recette['id']] as $commentaire): ?>
                                        <li><?php echo htmlspecialchars($commentaire['contenu']); ?> (Le <?php echo date('d/m/Y à H:i', strtotime($commentaire['date_creation'])); ?>)</li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Aucun commentaire pour cette recette.</p>
                            <?php endif; ?>

                            <div class="commentaire-form">
                                <h5>Laisser un commentaire :</h5>
                                <form method="post" action="">
                                    <textarea name="commentaire" placeholder="Votre commentaire ici..."></textarea>
                                    <input type="hidden" name="recette_id" value="<?php echo $recette['id']; ?>">
									<input type="hidden" name="type_cuisine" value="sucrée">
                                    <button type="submit">Envoyer</button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <img src="<?php echo $recette['image_url']; ?>" alt="<?php echo $recette['nom_recette']; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function afficherRecette(recetteId) {
            const recettes = document.querySelectorAll('.recette-block');
            recettes.forEach(recette => {
                recette.style.display = 'none';
            });

            if (recetteId) {
                const recetteSelectionnee = document.getElementById(recetteId);
                if (recetteSelectionnee) {
                    recetteSelectionnee.style.display = 'flex';
                }
            }
        }

        
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('selection-recette');
            if (selectElement.options.length > 0) {
                afficherRecette(selectElement.options[0].value);
            }
        });
    </script>
</body>
</html>