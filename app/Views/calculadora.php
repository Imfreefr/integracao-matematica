<?php

declare(strict_types=1);

$operacao = $pagina['operacao'];
$dados = $pagina['dados'];
$mostrarB = !in_array($operacao, ['transpor', 'determinante', 'inversa', 'escalar'], true);
$mostrarEscalar = $operacao === 'escalar';
$mostrarAjudaVetor = $operacao === 'resolver';
$operacoes = [
    'somar' => 'A + B (soma)', 'subtrair' => 'A − B (subtração)',
    'escalar' => 'k·A (escalar)', 'multiplicar' => 'A × B (multiplicação)',
    'transpor' => 'Aᵀ (transposta)', 'determinante' => 'det(A) (determinante)',
    'inversa' => 'A⁻¹ (inversa)', 'resolver' => 'Ax = b (sistema linear)',
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Álgebra Linear — PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-4" style="max-width:900px">
        <h1 class="h3">Álgebra Linear — Operações Matriciais</h1>
        <form method="post" class="card p-3 shadow-sm">
            <div class="mb-3">
                <label class="form-label" for="operacao">Operação</label>
                <select name="operacao" id="operacao" class="form-select" required>
                    <?php foreach ($operacoes as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= $operacao === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="matrizA">Matriz A</label>
                <textarea name="matrizA" id="matrizA" rows="4" class="form-control" required><?= htmlspecialchars($dados['matrizA'] ?? '[[1,2],[3,4]]') ?></textarea>
            </div>
            <div class="mb-3" <?= $mostrarB ? '' : 'hidden' ?> >
                <label class="form-label" for="matrizB">Matriz B</label>
                <textarea name="matrizB" id="matrizB" rows="4" class="form-control"><?= htmlspecialchars($dados['matrizB'] ?? '[[5,6],[7,8]]') ?></textarea>
                <div class="form-text" <?= $mostrarAjudaVetor ? '' : 'hidden' ?>>Para Ax=b, vetor b como <code>[5,6]</code> ou <code>5,6</code></div>
            </div>
            <div class="mb-3" <?= $mostrarEscalar ? '' : 'hidden' ?> >
                <label class="form-label" for="escalar">Escalar k</label>
                <input name="escalar" id="escalar" type="number" step="any" class="form-control" value="<?= htmlspecialchars($dados['escalar'] ?? '2') ?>">
            </div>
            <div>
                <button class="btn btn-primary">Calcular</button>
                <a href="?" class="btn btn-outline-secondary">Limpar</a>
            </div>
        </form>
        <?php if ($pagina['erro']): ?>
            <div class="alert alert-danger mt-3"><?= htmlspecialchars($pagina['erro']) ?></div>
        <?php endif; ?>
        <?php if ($pagina['resultado'] !== null && !$pagina['erro']): ?>
            <section class="card mt-3 p-3">
                <h2 class="h6">Resultado</h2>
                <pre class="mb-0"><?= htmlspecialchars($pagina['resultado']) ?></pre>
            </section>
        <?php endif; ?>
        <div class="mt-4 small text-muted">
            <details>
                <summary>Exemplos</summary>
                <ul>
                    <li>Soma: A=[[1,2],[3,4]] B=[[5,6],[7,8]] → [[6,8],[10,12]]</li>
                    <li>det([[1,2],[3,4]]) = -2</li>
                    <li>Ax=b: A=[[2,1],[1,3]] b=[5,6] → x=[1.8,1.4]</li>
                </ul>
            </details>
        </div>
    </main>
</body>
</html>
