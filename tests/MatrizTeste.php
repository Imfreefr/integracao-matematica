<?php
declare(strict_types=1);
namespace Tests;

use App\Models\Matriz;
use PHPUnit\Framework\TestCase;

final class MatrizTeste extends TestCase
{
    public function testConstrutorEAcessores(): void {
        $matriz = new Matriz([[1,2],[3,4]]);
        $this->assertSame(2, $matriz->linhas());
        $this->assertSame(2, $matriz->colunas());
        $this->assertEqualsWithDelta(3.0, $matriz->obter(1,0), 1e-9);
    }

    public function testNulaEIdentidade(): void {
        $this->assertTrue(Matriz::nula(2,3)->igual(new Matriz([[0,0,0],[0,0,0]])));
        $this->assertTrue(Matriz::identidade(3)->igual(new Matriz([[1,0,0],[0,1,0],[0,0,1]])));
        $this->assertTrue(Matriz::identidade(1)->igual(new Matriz([[1]])));
    }

    public function testSomarCenarioFeliz(): void {
        $matrizA = new Matriz([[1,2],[3,4]]); $matrizB = new Matriz([[5,6],[7,8]]);
        $this->assertTrue($matrizA->somar($matrizB)->igual(new Matriz([[6,8],[10,12]])));
    }

    public function testSomarBorda1x1(): void {
        $this->assertTrue((new Matriz([[5]]))->somar(new Matriz([[3]]))->igual(new Matriz([[8]])));
    }

    public function testSomarComNulaEIdentidade(): void {
        $matriz = new Matriz([[1,2],[3,4]]); $matrizNula = Matriz::nula(2,2); $matrizIdentidade = Matriz::identidade(2);
        $this->assertTrue($matriz->somar($matrizNula)->igual($matriz));
        $this->assertTrue($matriz->somar($matrizIdentidade)->igual(new Matriz([[2,2],[3,5]])));
    }

    public function testSomarErroDimensaoIncompativel(): void {
        $this->expectException(\RuntimeException::class);
        (new Matriz([[1,2]]))->somar(new Matriz([[1],[2]]));
    }

    public function testSubtrairCenarioFeliz(): void {
        $this->assertTrue((new Matriz([[5,6],[7,8]]))->subtrair(new Matriz([[1,2],[3,4]]))->igual(new Matriz([[4,4],[4,4]])));
    }

    public function testSubtrairBorda1x1(): void {
        $this->assertTrue((new Matriz([[5]]))->subtrair(new Matriz([[2]]))->igual(new Matriz([[3]])));
    }

    public function testSubtrairErroDimensao(): void {
        $this->expectException(\RuntimeException::class);
        (new Matriz([[1,2,3]]))->subtrair(new Matriz([[1,2]]));
    }

    public function testMultiplicarPorEscalar(): void {
        $this->assertTrue((new Matriz([[1,2],[3,4]]))->multiplicarPorEscalar(2)->igual(new Matriz([[2,4],[6,8]])));
        $this->assertTrue((new Matriz([[1]]))->multiplicarPorEscalar(0)->igual(new Matriz([[0]])));
        $this->assertTrue((new Matriz([[1,2]]))->multiplicarPorEscalar(-1)->igual(new Matriz([[-1,-2]])));
    }

    public function testMultiplicarCenarioFeliz2x2(): void {
        $matrizA = new Matriz([[1,2],[3,4]]); $matrizB = new Matriz([[5,6],[7,8]]);
        $this->assertTrue($matrizA->multiplicar($matrizB)->igual(new Matriz([[19,22],[43,50]])));
    }

    public function testMultiplicarRetangular(): void {
        $matrizA = new Matriz([[1,2,3],[4,5,6]]); $matrizB = new Matriz([[7,8],[9,10],[11,12]]);
        $this->assertTrue($matrizA->multiplicar($matrizB)->igual(new Matriz([[58,64],[139,154]])));
    }

    public function testMultiplicar1x1(): void {
        $this->assertTrue((new Matriz([[3]]))->multiplicar(new Matriz([[4]]))->igual(new Matriz([[12]])));
    }

    public function testMultiplicarComNulaEIdentidade(): void {
        $matriz = new Matriz([[1,2],[3,4]]); $matrizNula = Matriz::nula(2,2); $matrizIdentidade = Matriz::identidade(2);
        $this->assertTrue($matriz->multiplicar($matrizNula)->igual($matrizNula));
        $this->assertTrue($matriz->multiplicar($matrizIdentidade)->igual($matriz));
        $this->assertTrue($matrizIdentidade->multiplicar($matriz)->igual($matriz));
    }

