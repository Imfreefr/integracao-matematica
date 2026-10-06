<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Matriz;
use App\Models\SistemaLinear;
use InvalidArgumentException;
use Throwable;

final class CalculadoraController
{
    public function processar(array $dados): array
    {
        $operacao = $dados['operacao'] ?? 'somar';
        $resultado = null;
        $erro = null;

        if (($dados['enviado'] ?? false) === true) {
            try {
                $resultado = $this->executar($operacao, $dados);
            } catch (Throwable $exception) {
                $erro = $exception->getMessage();
            }
        }

        return ['operacao' => $operacao, 'resultado' => $resultado, 'erro' => $erro, 'dados' => $dados];
    }

    private function executar(string $operacao, array $dados): string
    {
        $matrizA = $this->interpretarMatriz((string) ($dados['matrizA'] ?? ''));

        return match ($operacao) {
            'somar' => $this->formatarMatriz($matrizA->somar($this->interpretarMatriz((string) $dados['matrizB']))),
            'subtrair' => $this->formatarMatriz($matrizA->subtrair($this->interpretarMatriz((string) $dados['matrizB']))),
            'escalar' => $this->formatarMatriz($matrizA->multiplicarPorEscalar((float) $dados['escalar'])),
            'multiplicar' => $this->formatarMatriz($matrizA->multiplicar($this->interpretarMatriz((string) $dados['matrizB']))),
            'transpor' => $this->formatarMatriz($matrizA->transpor()),
            'determinante' => (string) $matrizA->determinante(),
            'inversa' => $this->formatarMatriz($matrizA->inversa()),
            'resolver' => $this->formatarVetor(SistemaLinear::resolver($matrizA, $this->interpretarVetor((string) $dados['matrizB']))),
            default => throw new InvalidArgumentException('Operação inválida'),
        };
    }

    private function interpretarMatriz(string $texto): Matriz
    {
        $texto = trim($texto);
        if ($texto === '') throw new InvalidArgumentException('Matriz vazia');
        $decodificado = json_decode($texto, true);
        if (is_array($decodificado)) return new Matriz($decodificado);

        $dados = [];
        foreach (preg_split('/\s*;\s*|\n+/', $texto) as $linha) {
            $linha = trim($linha);
            if ($linha !== '') $dados[] = array_map('floatval', preg_split('/[\s,]+/', $linha));
        }
        return new Matriz($dados);
    }

    private function interpretarVetor(string $texto): array
    {
        $decodificado = json_decode(trim($texto), true);
        if (is_array($decodificado)) return array_map('floatval', $decodificado);
        return array_map('floatval', preg_split('/[\s,;]+/', trim($texto)));
    }

    private function formatarMatriz(Matriz $matriz): string
    {
        return implode("\n", array_map(
            fn (array $linha): string => implode("\t", array_map($this->formatarNumero(...), $linha)),
            $matriz->paraArray(),
        ));
    }

    private function formatarVetor(array $vetor): string
    {
        return implode("\n", array_map($this->formatarNumero(...), $vetor));
    }

    private function formatarNumero(float $valor): string
    {
        return rtrim(rtrim(number_format($valor, 6, '.', ''), '0'), '.');
    }
}
