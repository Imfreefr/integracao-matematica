<?php

declare(strict_types=1);

namespace App\Models;

final class Matriz
{
    /** @var float[][] */
    private array $dados;
    private int $linhas;
    private int $colunas;

    public function __construct(array $dados)
    {
        if ($dados === []) throw new \InvalidArgumentException('Matriz vazia');
        $colunas = count($dados[0]);
        if ($colunas === 0) throw new \InvalidArgumentException('Matriz vazia');
        foreach ($dados as $linha) {
            if (!is_array($linha) || count($linha) !== $colunas) throw new \InvalidArgumentException('Linhas com tamanhos diferentes');
            foreach ($linha as $valor) if (!is_numeric($valor)) throw new \InvalidArgumentException('Valor não numérico');
        }
        $this->dados = array_map(fn($linha) => array_map(fn($valor) => (float)$valor, $linha), $dados);
        $this->linhas = count($dados);
        $this->colunas = $colunas;
    }

    public static function deArray(array $dados): self
    {
        return new self($dados);
    }
    public static function nula(int $quantidadeLinhas, int $quantidadeColunas): self
    {
        return new self(array_fill(0, $quantidadeLinhas, array_fill(0, $quantidadeColunas, 0.0)));
    }
    public static function identidade(int $ordem): self
    {
        $dados = array_fill(0, $ordem, array_fill(0, $ordem, 0.0));
        for ($i = 0; $i < $ordem; $i++) $dados[$i][$i] = 1.0;
        return new self($dados);
    }

    public function linhas(): int
    {
        return $this->linhas;
    }
    public function colunas(): int
    {
        return $this->colunas;
    }
    public function paraArray(): array
    {
        return $this->dados;
    }
    public function obter(int $linha, int $coluna): float
    {
        return $this->dados[$linha][$coluna];
    }

    public function somar(self $outra): self
    {
        $this->garantirMesmasDimensoes($outra);
        $resultado = [];
        for ($linha = 0; $linha < $this->linhas; $linha++) for ($coluna = 0; $coluna < $this->colunas; $coluna++) $resultado[$linha][$coluna] = $this->dados[$linha][$coluna] + $outra->dados[$linha][$coluna];
        return new self($resultado);
    }

    public function subtrair(self $outra): self
    {
        $this->garantirMesmasDimensoes($outra);
        $resultado = [];
        for ($linha = 0; $linha < $this->linhas; $linha++) for ($coluna = 0; $coluna < $this->colunas; $coluna++) $resultado[$linha][$coluna] = $this->dados[$linha][$coluna] - $outra->dados[$linha][$coluna];
        return new self($resultado);
    }

    public function multiplicarPorEscalar(float $escalar): self
    {
        $resultado = $this->dados;
        foreach ($resultado as &$linha) foreach ($linha as &$valor) $valor *= $escalar;
        return new self($resultado);
    }

    public function multiplicar(self $outra): self
    {
        if ($this->colunas !== $outra->linhas) throw new \RuntimeException('Dimensões incompatíveis para multiplicação: ' . $this->colunas . ' vs ' . $outra->linhas);
        $resultado = array_fill(0, $this->linhas, array_fill(0, $outra->colunas, 0.0));
        for ($linha = 0; $linha < $this->linhas; $linha++) for ($k = 0; $k < $this->colunas; $k++) for ($coluna = 0; $coluna < $outra->colunas; $coluna++) $resultado[$linha][$coluna] += $this->dados[$linha][$k] * $outra->dados[$k][$coluna];
        return new self($resultado);
    }

    public function transpor(): self
    {
        $resultado = array_fill(0, $this->colunas, array_fill(0, $this->linhas, 0.0));
        for ($linha = 0; $linha < $this->linhas; $linha++) for ($coluna = 0; $coluna < $this->colunas; $coluna++) $resultado[$coluna][$linha] = $this->dados[$linha][$coluna];
        return new self($resultado);
    }