    public function testMultiplicarErroDimensao(): void {
        $this->expectException(\RuntimeException::class);
        (new Matriz([[1,2,3]]))->multiplicar(new Matriz([[1,2],[3,4]]));
    }

    public function testTransporCenarioFeliz(): void {
        $this->assertTrue((new Matriz([[1,2,3],[4,5,6]]))->transpor()->igual(new Matriz([[1,4],[2,5],[3,6]])));
    }

    public function testTransporBorda1x1EIdentidade(): void {
        $this->assertTrue((new Matriz([[7]]))->transpor()->igual(new Matriz([[7]])));
        $matrizIdentidade = Matriz::identidade(3);
        $this->assertTrue($matrizIdentidade->transpor()->igual($matrizIdentidade));
    }

    public function testTransporNula(): void {
        $this->assertTrue(Matriz::nula(2,3)->transpor()->igual(Matriz::nula(3,2)));
    }

    public function testDeterminanteCenarioFeliz(): void {
        $this->assertEqualsWithDelta(-2.0, (new Matriz([[1,2],[3,4]]))->determinante(), 1e-9);
        $this->assertEqualsWithDelta(1.0, (new Matriz([[1,2,3],[0,1,4],[5,6,0]]))->determinante(), 1e-9);
    }

    public function testDeterminante1x1(): void {
        $this->assertEqualsWithDelta(5.0, (new Matriz([[5]]))->determinante(), 1e-9);
    }

    public function testDeterminanteIdentidadeENula(): void {
        $this->assertEqualsWithDelta(1.0, Matriz::identidade(3)->determinante(), 1e-9);
        $this->assertEqualsWithDelta(0.0, Matriz::nula(2,2)->determinante(), 1e-9);
    }

    public function testDeterminanteSingular(): void {
        $this->assertEqualsWithDelta(0.0, (new Matriz([[1,2],[2,4]]))->determinante(), 1e-9);
    }

    public function testDeterminanteErroNaoQuadrada(): void {
        $this->expectException(\RuntimeException::class);
        (new Matriz([[1,2,3],[4,5,6]]))->determinante();
    }

    public function testDeterminantePrecisao(): void {
        $matriz = new Matriz([[0.1,0.2],[0.3,0.4]]);
        $this->assertEqualsWithDelta(-0.02, $matriz->determinante(), 1e-9);
    }

    public function testInversaCenarioFeliz2x2(): void {
        $matriz = new Matriz([[4,7],[2,6]]); $inversa = $matriz->inversa();
        $this->assertEqualsWithDelta(0.6, $inversa->obter(0,0), 1e-9);
        $this->assertEqualsWithDelta(-0.7, $inversa->obter(0,1), 1e-9);
        $this->assertTrue($matriz->multiplicar($inversa)->igual(Matriz::identidade(2), 1e-8));
    }

    public function testInversa1x1(): void {
        $this->assertTrue((new Matriz([[4]]))->inversa()->igual(new Matriz([[0.25]]), 1e-9));
    }

    public function testInversaIdentidade(): void {
        $matrizIdentidade = Matriz::identidade(3);
        $this->assertTrue($matrizIdentidade->inversa()->igual($matrizIdentidade, 1e-9));
    }

    public function testInversaErroSingular(): void {
        $this->expectException(\RuntimeException::class);
        (new Matriz([[1,2],[2,4]]))->inversa();
    }

    public function testInversaErroNaoQuadrada(): void {
        $this->expectException(\RuntimeException::class);
        (new Matriz([[1,2,3],[4,5,6]]))->inversa();
    }

    public function testIgualComTolerancia(): void {
        $matrizA = new Matriz([[0.1+0.2]]); $matrizB = new Matriz([[0.3]]);
        $this->assertTrue($matrizA->igual($matrizB, 1e-9));
        $this->assertFalse($matrizA->igual(new Matriz([[0.31]]), 1e-9));
        $this->assertFalse($matrizA->igual(new Matriz([[0.3,0]])));
    }

    public function testConstrutorErroVazia(): void {
        $this->expectException(\InvalidArgumentException::class); new Matriz([]);
    }

    public function testConstrutorErroDesalinhada(): void {
        $this->expectException(\InvalidArgumentException::class); new Matriz([[1,2],[3]]);
    }

    public function testConstrutorErroNaoNumerico(): void {
        $this->expectException(\InvalidArgumentException::class); new Matriz([[1,'a']]);
    }
}
