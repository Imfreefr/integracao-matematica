<?php

declare(strict_types=1);

namespace App\Models;

final class SistemaLinear
{
    public static function resolver(Matriz $matrizCoeficientes, array $vetorTermos): array
    {
        $ordem = $matrizCoeficientes->linhas();
        if ($matrizCoeficientes->linhas() !== $matrizCoeficientes->colunas()) throw new \RuntimeException('Sistema exige matriz quadrada');
        if (count($vetorTermos) !== $ordem) throw new \RuntimeException('Vetor b com tamanho incompatível');
        $matrizAmpliada = $matrizCoeficientes->paraArray();
        for ($linha = 0; $linha < $ordem; $linha++) $matrizAmpliada[$linha][] = (float)$vetorTermos[$linha];

        for ($coluna = 0; $coluna < $ordem; $coluna++) {
            $linhaPivo = $coluna;
            for ($linha = $coluna; $linha < $ordem; $linha++) if (abs($matrizAmpliada[$linha][$coluna]) > abs($matrizAmpliada[$linhaPivo][$coluna])) $linhaPivo = $linha;
            if (abs($matrizAmpliada[$linhaPivo][$coluna]) < 1e-12) {
                $linhaNula = true;
                for ($c = $coluna; $c < $ordem; $c++) if (abs($matrizAmpliada[$linhaPivo][$c]) > 1e-12) $linhaNula = false;
                if ($linhaNula && abs($matrizAmpliada[$linhaPivo][$ordem]) > 1e-12) throw new \RuntimeException('Sistema impossível — sem solução');
                throw new \RuntimeException('Sistema indeterminado ou matriz singular');
            }
            if ($linhaPivo !== $coluna) [$matrizAmpliada[$coluna], $matrizAmpliada[$linhaPivo]] = [$matrizAmpliada[$linhaPivo], $matrizAmpliada[$coluna]];
            for ($linha = $coluna + 1; $linha < $ordem; $linha++) {
                $fator = $matrizAmpliada[$linha][$coluna] / $matrizAmpliada[$coluna][$coluna];
                for ($c = $coluna; $c <= $ordem; $c++) $matrizAmpliada[$linha][$c] -= $fator * $matrizAmpliada[$coluna][$c];
            }
        }
        $solucao = array_fill(0, $ordem, 0.0);
        for ($linha = $ordem - 1; $linha >= 0; $linha--) {
            $soma = $matrizAmpliada[$linha][$ordem];
            for ($coluna = $linha + 1; $coluna < $ordem; $coluna++) $soma -= $matrizAmpliada[$linha][$coluna] * $solucao[$coluna];
            if (abs($matrizAmpliada[$linha][$linha]) < 1e-12) throw new \RuntimeException('Sistema indeterminado ou matriz singular');
            $solucao[$linha] = $soma / $matrizAmpliada[$linha][$linha];
        }
        return $solucao;
    }

    public static function resolverMatriz(Matriz $matrizCoeficientes, Matriz $matrizTermos): Matriz
    {
        if ($matrizCoeficientes->linhas() !== $matrizTermos->linhas()) throw new \RuntimeException('Dimensões incompatíveis A vs B');
        $quantidadeColunas = $matrizTermos->colunas();
        $resultado = [];
        for ($coluna = 0; $coluna < $quantidadeColunas; $coluna++) {
            $colunaVetor = array_column($matrizTermos->paraArray(), $coluna);
            $solucaoColuna = self::resolver($matrizCoeficientes, $colunaVetor);
            for ($linha = 0; $linha < $matrizCoeficientes->linhas(); $linha++) $resultado[$linha][$coluna] = $solucaoColuna[$linha];
        }
        return new Matriz($resultado);
    }
}
