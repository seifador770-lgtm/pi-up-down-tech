<?php
require_once __DIR__ . '/assets/api/conexao.php';

$categoriaFiltro = trim($_GET['categoria'] ?? '');
$categoriasPermitidas = ['Motor', 'Freios', 'Suspensão', 'Ignição'];
if ($categoriaFiltro !== '' && !in_array($categoriaFiltro, $categoriasPermitidas, true)) {
    $categoriaFiltro = '';
}

if ($categoriaFiltro !== '') {
    $stmt = $pdo->prepare('SELECT * FROM pecas WHERE categoria = ? ORDER BY id DESC');
    $stmt->execute([$categoriaFiltro]);
} else {
    $stmt = $pdo->query('SELECT * FROM pecas ORDER BY id DESC');
}
$pecas = $stmt->fetchAll();

function e($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TurboPeças | Oficina & Autopeças</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>⚙️ PI-UP-<span>DOWN-TECH</span></h1>
            <nav>
                <a href="index.php">Loja</a>
                <a href="admin.php">+ Cadastrar Peça</a>
                <button id="btn-carrinho" type="button" onclick="toggleCarrinho()">
                    🛒 Carrinho (<span id="cart-count">0</span>)
                </button>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alerta-sucesso" role="status">Peça cadastrada no catálogo com sucesso!</div>
        <?php elseif (isset($_GET['erro'])): ?>
            <div class="alerta-erro" role="alert">Não foi possível cadastrar a peça. Verifique os dados e se o SKU já existe.</div>
        <?php endif; ?>

        <section class="filtros">
            <h2>Catálogo de Peças</h2>
            <div class="categorias-btns">
                <a href="index.php" class="<?= $categoriaFiltro === '' ? 'ativo' : '' ?>">Todas</a>
                <a href="index.php?categoria=Motor" class="<?= $categoriaFiltro === 'Motor' ? 'ativo' : '' ?>">Motor</a>
                <a href="index.php?categoria=Freios" class="<?= $categoriaFiltro === 'Freios' ? 'ativo' : '' ?>">Freios</a>
                <a href="index.php?categoria=Suspensão" class="<?= $categoriaFiltro === 'Suspensão' ? 'ativo' : '' ?>">Suspensão</a>
                <a href="index.php?categoria=Ignição" class="<?= $categoriaFiltro === 'Ignição' ? 'ativo' : '' ?>">Ignição</a>
            </div>
        </section>

        <section class="grid-pecas">
            <?php if (!$pecas): ?>
                <p>Nenhuma peça encontrada nesta categoria.</p>
            <?php endif; ?>
            <?php foreach ($pecas as $peca): ?>
                <div class="card">
                    <span class="badge"><?= e($peca['categoria']) ?></span>
                    <img src="<?= e($peca['imagem_url']) ?>" alt="<?= e($peca['nome']) ?>" loading="lazy">
                    <div class="card-body">
                        <small class="sku">Cód: <?= e($peca['codigo_sku']) ?></small>
                        <h3><?= e($peca['nome']) ?></h3>
                        <p class="estoque">Estoque: <?= (int)$peca['estoque'] ?> un.</p>
                        <div class="card-footer">
                            <span class="preco">R$ <?= number_format((float)$peca['preco'], 2, ',', '.') ?></span>
                            <?php if ((int)$peca['estoque'] > 0): ?>
                                <button type="button" class="btn-adicionar"
                                    data-id="<?= (int)$peca['id'] ?>"
                                    data-nome="<?= e($peca['nome']) ?>"
                                    data-preco="<?= e($peca['preco']) ?>"
                                    data-estoque="<?= (int)$peca['estoque'] ?>">Adicionar</button>
                            <?php else: ?>
                                <button type="button" disabled>Sem estoque</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <aside id="modal-carrinho" class="carrinho-sidebar" aria-label="Carrinho de compras">
        <div class="carrinho-header">
            <h3>Seu Pedido</h3>
            <button type="button" aria-label="Fechar carrinho" onclick="toggleCarrinho()">✖</button>
        </div>
        <div id="itens-carrinho" class="carrinho-itens"></div>
        <div class="carrinho-footer">
            <div class="total">Total: <span id="cart-total">R$ 0,00</span></div>
            <button class="btn-finalizar" type="button" onclick="finalizarPedido()">Finalizar Compra</button>
        </div>
    </aside>

    <script src="assets/js/script.js" defer></script>
</body>
</html>
