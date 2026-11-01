<?php
// Configuração comum: todas as páginas e todos os ficheiros da API começam por aqui

// Dados da base de dados (XAMPP: utilizador root sem palavra-passe)
const DB_HOST = "localhost";
const DB_NOME = "gestor_tarefas";
const DB_USER = "root";
const DB_PASS = "";

// Carrega as classes sozinho: new TarefaDAO() faz require de classes/TarefaDAO.php
spl_autoload_register(function (string $classe) {
  require __DIR__ . "/classes/" . $classe . ".php";
});

// Sessão segura: o JavaScript não lê o cookie (httponly) e outros sites não o enviam (samesite)
session_set_cookie_params([
  "path" => "/",
  "httponly" => true,
  "samesite" => "Strict",
  "secure" => !empty($_SERVER["HTTPS"]),
]);
session_start();

// Token CSRF: um por sessão; as páginas põem-no numa <meta> e o JavaScript envia-o
if (empty($_SESSION["csrf"])) {
  $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

// ---------- Idioma, tema e textos ----------

// Idioma escolhido no cookie "idioma" (por omissão: português)
function idioma(): string
{
  return ($_COOKIE["idioma"] ?? "pt") === "en" ? "en" : "pt";
}

// Tema escolhido no cookie "tema" (por omissão: claro)
function tema(): string
{
  return ($_COOKIE["tema"] ?? "claro") === "escuro" ? "escuro" : "claro";
}

// Todos os textos do idioma atual (o ficheiro lang/pt.php ou lang/en.php é lido uma só vez)
function textos(): array
{
  static $textos = null;
  $textos ??= require __DIR__ . "/../lang/" . idioma() . ".php";
  return $textos;
}

// t("chave") devolve o texto traduzido
function t(string $chave): string
{
  return textos()[$chave] ?? $chave;
}

// e() escapa o texto antes de o pôr no HTML: <b> aparece como texto (proteção XSS)
function e(string $texto): string
{
  return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}

// Usa a versão .min do CSS/JS se existir; ?v= muda quando o ficheiro muda (cache do browser)
function asset(string $caminho): string
{
  $min = preg_replace('/\.(css|js)$/', '.min.$1', $caminho);
  $usar = is_file(__DIR__ . "/../" . $min) ? $min : $caminho;
  return $usar . "?v=" . filemtime(__DIR__ . "/../" . $usar);
}

// Páginas só para quem fez login: sem sessão, volta ao login
function exigirLoginPagina(): void
{
  if (!isset($_SESSION["utilizador_id"])) {
    header("Location: index.php");
    exit;
  }
}

// ---------- Ajudas da API (respostas JSON) ----------

// Envia o código HTTP e os dados em JSON, e termina
function responder(int $codigo, array $dados = []): never
{
  http_response_code($codigo);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($dados, JSON_UNESCAPED_UNICODE);
  exit;
}

// Devolve o id do utilizador autenticado; sem login responde 401
function exigirLogin(): int
{
  if (!isset($_SESSION["utilizador_id"])) {
    responder(401, ["erro" => "erro.sem_login"]);
  }
  return $_SESSION["utilizador_id"];
}

// Pedidos que alteram dados têm de trazer o token (cabeçalho X-CSRF-Token)
function exigirCsrf(): void
{
  $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
  if (!hash_equals($_SESSION["csrf"], $token)) {
    responder(403, ["erro" => "erro.csrf"]);
  }
}

// Lê o corpo JSON que o JavaScript envia e devolve-o como array
function lerJson(): array
{
  $dados = json_decode(file_get_contents("php://input"), true);
  return is_array($dados) ? $dados : [];
}
