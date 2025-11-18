
<?php
$fichier = "table.txt";
$lignes = [];
if (file_exists($fichier)) {
	$lignes = file($fichier);
}

$erreurs = [];
for ($i = 1; $i <= 10; $i++) {
	if (!isset($lignes[$i])) continue;
	$valeurs = explode(" ", trim($lignes[$i]));
	for ($j = 1; $j <= 10; $j++) {
		if (!isset($valeurs[$j])) continue;
		$produit = $i * $j;
		if ((int)$valeurs[$j] !== $produit) {
			$erreurs[] = $i . "x" . $j;
		}
	}
}

if (count($erreurs) > 0) {
	echo "Les erreurs sont " . implode(", ", $erreurs);
} else {
	echo "Aucune erreur trouvée.";
}

