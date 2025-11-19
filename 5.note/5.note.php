<?php

try {
	$bdd = new PDO("mysql:host=localhost;dbname=jo;charset=utf8", "root", "");
} catch(PDOException $e) {
	die($e->getMessage());
}

$courses = $bdd->query("SELECT DISTINCT course FROM jo.`100`")->fetchAll(PDO::FETCH_COLUMN);

$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nom = trim($_POST['nom'] ?? '');
	$pays = strtoupper(trim($_POST['pays'] ?? ''));
	$course = $_POST['course'] ?? '';
	$temps = $_POST['temps'] ?? '';

	if (strlen($pays) !== 3) {
		$erreurs[] = "Le pays doit être en 3 lettres.";
	}
	if (!is_numeric($temps)) {
		$erreurs[] = "Le temps doit être un nombre.";
	}
	if (!in_array($course, $courses)) {
		$erreurs[] = "Course invalide.";
	}
	if (!$erreurs) {
		$sth = $bdd->prepare('INSERT INTO jo.`100` (`nom`, `pays`, `course`, `temps`) VALUES (:nom, :pays, :course, :temps)');
		$sth->execute([
			'nom' => $nom,
			'pays' => $pays,
			'course' => $course,
			'temps' => $temps
		]);
	}
}

$recherche = trim($_GET['recherche'] ?? '');
$where = '';
if ($recherche) {
	$where = "WHERE nom LIKE :recherche OR pays LIKE :recherche OR course LIKE :recherche";
}

$page = max(1, (int)($_GET['page'] ?? 1));
$limite = 10;
$debut = ($page - 1) * $limite;

$trisAutorises = ['nom', 'pays', 'course', 'temps'];
$tri = isset($_GET['sort']) && in_array($_GET['sort'], $trisAutorises) ? $_GET['sort'] : $trisAutorises[0];
$ordre = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

$sql = "SELECT * FROM jo.`100` $where ORDER BY $tri $ordre LIMIT :debut, :limite";
$stmt = $bdd->prepare($sql);
if ($recherche) {
	$stmt->bindValue(':recherche', "%$recherche%", PDO::PARAM_STR);
}
$stmt->bindValue(':debut', $debut, PDO::PARAM_INT);
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
$donnees = $stmt->fetchAll();

$sqlCount = "SELECT COUNT(*) FROM jo.`100` $where";
$stmtCount = $bdd->prepare($sqlCount);
if ($recherche) {
	$stmtCount->bindValue(':recherche', "%$recherche%", PDO::PARAM_STR);
}
$stmtCount->execute();
$total = $stmtCount->fetchColumn();
$nbPages = ceil($total / $limite);

function classement($bdd, $course) {
	$sql = "SELECT nom, temps FROM jo.`100` WHERE course = :course ORDER BY temps ASC";
	$stmt = $bdd->prepare($sql);
	$stmt->execute(['course' => $course]);
	$resultats = $stmt->fetchAll();
	$classement = [];
	foreach ($resultats as $i => $row) {
		$classement[$row['nom']] = $i + 1;
	}
	return $classement;
}

?>
<form method="post">
	<label>Nom: <input type="text" name="nom" required></label>
	<label>Pays (3 lettres): <input type="text" name="pays" maxlength="3" required></label>
	<label>Course:
		<select name="course" required>
			<?php foreach ($courses as $c) { echo "<option value='".htmlspecialchars($c)."'>".htmlspecialchars($c)."</option>"; } ?>
		</select>
	</label>
	<label>Temps: <input type="number" name="temps" required></label>
	<button type="submit">Ajouter</button>
</form>

<?php if ($erreurs) { echo '<ul style="color:red">'; foreach ($erreurs as $e) echo "<li>$e</li>"; echo '</ul>'; } ?>

<form method="get">
	<input type="text" name="recherche" value="<?= htmlspecialchars($recherche) ?>" placeholder="Recherche...">
	<button type="submit">Rechercher</button>
</form>

<table>
	<thead>
		<tr>
			<th><a href="?sort=nom&order=<?= $ordre === 'asc' ? 'desc' : 'asc' ?>">Nom</a></th>
			<th><a href="?sort=pays&order=<?= $ordre === 'asc' ? 'desc' : 'asc' ?>">Pays</a></th>
			<th><a href="?sort=course&order=<?= $ordre === 'asc' ? 'desc' : 'asc' ?>">Course</a></th>
			<th><a href="?sort=temps&order=<?= $ordre === 'asc' ? 'desc' : 'asc' ?>">Temps</a></th>
			<th>Classement</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($donnees as $valeur) {
		$classementCourse = classement($bdd, $valeur['course']);
		$rang = $classementCourse[$valeur['nom']] ?? '';
	?>
		<tr>
			<td><?= htmlspecialchars($valeur["nom"]) ?></td>
			<td><?= htmlspecialchars($valeur["pays"]) ?></td>
			<td><?= htmlspecialchars($valeur["course"]) ?></td>
			<td><?= htmlspecialchars($valeur["temps"]) ?></td>
			<td><?= $rang ?></td>
			<td><a href="edit.php?id=<?= $valeur['id'] ?>">Modifier</a></td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<div>
	<?php for ($i = 1; $i <= $nbPages; $i++) {
		$params = $_GET;
		$params['page'] = $i;
		$url = '?' . http_build_query($params);
		echo "<a href='$url'" . ($i == $page ? " style='font-weight:bold'" : "") . ">$i</a> ";
	} ?>
</div>
