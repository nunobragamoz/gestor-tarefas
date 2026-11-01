<?php
// Configurações: preferências (guardadas em cookies) e exportar / importar tarefas
require __DIR__ . "/api/config.php";
exigirLoginPagina();

// Guardar preferências: formulário normal (POST) que grava dois cookies durante 1 ano
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")) {
    http_response_code(403);
    exit(e(t("erro.csrf")));
  }
  // Só aceita valores conhecidos (nunca grava o que o utilizador escreveu)
  $idioma = ($_POST["idioma"] ?? "") === "en" ? "en" : "pt";
  $tema = ($_POST["tema"] ?? "") === "escuro" ? "escuro" : "claro";
  $opcoes = ["expires" => time() + 60 * 60 * 24 * 365, "path" => "/", "httponly" => true, "samesite" => "Lax"];
  setcookie("idioma", $idioma, $opcoes);
  setcookie("tema", $tema, $opcoes);

  // POST → redirecionar → GET: recarregar a página não volta a enviar o formulário
  header("Location: configuracoes.php?guardado=1");
  exit;
}

$pagina = "config";
require __DIR__ . "/includes/cabecalho.php";
?>
<h1 class="h4 mb-3"><?= e(t("menu.config")) ?></h1>
<?php if (isset($_GET["guardado"])): ?>
  <div class="alert alert-success py-2"><?= e(t("config.guardadas")) ?></div>
<?php endif; ?>
<div id="mensagem" class="alert d-none"></div>

<div class="row g-4">
  <div class="col-md-6">
    <form method="post" class="card card-body shadow-sm">
      <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
      <h2 class="h5"><?= e(t("config.preferencias")) ?></h2>
      <label for="idioma" class="form-label"><?= e(t("config.idioma")) ?></label>
      <select id="idioma" name="idioma" class="form-select mb-3">
        <option value="pt" <?= idioma() === "pt" ? "selected" : "" ?>>Português</option>
        <option value="en" <?= idioma() === "en" ? "selected" : "" ?>>English</option>
      </select>
      <label for="tema" class="form-label"><?= e(t("config.tema")) ?></label>
      <select id="tema" name="tema" class="form-select mb-3">
        <option value="claro" <?= tema() === "claro" ? "selected" : "" ?>><?= e(t("tema.claro")) ?></option>
        <option value="escuro" <?= tema() === "escuro" ? "selected" : "" ?>><?= e(t("tema.escuro")) ?></option>
      </select>
      <button class="btn btn-primary"><?= e(t("botao.guardar")) ?></button>
    </form>
  </div>

  <div class="col-md-6">
    <div class="card card-body shadow-sm">
      <h2 class="h5"><?= e(t("config.dados")) ?></h2>
      <!-- Link normal (GET): o PHP envia o ficheiro para download -->
      <a class="btn btn-outline-primary mb-3" href="api/exportar.php"><?= e(t("config.exportar")) ?></a>
      <form id="form-importar" novalidate>
        <label for="ficheiro" class="form-label"><?= e(t("config.ficheiro")) ?></label>
        <input id="ficheiro" name="ficheiro" type="file" accept=".json,application/json" class="form-control mb-3">
        <button class="btn btn-primary"><?= e(t("config.importar")) ?></button>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . "/includes/rodape.php"; ?>
