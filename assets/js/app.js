// Gestor de Tarefas: o JavaScript fala com a API PHP (fetch) e atualiza a página sem a recarregar

const API = 'api'
const csrf = document.querySelector('meta[name="csrf"]').content
const textos = JSON.parse(document.getElementById('textos').textContent)

// t('chave') devolve o texto no idioma atual (o PHP pôs os textos no rodapé)
function t(chave) {
  return textos[chave] ?? chave
}

// Faz um pedido à API e devolve { ok, dados }. Envia sempre o token CSRF
async function pedido(ficheiro, metodo = 'GET', corpo = null) {
  const opcoes = { method: metodo, headers: { 'X-CSRF-Token': csrf } }
  if (corpo instanceof FormData) {
    opcoes.body = corpo // envio de ficheiro (importação)
  } else if (corpo) {
    opcoes.headers['Content-Type'] = 'application/json'
    opcoes.body = JSON.stringify(corpo)
  }
  const resposta = await fetch(`${API}/${ficheiro}`, opcoes)
  const dados = await resposta.json()
  // A sessão acabou: volta ao login
  if (dados.erro === 'erro.sem_login') {
    location.href = 'index.php'
  }
  return { ok: resposta.ok, dados }
}

// Mostra uma mensagem verde (success) ou vermelha (danger)
function mostrar(caixa, texto, tipo = 'success') {
  caixa.textContent = texto
  caixa.className = `alert alert-${tipo} py-2`
}

/* ---------- Login (index.php) ---------- */
const formLogin = document.getElementById('form-login')
if (formLogin) {
  formLogin.addEventListener('submit', async (evento) => {
    evento.preventDefault() // não recarrega a página
    const { ok, dados } = await pedido('login.php', 'POST', {
      email: formLogin.elements.email.value,
      password: formLogin.elements.password.value,
    })
    if (ok) {
      location.href = 'dashboard.php'
    } else {
      mostrar(document.getElementById('erro-login'), t(dados.erro), 'danger')
    }
  })
}

/* ---------- Logout (botão da navbar) ---------- */
const botaoSair = document.getElementById('sair')
if (botaoSair) {
  botaoSair.addEventListener('click', async () => {
    await pedido('logout.php', 'POST')
    location.href = 'index.php'
  })
}