    public function determinante(): float
    {
        if ($this->linhas !== $this->colunas) throw new \RuntimeException('Determinante exige matriz quadrada');
        $ordem = $this->linhas;
        $copia = $this->dados;
        $sinal = 1;
        for ($coluna = 0; $coluna < $ordem; $coluna++) {
            $linhaPivo = $coluna;
            for ($linha = $coluna; $linha < $ordem; $linha++) if (abs($copia[$linha][$coluna]) > abs($copia[$linhaPivo][$coluna])) $linhaPivo = $linha;
            if (abs($copia[$linhaPivo][$coluna]) < 1e-12) return 0.0;
            if ($linhaPivo !== $coluna) {
                [$copia[$coluna], $copia[$linhaPivo]] = [$copia[$linhaPivo], $copia[$coluna]];
                $sinal *= -1;
            }
            for ($linha = $coluna + 1; $linha < $ordem; $linha++) {
                $fator = $copia[$linha][$coluna] / $copia[$coluna][$coluna];
                for ($c = $coluna; $c < $ordem; $c++) $copia[$linha][$c] -= $fator * $copia[$coluna][$c];
            }
        }
        $determinante = (float)$sinal;
        for ($i = 0; $i < $ordem; $i++) $determinante *= $copia[$i][$i];
        return $determinante;
    }

    public function inversa(): self
    {
        if ($this->linhas !== $this->colunas) throw new \RuntimeException('Inversa exige matriz quadrada');
        $ordem = $this->linhas;
        $ampliada = [];
        for ($linha = 0; $linha < $ordem; $linha++) $ampliada[$linha] = array_merge($this->dados[$linha], self::identidade($ordem)->dados[$linha]);
        for ($coluna = 0; $coluna < $ordem; $coluna++) {
            $linhaPivo = $coluna;
            for ($linha = $coluna; $linha < $ordem; $linha++) if (abs($ampliada[$linha][$coluna]) > abs($ampliada[$linhaPivo][$coluna])) $linhaPivo = $linha;
            if (abs($ampliada[$linhaPivo][$coluna]) < 1e-12) throw new \RuntimeException('Matriz singular — sem inversa');
            if ($linhaPivo !== $coluna) [$ampliada[$coluna], $ampliada[$linhaPivo]] = [$ampliada[$linhaPivo], $ampliada[$coluna]];
            $divisor = $ampliada[$coluna][$coluna];
            for ($c = 0; $c < 2 * $ordem; $c++) $ampliada[$coluna][$c] /= $divisor;
            for ($linha = 0; $linha < $ordem; $linha++) if ($linha !== $coluna) {
                $fator = $ampliada[$linha][$coluna];
                for ($c = 0; $c < 2 * $ordem; $c++) $ampliada[$linha][$c] -= $fator * $ampliada[$coluna][$c];
            }
        }
        $inversa = [];
        for ($linha = 0; $linha < $ordem; $linha++) $inversa[$linha] = array_slice($ampliada[$linha], $ordem);
        return new self($inversa);
    }

    public function igual(self $outra, float $tolerancia = 1e-9): bool
    {
        if ($this->linhas !== $outra->linhas || $this->colunas !== $outra->colunas) return false;
        for ($linha = 0; $linha < $this->linhas; $linha++) for ($coluna = 0; $coluna < $this->colunas; $coluna++) if (abs($this->dados[$linha][$coluna] - $outra->dados[$linha][$coluna]) > $tolerancia) return false;
        return true;
    }

    private function garantirMesmasDimensoes(self $outra): void
    {
        if ($this->linhas !== $outra->linhas || $this->colunas !== $outra->colunas) throw new \RuntimeException('Dimensões incompatíveis: ' . $this->linhas . 'x' . $this->colunas . ' vs ' . $outra->linhas . 'x' . $outra->colunas);
    }
}
