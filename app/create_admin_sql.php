<?php
// create_admin_sql.php

// Paramètres de connexion à ta base MySQL (ajuste si nécessaire)
$host = 'db';           // Nom du service MySQL dans Docker (ou localhost si hors Docker)
$db   = 'symfony';      // Nom de ta base
$user = 'root';          // Utilisateur MySQL
$pass = 'root';  // Mot de passe MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (\PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}

// Infos de l’admin
$adminEmail = 'admin@gmail.com'; // On peut changer l'email ici
$adminPassword = 'Admin'; // On peut changer le mot de passe ici
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT); 
$roles = '["ROLE_ADMIN"]';

try {
    $stmt = $pdo->prepare("INSERT INTO user (email, roles, password) VALUES (:email, :roles, :password)");
    $stmt->execute([
        ':email' => $adminEmail,
        ':roles' => $roles,
        ':password' => $hashedPassword
    ]);
    echo "Compte admin créé avec succès : $adminEmail / $adminPassword\n";
} catch (\PDOException $e) {
    die("Erreur insertion admin : " . $e->getMessage());
}