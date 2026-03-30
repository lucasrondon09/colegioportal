# 🔧 Patch para Compatibilidade com PHP 8.3

## Problema

A biblioteca `eduardokum/laravel-boleto` usa a função `utf8_decode()` que foi **removida no PHP 8.2+**.

**Erro**: `Function utf8_decode() is deprecated`

---

## ✅ Solução Aplicada

Substituímos `utf8_decode()` por `mb_convert_encoding()` no arquivo:

**Arquivo**: `vendor/eduardokum/laravel-boleto/src/Boleto/Render/AbstractPdf.php`  
**Linha**: 113

### **Antes:**
```php
$var  = utf8_decode(array_shift($args));
```

### **Depois:**
```php
$var  = mb_convert_encoding(array_shift($args), 'ISO-8859-1', 'UTF-8');
```

---

## 🚀 Como Aplicar o Patch

### **Opção 1: Aplicar Manualmente**

1. Abra o arquivo:
   ```
   C:\wamp64\www\portal\vendor\eduardokum\laravel-boleto\src\Boleto\Render\AbstractPdf.php
   ```

2. Localize a linha 113:
   ```php
   $var  = utf8_decode(array_shift($args));
   ```

3. Substitua por:
   ```php
   $var  = mb_convert_encoding(array_shift($args), 'ISO-8859-1', 'UTF-8');
   ```

4. Salve o arquivo

### **Opção 2: Usar Script PowerShell (Windows)**

Crie um arquivo `patch_boleto.ps1` com o conteúdo:

```powershell
$file = "C:\wamp64\www\portal\vendor\eduardokum\laravel-boleto\src\Boleto\Render\AbstractPdf.php"
$content = Get-Content $file -Raw
$content = $content -replace 'utf8_decode\(array_shift\(\$args\)\)', 'mb_convert_encoding(array_shift($args), ''ISO-8859-1'', ''UTF-8'')'
Set-Content $file $content
Write-Host "Patch aplicado com sucesso!" -ForegroundColor Green
```

Execute no PowerShell:
```powershell
.\patch_boleto.ps1
```

### **Opção 3: Usar Script Bash (Linux/Mac)**

```bash
#!/bin/bash
FILE="vendor/eduardokum/laravel-boleto/src/Boleto/Render/AbstractPdf.php"
sed -i 's/utf8_decode(array_shift($args))/mb_convert_encoding(array_shift($args), '\''ISO-8859-1'\'', '\''UTF-8'\'')/g' "$FILE"
echo "Patch aplicado com sucesso!"
```

---

## ⚠️ Quando Aplicar

Você precisa aplicar este patch:

1. **Após instalar as dependências**: `composer install`
2. **Após atualizar as dependências**: `composer update`
3. **Sempre que reinstalar o vendor**

---

## 🔍 Verificar se o Patch Foi Aplicado

Execute no phpMyAdmin ou terminal:

```bash
grep -n "mb_convert_encoding" vendor/eduardokum/laravel-boleto/src/Boleto/Render/AbstractPdf.php
```

Deve retornar:
```
113:        $var  = mb_convert_encoding(array_shift($args), 'ISO-8859-1', 'UTF-8');
```

---

## 📝 Alternativa: Atualizar a Biblioteca

Verifique se há uma versão mais recente da biblioteca que já suporte PHP 8.3:

```bash
composer show eduardokum/laravel-boleto
composer update eduardokum/laravel-boleto
```

---

## 🎯 Resultado

Após aplicar o patch:
- ✅ Boletos serão gerados sem erros
- ✅ Compatível com PHP 8.3
- ✅ Função `mb_convert_encoding()` é nativa e não será removida

---

**Status**: ✅ Patch aplicado com sucesso no repositório
