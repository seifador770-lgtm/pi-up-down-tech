'use strict';

const CHAVE_CARRINHO = 'carrinho_oficina';

function carregarCarrinho() {
    try {
        const dados = JSON.parse(localStorage.getItem(CHAVE_CARRINHO) || '[]');
        if (!Array.isArray(dados)) return [];
        return dados.filter(item =>
            item && Number.isInteger(Number(item.id)) &&
            typeof item.nome === 'string' &&
            Number.isFinite(Number(item.preco)) && Number(item.preco) > 0 &&
            Number.isInteger(Number(item.qtd)) && Number(item.qtd) > 0
        ).map(item => ({
            id: Number(item.id),
            nome: item.nome,
            preco: Number(item.preco),
            qtd: Number(item.qtd),
            estoque: Number.isInteger(Number(item.estoque)) ? Number(item.estoque) : 999999
        }));
    } catch {
        return [];
    }
}

let carrinho = carregarCarrinho();

function moeda(valor) {
    return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function salvarCarrinho() {
    localStorage.setItem(CHAVE_CARRINHO, JSON.stringify(carrinho));
}

function atualizarInterfaceCarrinho() {
    const lista = document.getElementById('itens-carrinho');
    const count = document.getElementById('cart-count');
    const totalEl = document.getElementById('cart-total');
    if (!lista || !count || !totalEl) return;

    lista.replaceChildren();
    let total = 0;
    let totalItens = 0;

    carrinho.forEach((item, index) => {
        total += item.preco * item.qtd;
        totalItens += item.qtd;

        const linha = document.createElement('div');
        linha.className = 'item-carrinho';
        const descricao = document.createElement('div');
        const nome = document.createElement('strong');
        nome.textContent = item.nome;
        const detalhe = document.createElement('small');
        detalhe.textContent = `${item.qtd}x ${moeda(item.preco)}`;
        descricao.append(nome, document.createElement('br'), detalhe);

        const remover = document.createElement('button');
        remover.type = 'button';
        remover.textContent = 'Remover';
        remover.addEventListener('click', () => removerItem(index));

        linha.append(descricao, remover);
        lista.appendChild(linha);
    });

    if (carrinho.length === 0) {
        const vazio = document.createElement('p');
        vazio.textContent = 'Seu carrinho está vazio.';
        vazio.style.color = '#94a3b8';
        vazio.style.textAlign = 'center';
        lista.appendChild(vazio);
    }

    count.textContent = String(totalItens);
    totalEl.textContent = moeda(total);
    salvarCarrinho();
}

function adicionarAoCarrinho(id, nome, preco, estoque) {
    id = Number(id);
    preco = Number(preco);
    estoque = Number(estoque);
    if (!Number.isInteger(id) || !Number.isFinite(preco) || preco <= 0) return;

    const existente = carrinho.find(item => item.id === id);
    if (existente) {
        if (existente.qtd >= estoque) {
            alert('Não há mais unidades disponíveis em estoque.');
            return;
        }
        existente.qtd++;
        existente.estoque = estoque;
    } else {
        carrinho.push({ id, nome: String(nome), preco, qtd: 1, estoque });
    }
    atualizarInterfaceCarrinho();
    document.getElementById('modal-carrinho')?.classList.add('aberto');
}

function removerItem(index) {
    if (!Number.isInteger(index) || index < 0 || index >= carrinho.length) return;
    carrinho.splice(index, 1);
    atualizarInterfaceCarrinho();
}

function toggleCarrinho() {
    document.getElementById('modal-carrinho')?.classList.toggle('aberto');
}

function finalizarPedido() {
    if (carrinho.length === 0) {
        alert('Adicione pelo menos uma peça ao carrinho antes de finalizar.');
        return;
    }
    alert('Carrinho validado! Esta versão ainda não envia o pedido ao servidor nem registra a venda.');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-adicionar').forEach(botao => {
        botao.addEventListener('click', () => {
            adicionarAoCarrinho(
                botao.dataset.id,
                botao.dataset.nome,
                botao.dataset.preco,
                botao.dataset.estoque
            );
        });
    });
    atualizarInterfaceCarrinho();
});
