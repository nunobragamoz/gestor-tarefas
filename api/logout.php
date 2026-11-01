<?php
// POST: termina a sessão
require __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  responder(405, ["erro" => "erro.metodo"]);
}
exigirCsrf();

$_SESSION = [];
session_destroy();
responder(200, ["ok" => true]);
