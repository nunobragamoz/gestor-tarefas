<?php
// Início de todas as páginas: <head>, barra de navegação (só com login) e <main>
// Antes de o incluir, a página define $pagina ("login", "tarefas" ou "config")

// Devolve " active" para o link da página atual
function ativo(string $nome, string $pagina): string
{
  return $nome === $pagina ? " active" : "";
}
?>
<!doctype html>
<html lang="<?= idioma() ?>" data-bs-theme="<?= tema() === "escuro" ? "dark" : "light" ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Token CSRF: o app.js lê-o daqui e envia-o em todos os pedidos -->
  <meta name="csrf" content="<?= e($_SESSION["csrf"]) ?>">
  <title><?= e(t("app.titulo")) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= asset("assets/css/style.css") ?>">
</head>
<body>
<?php if (isset($_SESSION["utilizador_id"])): ?>
  <nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
    <div class="container flex-wrap gap-2">
      <span class="navbar-brand"><?= e(t("app.titulo")) ?></span>
      <div class="navbar-nav me-auto">
        <a class="nav-link<?= ativo("tarefas", $pagina) ?>" href="dashboard.php"><?= e(t("menu.tarefas")) ?></a>
        <a class="nav-link<?= ativo("config", $pagina) ?>" href="configuracoes.php"><?= e(t("menu.config")) ?></a>
      </div>
      <span class="navbar-text me-2 d-none d-sm-inline"><?= e(t("menu.ola")) ?> <?= e($_SESSION["utilizador_nome"]) ?></span>
      <button id="sair" type="button" class="btn btn-outline-light btn-sm"><?= e(t("menu.sair")) ?></button>
    </div>
  </nav>
<?php endif; ?>
<main class="container py-4">
