<?php
// Test d'upload dans le dossier couvertures

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $uploadDir = __DIR__ . '/uploads/couvertures/';
        
        // Vérifier si le dossier existe, sinon le créer
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Générer un nom de fichier unique
        $filename = uniqid() . '_' . $_FILES['file']['name'];
        $destination = $uploadDir . $filename;
        
        // Déplacer le fichier téléchargé
        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            echo "Le fichier a été téléchargé avec succès : " . $filename;
        } else {
            echo "Erreur lors du déplacement du fichier : " . error_get_last()['message'];
        }
    } else {
        echo "Erreur de téléchargement du fichier : " . $_FILES['file']['error'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test d'upload</title>
</head>
<body>
    <h1>Test d'upload de fichier</h1>
    <p>Utilisez ce formulaire pour tester l'upload d'images</p>
    
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept="image/*">
        <button type="submit">Télécharger</button>
    </form>
    
    <h2>Informations PHP</h2>
    <ul>
        <li>upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
        <li>post_max_size: <?php echo ini_get('post_max_size'); ?></li>
        <li>Permissions sur le dossier uploads/couvertures: <?php echo substr(sprintf('%o', fileperms(__DIR__ . '/uploads/couvertures')), -4); ?></li>
    </ul>
</body>
</html> 