<?php
/**
 * Welcome Page - Displays PHP version information
 */

// Configuration
define('GENERATOR_PATH', '3D-Chromschrift-Generator/gene.php');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        .php-version {
            font-size: 18px;
            color: #666;
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border-left: 4px solid #4CAF50;
        }
        .links {
            margin-top: 30px;
        }
        .links a {
            display: inline-block;
            margin-right: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .links a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Willkommen auf meiner Seite</h1>
        <div class="php-version">
            <strong>Aktuelle PHP-Version auf diesem Server:</strong> <?php echo htmlspecialchars(phpversion(), ENT_QUOTES, 'UTF-8'); ?>
        </div>
        
        <div class="links">
            <a href="<?php echo htmlspecialchars(GENERATOR_PATH, ENT_QUOTES, 'UTF-8'); ?>">3D Chrome Text Generator</a>
        </div>
    </div>
</body>
</html>
