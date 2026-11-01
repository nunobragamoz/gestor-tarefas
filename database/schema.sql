-- Base de dados do Gestor de Tarefas
SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS gestor_tarefas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestor_tarefas;

DROP TABLE IF EXISTS tarefas;
DROP TABLE IF EXISTS utilizadores;

CREATE TABLE utilizadores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE tarefas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  descricao TEXT,
  data_conclusao DATE NOT NULL,
  estado ENUM('pendente', 'em_andamento', 'concluida') NOT NULL DEFAULT 'pendente',
  -- Cada tarefa pertence a um utilizador; se o utilizador for apagado, as tarefas também
  FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
  INDEX (utilizador_id)
);

-- Dois utilizadores de teste (palavra-passe de ambos: Tarefas2026)
INSERT INTO utilizadores (nome, email, password_hash) VALUES
  ('Ana Silva', 'ana@teste.pt', '$2y$10$SSnGdYBFz4d7papjCjPVL.jruM6M.y6c1f/v7GvTztiqjbfttXaKy'),
  ('Bruno Costa', 'bruno@teste.pt', '$2y$10$W9Gj/ghX3ByWre2HA4cTbOljzlq7xpxc5B8oDeUGC2oTDQZMf3RqW');

-- Algumas tarefas de exemplo para a Ana
INSERT INTO tarefas (utilizador_id, titulo, descricao, data_conclusao, estado) VALUES
  (1, 'Rever o relatório', 'Ler e comentar a versão 2', '2026-11-05', 'pendente'),
  (1, 'Preparar a apresentação', 'Slides para segunda-feira', '2026-11-09', 'em_andamento'),
  (1, 'Reunião semanal', 'Sala 2, 10h', '2026-10-30', 'concluida');
