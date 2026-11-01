<?php
// Um utilizador da aplicação
class Utilizador
{
  public function __construct(
    private int $id,
    private string $nome,
    private string $email,
    private string $passwordHash
  ) {}

  public function getId(): int { return $this->id; }
  public function getNome(): string { return $this->nome; }
  public function getEmail(): string { return $this->email; }

  // Compara a palavra-passe escrita com o hash guardado na base de dados
  public function verificarPassword(string $password): bool
  {
    return password_verify($password, $this->passwordHash);
  }
}
