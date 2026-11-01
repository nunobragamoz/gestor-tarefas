<?php
// Página de login. Quem já tem sessão vai diretamente para as tarefas
require __DIR__ . "/api/config.php";

if (isset($_SESSION["utilizador_id"])) {
  header("Location: dashboard.php");
  exit;
}

$pagina = "login";
require __DIR__ . "/includes/cabecalho.php";
?>
<form id="form-login" class="card card-body shadow-sm login mx-auto" novalidate>
  <h1 class="h4 mb-3"><?= e(t("login.titulo")) ?></h1>
  <div id="erro-login" class="alert alert-danger d-none"></div>
  <div class="mb-3">
    <label for="email" class="form-label"><?= e(t("login.email")) ?></label>
    <input id="email" name="email" type="email" class="form-control" autocomplete="username">
  </div>
  <div class="mb-3">
    <label for="password" class="form-label"><?= e(t("login.password")) ?></label>
    <input id="password" name="password" type="password" class="form-control" autocomplete="current-password">
  </div>
  <button class="btn btn-primary w-100"><?= e(t("login.titulo")) ?></button>
</form>
<?php require __DIR__ . "/includes/rodape.php"; ?>
