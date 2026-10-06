# Álgebra Linear — PHP + PHPUnit

Aplicação web em PHP puro para operações matriciais e resolução de sistemas lineares.

## Objetivos e algoritmos

O projeto implementa:

- Soma e subtração de matrizes.
- Multiplicação de matriz por escalar.
- Multiplicação de matrizes.
- Transposição.
- Determinante por eliminação de Gauss com pivoteamento parcial.
- Matriz inversa por Gauss-Jordan.
- Resolução de sistemas lineares por eliminação de Gauss e substituição regressiva.
- Resolução de sistemas com múltiplos vetores de termos independentes.

## Estrutura

- `app/Models`: regras matemáticas (`Matriz` e `SistemaLinear`).
- `app/Controllers`: entrada de dados, seleção da operação e formatação dos resultados.
- `app/Views`: interface HTML da calculadora.
- `index.php`: ponto de entrada na raiz do projeto.
- `tests`: testes unitários PHPUnit.
- `css`: estilos da interface.

## Ferramentas

PHP >= 8.4, HTML5, CSS3, Bootstrap 5, Composer, PHPUnit, Git e GitHub.

## Instalação

Na pasta do projeto, execute:

```bash
composer install
```

Isso instala o PHPUnit e cria o executável `vendor/bin/phpunit`.

## Execução com PHP ou Laravel Herd

Para executar com o servidor interno do PHP:

```bash
php -S localhost:8000
```

Acesse `http://localhost:8000`.

Com Laravel Herd, adicione ou vincule a pasta do projeto ao Herd. Como o `index.php` está na raiz, o endereço fornecido pelo Herd abrirá diretamente a aplicação.

## Testes unitários

Execute todos os testes com:

```bash
vendor/bin/phpunit --testdox
```

Os testes cobrem casos felizes, matrizes 1x1, identidade e nula, dimensões incompatíveis, matrizes singulares, sistemas impossíveis ou indeterminados e comparações de ponto flutuante com `assertEqualsWithDelta()`.

## Cobertura

O PHPUnit considera somente o código dos algoritmos em `app` por meio do `phpunit.xml`.

Para gerar o relatório no terminal:

```bash
vendor/bin/phpunit --coverage-text
```

Para gerar o relatório HTML:

```bash
vendor/bin/phpunit --coverage-html coverage
```

O relatório HTML será criado em `coverage/index.html`. A cobertura deve ser de pelo menos 80% do código dos algoritmos, sem considerar a interface web.

## Formato das entradas

As matrizes aceitam JSON, por exemplo:

```text
[[1,2],[3,4]]
```

Também aceitam linhas separadas por ponto e vírgula:

```text
1,2;3,4
```

Para sistemas lineares, o vetor `b` pode ser informado como `[5,6]` ou `5,6`.
