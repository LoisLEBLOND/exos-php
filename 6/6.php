<?php


function triValide($tri, $trisAutorises) {
	return in_array($tri, $trisAutorises) ? $tri : $trisAutorises[0];
}

function ordreValide($ordre) {
	return ($ordre === 'asc' || $ordre === 'desc') ? $ordre : 'asc';
}

function fleche($colonneActuelle, $tri, $ordre) {
	if ($colonneActuelle !== $tri) return '';
	$couleur = 'red';
	return $ordre === 'asc' ? "<span style='color:$couleur'>↑</span>" : "<span style='color:$couleur'>↓</span>";
}

$trisAutorises = ['nom', 'pays', 'course', 'temps'];
$tri = isset($_GET['sort']) ? triValide($_GET['sort'], $trisAutorises) : $trisAutorises[0];
$ordre = isset($_GET['order']) ? ordreValide($_GET['order']) : 'asc';

try {
	$bdd = new PDO("mysql:host=localhost;dbname=jo;charset=utf8", "root", "");
} catch(PDOException $e) {
	die($e->getMessage());
}

$requete = "SELECT * FROM jo.`100` ORDER BY $tri " . ($ordre === 'asc' ? 'ASC' : 'DESC');
$stmt = $bdd->prepare($requete);
$stmt->execute();
$donnees = $stmt->fetchAll();

function lien_th($etiquette, $colonne, $tri, $ordre) {
	$ordreSuivant = ($tri === $colonne && $ordre === 'asc') ? 'desc' : 'asc';
	$fleche = fleche($colonne, $tri, $ordre);
	$url = "?sort=$colonne&order=$ordreSuivant";
	return "<a href='$url'>$etiquette $fleche</a>";
}

?>
<table>
	<thead>
		<tr>
			<th><?= lien_th('Nom', 'nom', $tri, $ordre) ?></th>
			<th><?= lien_th('Pays', 'pays', $tri, $ordre) ?></th>
			<th><?= lien_th('Course', 'course', $tri, $ordre) ?></th>
			<th><?= lien_th('Temps', 'temps', $tri, $ordre) ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($donnees as $valeur) { ?>
		<tr>
			<td><?= htmlspecialchars($valeur["nom"]) ?></td>
			<td><?= htmlspecialchars($valeur["pays"]) ?></td>
			<td><?= htmlspecialchars($valeur["course"]) ?></td>
			<td><?= htmlspecialchars($valeur["temps"]) ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>