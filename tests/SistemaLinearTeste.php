<?php
declare(strict_types=1);
namespace Tests;

use App\Models\SistemaLinear;
use App\Models\Matriz;
use PHPUnit\Framework\TestCase;

final class SistemaLinearTeste extends TestCase
{
    public function testResolverCenarioFeliz2x2(): void {
        $matrizCoeficientes = new Matriz([[2,1],[1,3]]); $vetorTermos = [5,6];
        $solucao = SistemaLinear::resolver($matrizCoeficientes, $vetorTermos);
        $this->assertEqualsWithDelta(1.8, $solucao[0], 1e-9);
        $this->assertEqualsWithDelta(1.4, $solucao[1], 1e-9);
    }

    public function testResolverCenarioFeliz3x3(): void {
        $matrizCoeficientes = new Matriz([[1,2,3],[0,1,4],[5,6,0]]); $vetorTermos = [14,14,17];
        $solucao = SistemaLinear::resolver($matrizCoeficientes, $vetorTermos);
        $this->assertEqualsWithDelta(1.0, $solucao[0], 1e-9);
        $this->assertEqualsWithDelta(2.0, $solucao[1], 1e-9);
        $this->assertEqualsWithDelta(3.0, $solucao[2], 1e-9);
    }

    public function testResolver1x1(): void {
        $solucao = SistemaLinear::resolver(new Matriz([[4]]), [8]);
        $this->assertEqualsWithDelta(2.0, $solucao[0], 1e-9);
    }

    public function testResolverIdentidade(): void {
        $matrizCoeficientes = Matriz::identidade(3); $vetorTermos = [7,8,9];
        $this->assertEquals([7.0,8.0,9.0], SistemaLinear::resolver($matrizCoeficientes, $vetorTermos));
    }

    public function testResolverVetorNulo(): void {
        $solucao = SistemaLinear::resolver(Matriz::identidade(2), [0,0]);
        $this->assertEqualsWithDelta(0.0,$solucao[0],1e-9);
        $this->assertEqualsWithDelta(0.0,$solucao[1],1e-9);
    }

    public function testResolverPrecisaoFloat(): void {
        $matrizCoeficientes = new Matriz([[0.1,0.2],[0.3,0.4]]); $vetorTermos = [0.5,1.1];
        $solucao = SistemaLinear::resolver($matrizCoeficientes, $vetorTermos);
        $this->assertEqualsWithDelta(1.0,$solucao[0],1e-9);
        $this->assertEqualsWithDelta(2.0,$solucao[1],1e-9);
    }

    public function testResolverPrecisaPivotear(): void {
        $matrizCoeficientes = new Matriz([[0,1],[1,0]]); $vetorTermos = [5,7];
        $solucao = SistemaLinear::resolver($matrizCoeficientes, $vetorTermos);
        $this->assertEqualsWithDelta(7.0,$solucao[0],1e-9);
        $this->assertEqualsWithDelta(5.0,$solucao[1],1e-9);
    }

    public function testResolverErroSingular(): void {
        $this->expectException(\RuntimeException::class);
        SistemaLinear::resolver(new Matriz([[1,2],[2,4]]), [3,6]);
    }

    public function testResolverErroImpossivel(): void {
        $this->expectException(\RuntimeException::class);
        SistemaLinear::resolver(new Matriz([[1,2],[2,4]]), [3,7]);
    }

    public function testResolverErroDimensaoIncompativel(): void {
        $this->expectException(\RuntimeException::class);
        SistemaLinear::resolver(new Matriz([[1,2],[3,4]]), [1,2,3]);
    }

    public function testResolverErroNaoQuadrada(): void {
        $this->expectException(\RuntimeException::class);
        SistemaLinear::resolver(new Matriz([[1,2,3],[4,5,6]]), [1,2]);
    }

    public function testResolverMatrizMultiplosTermos(): void {
        $matrizCoeficientes = new Matriz([[2,1],[1,3]]); $matrizTermos = new Matriz([[5,6],[6,9]]);
        $matrizSolucao = SistemaLinear::resolverMatriz($matrizCoeficientes, $matrizTermos);
        $this->assertTrue($matrizCoeficientes->multiplicar($matrizSolucao)->igual($matrizTermos,1e-9));
    }

    public function testResolverMatrizErroDimensao(): void {
        $this->expectException(\RuntimeException::class);
        SistemaLinear::resolverMatriz(new Matriz([[1,2],[3,4]]), new Matriz([[1],[2],[3]]));
    }
}
