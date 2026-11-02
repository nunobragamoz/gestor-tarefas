<?php
// API REST das tarefas:
//   GET    tarefas.php      → lista as tarefas
//   POST   tarefas.php      → cria uma tarefa
//   PUT    tarefas.php?id=3 → atualiza a tarefa 3
//   DELETE tarefas.php?id=3 → elimina a tarefa 3
require __DIR__ . "/config.php";

$utilizadorId = exigirLogin();
$metodo = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;
$dao = new TarefaDAO();

// Só os pedidos que alteram dados precisam do token CSRF
if ($metodo !== "GET") {
  exigirCsrf();
}

if ($metodo === "GET") {
  responder(200, $dao->listar($utilizadorId));
}

if ($metodo === "POST" || $metodo === "PUT") {
  $dados = lerJson();
  $dados["id"] = $id; // no POST fica null: é uma tarefa nova
  $tarefa = Tarefa::deArray($dados, $utilizadorId);

  $erros = $tarefa->validar();
  if ($erros) {
    // 422 = os dados vieram inválidos (não é "não encontrado" nem erro do servidor)
    responder(422, ["erros" => $erros]);
  }
  if ($metodo === "POST") {
    responder(201, ["id" => $dao->criar($tarefa)]);
  }
  if ($id === null || !$dao->atualizar($tarefa)) {
    responder(404, ["erro" => "erro.nao_encontrada"]);
  }
  responder(200, ["ok" => true]);
}

if ($metodo === "DELETE") {
  if ($id === null || !$dao->eliminar($id, $utilizadorId)) {
    responder(404, ["erro" => "erro.nao_encontrada"]);
  }
  responder(200, ["ok" => true]);
}

responder(405, ["erro" => "erro.metodo"]);
