# Patches do Projeto

## laravel-boleto-php83-fix.patch

**Problema:** A biblioteca `eduardokum/laravel-boleto` v0.10.1 não é totalmente compatível com PHP 8.3. O método `count()` da classe `AbstractRetorno` não possui a declaração de tipo de retorno correta, causando erro fatal ao processar arquivos de retorno CNAB.

**Solução:** Adiciona o atributo `#[\ReturnTypeWillChange]` ao método `count()` para suprimir o aviso de incompatibilidade de tipo de retorno.

**Aplicação:** O patch é aplicado automaticamente pelo `cweagans/composer-patches` durante a instalação das dependências com `composer install` ou `composer update`.

**Arquivo modificado:** `vendor/eduardokum/laravel-boleto/src/Cnab/Retorno/AbstractRetorno.php`

**Data:** 09/12/2025
