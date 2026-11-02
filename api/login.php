<?php
// POST { email, password }: inicia a sessão
require __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  responder(405, ["erro" => "erro.metodo"]);
}
exigirCsrf();

$dados = lerJson();
$email = trim((string) ($dados["email"] ?? ""));
$password = (string) ($dados["password"] ?? "");

$utilizador = (new UtilizadorDAO())->procurarPorEmail($email);

// de propósito a mesma mensagem se o email ou a password estiverem errados, para não dar a perceber que emails é que têm conta
if ($utilizador === null || !$utilizador->verificarPassword($password)) {
  responder(401, ["erro" => "erro.login"]);
}

// Novo id de sessão ao entrar (protege contra fixação de sessão)
session_regenerate_id(true);
$_SESSION["utilizador_id"] = $utilizador->getId();
$_SESSION["utilizador_nome"] = $utilizador->getNome();

responder(200, ["nome" => $utilizador->getNome()]);
