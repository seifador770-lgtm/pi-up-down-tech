<?php
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../admin.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$sku = strtoupper(trim($_POST['codigo_sku'] ?? ''));
$preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
$estoque = filter_var($_POST['estoque'] ?? null, FILTER_VALIDATE_INT);
$imagem = trim($_POST['imagem_url'] ?? '');

$categoriasPermitidas = ['Motor', 'Freios', 'Suspensão', 'Ignição'];

if ($nome === '' || strlen($nome) > 600 ||
    !in_array($categoria, $categoriasPermitidas, true) ||
    $sku === '' || strlen($sku) > 200 ||
    $preco === false || $preco <= 0 ||
    $estoque === false || $estoque < 0 ||
    ($imagem !== '' && !filter_var($imagem, FILTER_VALIDATE_URL))) {
    header('Location: ../../admin.php?erro=dados');
    exit;
}

if ($imagem === '') {
    $imagem = 'https://placehold.co/300x200/1e293b/f8fafc?text=' . rawurlencode($nome);
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO pecas (nome, categoria, codigo_sku, preco, estoque, imagem_url)
         VALUES (:nome, :categoria, :sku, :preco, :estoque, :imagem)'
    );
    $stmt->execute([
        ':nome' => $nome,
        ':categoria' => $categoria,
        ':sku' => $sku,
        ':preco' => $preco,
        ':estoque' => $estoque,
        ':imagem' => $imagem
    ]);
    header('Location: ../../index.php?sucesso=1');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: ../../admin.php?erro=cadastro');
    exit;
}
