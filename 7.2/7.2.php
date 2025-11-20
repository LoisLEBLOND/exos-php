<?php
session_start();
if (!isset($_SESSION["username"])){
    $_SESSION["username"] = $_POST["username"];
}

echo "<h2>Bonjour ". $_POST["username"] . "</h2>";
?>

<form method="post" action="7.2.html">
    <h4>Supprimer votre compte<h4>
    <input type="submit" class="submit_btn">
    <?php unset($_SESSION["username"]) ?>
</form>