/* ---------- Tarefas (dashboard.php) ---------- */
const formTarefa = document.getElementById('form-tarefa')
if (formTarefa) {
  const lista = document.getElementById('lista')
  const modelo = document.getElementById('modelo-tarefa')
  const mensagem = document.getElementById('mensagem')
  const campos = ['titulo', 'descricao', 'data_conclusao', 'estado']
  let tarefas = [] // última lista recebida do PHP
  let emEdicao = null // tarefa que está no formulário, ou null (tarefa nova)

  // Pede a lista ao PHP (GET) e desenha um cartão por tarefa
  async function carregar() {
    const { ok, dados } = await pedido('tarefas.php')
    if (!ok) return
    tarefas = dados
    lista.replaceChildren()
    document.getElementById('vazia').classList.toggle('d-none', tarefas.length > 0)
    for (const tarefa of tarefas) {
      const copia = modelo.content.cloneNode(true)
      const cartao = copia.querySelector('.tarefa')
      cartao.classList.add(`tarefa--${tarefa.estado}`)
      cartao.dataset.id = tarefa.id
      // textContent mostra o texto tal como é: <b> aparece como texto (proteção XSS)
      cartao.querySelector('.estado').textContent = t('estado.' + tarefa.estado)
      cartao.querySelector('.titulo').textContent = tarefa.titulo
      cartao.querySelector('.descricao').textContent = tarefa.descricao
      cartao.querySelector('.data').textContent = tarefa.data_conclusao
      if (tarefa.estado === 'concluida') {
        cartao.querySelector('[data-acao="concluir"]').remove()
      }
      lista.append(copia)
    }
  }

  function limparErros() {
    formTarefa.querySelectorAll('.is-invalid').forEach((campo) => campo.classList.remove('is-invalid'))
    document.getElementById('erro-geral').classList.add('d-none')
  }

  // erros = { titulo: 'erro.titulo', ... } vindo do PHP (Tarefa::validar)
  function mostrarErros(erros) {
    for (const [campo, chave] of Object.entries(erros)) {
      formTarefa.elements[campo].classList.add('is-invalid')
      formTarefa.querySelector(`[data-erro="${campo}"]`).textContent = t(chave)
    }
  }

  // Formulário em modo "Nova tarefa"
  function modoCriar() {
    emEdicao = null
    formTarefa.reset()
    limparErros()
    document.getElementById('titulo-form').textContent = t('tarefas.nova')
    document.getElementById('botao-guardar').textContent = t('botao.criar')
    document.getElementById('botao-cancelar').classList.add('d-none')
  }

  // Formulário em modo "Editar tarefa": copia os valores da tarefa para os campos
  function modoEditar(tarefa) {
    emEdicao = tarefa
    limparErros()
    for (const campo of campos) {
      formTarefa.elements[campo].value = tarefa[campo] ?? ''
    }
    document.getElementById('titulo-form').textContent = t('tarefas.editar')
    document.getElementById('botao-guardar').textContent = t('botao.guardar')
    document.getElementById('botao-cancelar').classList.remove('d-none')
    formTarefa.elements.titulo.focus()
  }

  // Criar (POST) ou atualizar (PUT); o PHP valida e devolve os erros
  formTarefa.addEventListener('submit', async (evento) => {
    evento.preventDefault()
    limparErros()
    const dados = Object.fromEntries(new FormData(formTarefa))
    const { ok, dados: resposta } = emEdicao
      ? await pedido(`tarefas.php?id=${emEdicao.id}`, 'PUT', dados)
      : await pedido('tarefas.php', 'POST', dados)

    if (!ok) {
      if (resposta.erros) {
        mostrarErros(resposta.erros)
      } else {
        mostrar(document.getElementById('erro-geral'), t(resposta.erro), 'danger')
      }
      return
    }
    mostrar(mensagem, t(emEdicao ? 'msg.atualizada' : 'msg.criada'))
    modoCriar()
    carregar()
  })

  document.getElementById('botao-cancelar').addEventListener('click', modoCriar)

  // Um só "ouvinte" para os botões de todos os cartões (delegação de eventos)
  lista.addEventListener('click', async (evento) => {
    const botao = evento.target.closest('button[data-acao]')
    if (!botao) return
    const id = botao.closest('.tarefa').dataset.id
    const tarefa = tarefas.find((item) => String(item.id) === id)

    if (botao.dataset.acao === 'editar') {
      modoEditar(tarefa)
      return
    }
    if (botao.dataset.acao === 'concluir') {
      // Envia a mesma tarefa com o estado "concluida"
      await pedido(`tarefas.php?id=${id}`, 'PUT', { ...tarefa, estado: 'concluida' })
      mostrar(mensagem, t('msg.atualizada'))
    }
    if (botao.dataset.acao === 'eliminar') {
      if (!confirm(t('confirmar.eliminar'))) return
      await pedido(`tarefas.php?id=${id}`, 'DELETE')
      mostrar(mensagem, t('msg.eliminada'))
      if (emEdicao && String(emEdicao.id) === id) modoCriar()
    }
    carregar()
  })

  carregar()
}

/* ---------- Importar (configuracoes.php) ---------- */
const formImportar = document.getElementById('form-importar')
if (formImportar) {
  formImportar.addEventListener('submit', async (evento) => {
    evento.preventDefault()
    // FormData = como um formulário com ficheiro (multipart)
    const { ok, dados } = await pedido('importar.php', 'POST', new FormData(formImportar))
    const caixa = document.getElementById('mensagem')
    if (ok) {
      mostrar(caixa, t('config.importadas').replace('{n}', dados.total))
      formImportar.reset()
    } else {
      mostrar(caixa, t(dados.erro), 'danger')
    }
  })
}
