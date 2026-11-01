<?php
// POST (ficheiro "ficheiro"): importa tarefas de um ficheiro JSON
require __DIR__ . "/config.php";

$utilizadorId = exigirLogin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  responder(405, ["erro" => "erro.metodo"]);
}
exigirCsrf();

// O ficheiro tem de ter chegado bem e ter no máximo 1 MB
$ficheiro = $_FILES["ficheiro"] ?? null;
if (!$ficheiro || $ficheiro["error"] !== UPLOAD_ERR_OK || $ficheiro["size"] > 1000000) {
  responder(400, ["erro" => "erro.ficheiro"]);
}

// Tem de ser uma lista JSON: [ {...}, {...} ]
$lista = json_decode(file_get_contents($ficheiro["tmp_name"]), true);
if (!is_array($lista) || !array_is_list($lista)) {
  responder(400, ["erro" => "erro.formato"]);
}

// Cada tarefa passa pela mesma validação do formulário
$tarefas = [];
foreach ($lista as $item) {
  if (!is_array($item)) {
    responder(400, ["erro" => "erro.formato"]);
  }
  unset($item["id"]); // são sempre tarefas novas do utilizador autenticado
  $tarefa = Tarefa::deArray($item, $utilizadorId);
  if ($tarefa->validar()) {
    responder(400, ["erro" => "erro.formato"]);
  }
  $tarefas[] = $tarefa;
}

$total = (new TarefaDAO())->importar($tarefas);
responder(200, ["total" => $total]);
