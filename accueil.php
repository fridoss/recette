<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passionné de Cuisine - Accueil</title>
 
    <style>
        body {
            background-image: url('cuisine.jpeg'); 
            background-size: cover;
            background-position: center;
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            font-size: 2.5em;
            color: #e44d26; 
            margin-bottom: 20px;
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
            font-size: 1.2em;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #e44d26;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur Passionné de Cuisine</h1>
        <nav>
            <ul>
                <li><a href="accueil.php">Accueil</a></li>
                <li><a href="cuisine_salee.php">Cuisine Salée</a></li>
                <li><a href="cuisine_sucree.php">Cuisine Sucrée</a></li>
                <li><a href="cuisine_vegetarienne.php">Cuisine Végétarienne</a></li>
            </ul>
        </nav>
        <p>Découvrez de délicieuses recettes pour tous les goûts !</p>
    </div>
</body>
</html>