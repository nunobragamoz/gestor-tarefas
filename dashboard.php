<?php
// Dashboard: formulário à esquerda, lista de tarefas à direita (o app.js preenche a lista)
require __DIR__ . "/api/config.php";
exigirLoginPagina();

$pagina = "tarefas";
require __DIR__ . "/includes/cabecalho.php";
?>
<div class="row g-4">
  <div class="col-lg-4">
    <!-- novalidate: o browser não valida, assim vê as mensagens de erro do PHP -->
    <form id="form-tarefa" class="card card-body shadow-sm" novalidate>
      <h2 id="titulo-form" class="h5"><?= e(t("tarefas.nova")) ?></h2>
      <div id="erro-geral" class="alert alert-danger d-none"></div>
      <div class="mb-3">
        <label for="titulo" class="form-label"><?= e(t("campo.titulo")) ?></label>
        <input id="titulo" name="titulo" class="form-control">
        <div class="invalid-feedback" data-erro="titulo"></div>
      </div>
      <div class="mb-3">
        <label for="descricao" class="form-label"><?= e(t("campo.descricao")) ?></label>
        <textarea id="descricao" name="descricao" rows="3" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label for="data_conclusao" class="form-label"><?= e(t("campo.data")) ?></label>
        <input id="data_conclusao" name="data_conclusao" type="date" class="form-control">
        <div class="invalid-feedback" data-erro="data_conclusao"></div>
      </div>
      <div class="mb-3">
        <label for="estado" class="form-label"><?= e(t("campo.estado")) ?></label>
        <select id="estado" name="estado" class="form-select">
          <?php foreach (Tarefa::ESTADOS as $estado): ?>
            <option value="<?= $estado ?>"><?= e(t("estado." . $estado)) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback" data-erro="estado"></div>
      </div>
      <div class="d-flex gap-2">
        <button id="botao-guardar" class="btn btn-primary"><?= e(t("botao.criar")) ?></button>
        <button id="botao-cancelar" type="button" class="btn btn-outline-secondary d-none"><?= e(t("botao.cancelar")) ?></button>
      </div>
    </form>
  </div>

  <div class="col-lg-8">
    <h2 class="h5"><?= e(t("tarefas.minhas")) ?></h2>
    <div id="mensagem" class="alert d-none"></div>
    <p id="vazia" class="text-body-secondary d-none"><?= e(t("tarefas.vazia")) ?></p>
    <div id="lista" class="row row-cols-1 row-cols-md-2 g-3"></div>
  </div>
</div>

<!-- Modelo de um cartão: o app.js copia-o para cada tarefa e preenche os textos -->
<template id="modelo-tarefa">
  <div class="col">
    <div class="card h-100 tarefa">
      <div class="card-body">
        <span class="badge estado"></span>
        <h3 class="h6 mt-2 titulo"></h3>
        <p class="small mb-2 descricao"></p>
        <p class="small text-body-secondary mb-0"><?= e(t("tarefas.conclusao")) ?> <span class="data"></span></p>
      </div>
      <div class="card-footer d-flex gap-2 botoes">
        <button type="button" class="btn btn-sm btn-success" data-acao="concluir"><?= e(t("botao.concluir")) ?></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-acao="editar"><?= e(t("botao.editar")) ?></button>
        <button type="button" class="btn btn-sm btn-outline-danger" data-acao="eliminar"><?= e(t("botao.eliminar")) ?></button>
      </div>
    </div>
  </div>
</template>
<?php require __DIR__ . "/includes/rodape.php"; ?>
