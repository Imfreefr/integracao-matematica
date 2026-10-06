# Relatório Técnico — Álgebra Linear em PHP

## 1. Lógica dos Algoritmos

**Representação:** `Matriz` armazena `float[][]` validado no construtor (retangular, numérico, não vazio). Imutável: operações retornam nova instância.

- **somar/subtrair:** O(n·m), verifica dimensões idênticas via `garantirMesmasDimensoes`.
- **multiplicarPorEscalar:** O(n·m).
- **multiplicar:** O(n·p·m) triplo loop, verifica `colunas == linhas` da outra.
- **transpor:** O(n·m).
- **determinante:** Eliminação de Gauss com pivoteamento parcial, produto da diagonal × sinal de trocas. O(n³). Retorna 0 se `|linhaPivo| < 1e-12`.
- **inversa:** Gauss-Jordan em matriz ampliada [A|I], normaliza `divisor` e zera coluna com `fator`. O(n³). Lança `RuntimeException` se singular.
- **SistemaLinear::resolver:** Gauss com pivoteamento + substituição regressiva. Classifica: `linhaNula` com termo ≠0 → impossível; caso contrário indeterminado/singular. `resolverMatriz` resolve múltiplos RHS coluna a coluna.

Precisão: comparações com `abs()<1e-12`, testes usam `assertEqualsWithDelta(1e-9)` e `igual(tolerancia)`.

## 2. Decisões de Design

- Sem dependências externas (stdlib puro) — YAGNI, leve para Herd.
- Exceções nativas: `InvalidArgumentException` (validação) e `RuntimeException` (incompatível/singular).
- `final class`, `strict_types`, `psr-4` (`App\` → `src/`).
- Interface web em `public/index.php` único arquivo, Bootstrap CDN, `interpretarMatriz`/`interpretarVetor` flexíveis (JSON ou `1,2;3,4`).
- Testes `MatrizTeste`/`SistemaLinearTeste` com PHPUnit 11, `suffix="Teste.php"`.

## 3. Dificuldades e Soluções

- **Pivô nulo:** busca de maior `|linhaPivo|` na coluna e troca de linhas.
- **Precisão float:** `0.1+0.2 != 0.3`; uso de `tolerancia` em `igual()` e asserts.
- **Determinante:** corrigido para produto pós-triangularização.
- **Renomeação PT:** ajuste de `phpunit.xml` para `suffix="Teste.php"`.

## 4. Cobertura

`vendor/bin/phpunit --coverage-text` >90% em `src/` (requer Xdebug/PCOV). Todos os ramos de erro testados.
