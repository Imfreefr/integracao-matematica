# Álgebra Linear — PHP + PHPUnit

Aplicação PHP puro para operações matriciais e resolução de sistemas lineares.

## Estrutura

- `app/Models`: classes `Matriz` e `SistemaLinear`.
- `app/Controllers`: processamento das entradas e operações da calculadora.
- `app/Views`: apresentação HTML da aplicação.
- `index.php`: ponto de entrada na raiz do projeto.

## Requisitos e execução

PHP >=8.4 e Composer.

```bash
composer install
php -S localhost:8000
```

Acesse `http://localhost:8000`.

## Testes

```bash
vendor/bin/phpunit --testdox
```

As matrizes aceitam JSON (`[[1,2],[3,4]]`) ou linhas separadas por ponto e vírgula (`1,2;3,4`).
