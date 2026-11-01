<?php
// Acesso à tabela utilizadores (só esta classe escreve SQL de utilizadores)
class UtilizadorDAO
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::ligar();
  }

  public function procurarPorEmail(string $email): ?Utilizador
  {
    $stmt = $this->pdo->prepare("SELECT id, nome, email, password_hash FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    $linha = $stmt->fetch();
    if (!$linha) {
      return null;
    }
    return new Utilizador((int) $linha["id"], $linha["nome"], $linha["email"], $linha["password_hash"]);
  }
}
