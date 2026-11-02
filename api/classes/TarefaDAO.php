<?php
// Acesso à tabela tarefas. Todas as consultas são preparadas (?) e filtram pelo
// utilizador, por isso ninguém vê nem altera as tarefas de outra pessoa
class TarefaDAO
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::ligar();
  }

  // Lista as tarefas do utilizador. Usa um ficheiro de cache para não ir à base de dados sempre
  public function listar(int $utilizadorId): array
  {
    $cache = $this->ficheiroCache($utilizadorId);
    if (is_file($cache)) {
      return json_decode(file_get_contents($cache), true);
    }
    $stmt = $this->pdo->prepare(
      "SELECT id, titulo, descricao, data_conclusao, estado FROM tarefas
       WHERE utilizador_id = ? ORDER BY estado = 'concluida', data_conclusao"
    );
    $stmt->execute([$utilizadorId]);
    $tarefas = $stmt->fetchAll();
    // o Apache corre com outro utilizador, por isso a pasta cache/ precisa de chmod 777 para o PHP poder gravar aqui
    file_put_contents($cache, json_encode($tarefas, JSON_UNESCAPED_UNICODE));
    return $tarefas;
  }

  public function criar(Tarefa $tarefa): int
  {
    $stmt = $this->pdo->prepare(
      "INSERT INTO tarefas (utilizador_id, titulo, descricao, data_conclusao, estado) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([
      $tarefa->getUtilizadorId(),
      $tarefa->getTitulo(),
      $tarefa->getDescricao(),
      $tarefa->getDataConclusao(),
      $tarefa->getEstado(),
    ]);
    $this->limparCache($tarefa->getUtilizadorId());
    return (int) $this->pdo->lastInsertId();
  }

  // Devolve false se a tarefa não existe ou é de outro utilizador
  public function atualizar(Tarefa $tarefa): bool
  {
    $stmt = $this->pdo->prepare(
      "UPDATE tarefas SET titulo = ?, descricao = ?, data_conclusao = ?, estado = ?
       WHERE id = ? AND utilizador_id = ?"
    );
    $stmt->execute([
      $tarefa->getTitulo(),
      $tarefa->getDescricao(),
      $tarefa->getDataConclusao(),
      $tarefa->getEstado(),
      $tarefa->getId(),
      $tarefa->getUtilizadorId(),
    ]);
    $this->limparCache($tarefa->getUtilizadorId());
    return $stmt->rowCount() > 0;
  }

  public function eliminar(int $id, int $utilizadorId): bool
  {
    $stmt = $this->pdo->prepare("DELETE FROM tarefas WHERE id = ? AND utilizador_id = ?");
    $stmt->execute([$id, $utilizadorId]);
    $this->limparCache($utilizadorId);
    return $stmt->rowCount() > 0;
  }

  // Importa várias tarefas: ou entram todas, ou nenhuma (transação)
  public function importar(array $tarefas): int
  {
    $this->pdo->beginTransaction();
    try {
      foreach ($tarefas as $tarefa) {
        $this->criar($tarefa);
      }
      $this->pdo->commit();
    } catch (Exception $erro) {
      $this->pdo->rollBack();
      throw $erro;
    }
    return count($tarefas);
  }

  private function ficheiroCache(int $utilizadorId): string
  {
    return __DIR__ . "/../../cache/tarefas_" . $utilizadorId . ".json";
  }

  // apago a cache sempre que algo muda, senão a lista continuava a mostrar dados antigos
  private function limparCache(int $utilizadorId): void
  {
    $cache = $this->ficheiroCache($utilizadorId);
    if (is_file($cache)) {
      unlink($cache);
    }
  }
}
