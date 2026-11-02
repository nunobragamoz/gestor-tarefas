<?php
// Uma tarefa: guarda os dados e sabe validar-se
class Tarefa
{
  public const ESTADOS = ["pendente", "em_andamento", "concluida"];

  public function __construct(
    private ?int $id,
    private int $utilizadorId,
    private string $titulo,
    private string $descricao,
    private string $dataConclusao,
    private string $estado
  ) {}

  // Cria uma Tarefa a partir de um array (pedido do JavaScript ou ficheiro JSON)
  public static function deArray(array $dados, int $utilizadorId): Tarefa
  {
    return new Tarefa(
      isset($dados["id"]) ? (int) $dados["id"] : null,
      $utilizadorId,
      trim((string) ($dados["titulo"] ?? "")),
      trim((string) ($dados["descricao"] ?? "")),
      trim((string) ($dados["data_conclusao"] ?? "")),
      (string) ($dados["estado"] ?? "pendente")
    );
  }

  // devolve as chaves de tradução dos erros (ex.: "erro.titulo"), não o texto, para servir em PT e EN. Vazio = está tudo bem
  public function validar(): array
  {
    $erros = [];
    if ($this->titulo === "") {
      $erros["titulo"] = "erro.titulo";
    } elseif (mb_strlen($this->titulo) > 150) {
      $erros["titulo"] = "erro.titulo_longo";
    }
    // A data tem de existir mesmo (recusa 2026-02-31)
    $data = DateTime::createFromFormat("Y-m-d", $this->dataConclusao);
    if (!$data || $data->format("Y-m-d") !== $this->dataConclusao) {
      $erros["data_conclusao"] = "erro.data";
    }
    if (!in_array($this->estado, self::ESTADOS, true)) {
      $erros["estado"] = "erro.estado";
    }
    return $erros;
  }

  public function getId(): ?int { return $this->id; }
  public function getUtilizadorId(): int { return $this->utilizadorId; }
  public function getTitulo(): string { return $this->titulo; }
  public function getDescricao(): string { return $this->descricao; }
  public function getDataConclusao(): string { return $this->dataConclusao; }
  public function getEstado(): string { return $this->estado; }
}
