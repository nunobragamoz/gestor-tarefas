# Gestor de Tarefas - Caso Prático PHP

Aplicação de gestão de tarefas em PHP 8 (páginas e API REST), MySQL (dados)
e JavaScript (atualizações sem recarregar a página).

## Como instalar e executar
1. Copiar a pasta `gestor-tarefas` para `htdocs/exerciciosPhp/` do XAMPP.
2. Iniciar o Apache e o MySQL.
3. Importar `database/schema.sql` no phpMyAdmin (cria a BD `gestor_tarefas`).
4. Dar permissão de escrita à pasta `cache/` (`chmod 777 cache`).
5. Abrir http://localhost/exerciciosPhp/gestor-tarefas/
6. Entrar com `ana@teste.pt` ou `bruno@teste.pt`, palavra-passe `Tarefas2026`.

## Base de dados
- `utilizadores`: id, nome, email (único), password_hash.
- `tarefas`: id, utilizador_id, titulo, descricao, data_conclusao,
  estado (ENUM pendente / em_andamento / concluida).
- Chave estrangeira `utilizador_id` com ON DELETE CASCADE e índice.

## Estrutura e POO
- `api/classes/`: `Tarefa`, `Utilizador` (entidades), `TarefaDAO`,
  `UtilizadorDAO` (acesso aos dados), `Database` (ligação PDO).
- `api/tarefas.php`: API REST (GET, POST, PUT, DELETE) que devolve JSON.
- `index.php`, `dashboard.php`, `configuracoes.php`: páginas em PHP,
  com `includes/` comuns.
- `assets/js/app.js`: usa `fetch` para falar com a API sem recarregar.

## Segurança
- Consultas preparadas com PDO em todas as queries (SQL injection).
- Palavras-passe com `password_hash()` / `password_verify()`.
- Sessão com cookie httponly e samesite; novo id de sessão no login.
- Token CSRF em todos os pedidos que alteram dados.
- Saída escapada com `htmlspecialchars()` no PHP e `textContent` no JS (XSS).
- Cada consulta filtra pelo utilizador; pastas internas bloqueadas (.htaccess).

## Otimização
- Cache em ficheiro da lista de tarefas (`cache/`), apagada quando mudam.
- CSS e JS minificados (clean-css e terser) com `?v=` para a cache do browser.
- Compressão gzip e cache do browser no `.htaccess`.

## Idiomas e preferências
Português e inglês (`lang/pt.php`, `lang/en.php`); idioma e tema guardados
em cookies durante 1 ano.
