<?php
// GET: descarrega todas as tarefas do utilizador num ficheiro JSON
require __DIR__ . "/config.php";

$utilizadorId = exigirLogin();
$tarefas = (new TarefaDAO())->listar($utilizadorId);

// Tira o id: assim o ficheiro pode ser importado em qualquer conta
foreach ($tarefas as $i => $tarefa) {
  unset($tarefas[$i]["id"]);
}

// "attachment" faz o browser descarregar o ficheiro em vez de o mostrar
header("Content-Type: application/json; charset=utf-8");
header('Content-Disposition: attachment; filename="tarefas.json"');
echo json_encode($tarefas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
