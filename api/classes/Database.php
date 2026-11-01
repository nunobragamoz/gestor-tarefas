<?php
// Ligação à base de dados com PDO (criada uma só vez por pedido)
class Database
{
  private static ?PDO $pdo = null;

  public static function ligar(): PDO
  {
    if (self::$pdo === null) {
      self::$pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NOME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // erros SQL viram exceções
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // linhas como arrays associativos
          PDO::MYSQL_ATTR_FOUND_ROWS => true, // UPDATE sem alterações conta como encontrado
        ]
      );
    }
    return self::$pdo;
  }
}
