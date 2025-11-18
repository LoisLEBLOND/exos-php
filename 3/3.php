<?php
$mots = ["Alice Dupont", "John Doe", "Jean Martin", "Richard Waterson", "Jotaro Kujo"];
$chemin = "contact.txt";
$contenu = file_get_contents($chemin);

$fichier = fopen($chemin, "a");
foreach ($mots as $mot) {
    if (strpos($contenu, $mot) === false) {
        fwrite($fichier, $mot . "<br>");
    }
}
fclose($fichier);

$contenu = file_get_contents($chemin);
echo $contenu;