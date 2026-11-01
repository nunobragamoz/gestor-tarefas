<?php
// GET: diz quem está autenticado (útil para testar a API no browser)
require __DIR__ . "/config.php";

responder(200, ["nome" => $_SESSION["utilizador_nome"] ?? null]);
