<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Controllers\CalculadoraController;

$dados = $_POST;
$dados['enviado'] = $_SERVER['REQUEST_METHOD'] === 'POST';
$pagina = (new CalculadoraController())->processar($dados);

require __DIR__ . '/app/Views/calculadora.php';
