<?php

if (!isset($_SESSION["username"])){
    $_SESSION["username"] = $_POST["username"];
}

echo "<h2>Bonjour ". $_POST["username"] . "</h2>";