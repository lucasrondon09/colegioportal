# 📋 Instalação - Campos de Configuração de Boleto por Lançamento

## ⚠️ IMPORTANTE: Execute o SQL Antes de Testar!

Antes de usar a nova funcionalidade, você **DEVE** executar o script SQL para adicionar as colunas na tabela de parcelas.

---

## 🔧 Passo a Passo

### 1. **Executar o Script SQL**

1. Abra o **phpMyAdmin** no WAMP
2. Selecione o banco de dados `colegioportal`
3. Clique na aba **"SQL"**
4. Abra o arquivo `ALTER_PARCELAS_ADD_BOLETO_CONFIG.sql`
5. Copie todo o conteúdo
6. Cole no phpMyAdmin
7. Clique em **"Executar"**

### 2. **Verificar se as Colunas Foram Criadas**

Execute este comando no phpMyAdmin para verificar:

```sql
SHOW COLUMNS FROM si_parcelas;
```

Você deve ver as novas colunas:
- `juros_percentual`
- `multa_percentual`
- `multa_apos_dias`
- `desconto_percentual`
- `nao_receber_apos_dias`
- `protestar_apos_dias`
- `aceite`
- `tipo_titulo`
- `mensagem_banco`

### 3. **Fazer Git Pull**

```bash
cd C:\wamp64\www\portal
git pull origin main
```

### 4. **Testar a Funcionalidade**

1. Acesse: **Admin → Contratos → Lançamentos**
2. Clique em **"Cadastrar Lançamento"**
3. Preencha os dados básicos
4. **Veja a nova seção**: "Configurações do Boleto"
5. Os campos estarão pré-preenchidos com valores padrão
6. **Personalize** se desejar
7. Marque **"Registrar Boletos"**
8. Clique em **"Salvar"**

---

## 🎯 Como Funciona

### **Fluxo de Configuração**

```
1. Configuração Global (si_boleto_config)
   ↓
2. Pré-preenche Formulário de Lançamento
   ↓
3. Usuário Pode Editar/Personalizar
   ↓
4. Salva na Parcela (si_parcelas)
   ↓
5. Boleto Usa Configuração da Parcela
```

### **Prioridade de Valores**

```php
// BoletoService verifica nesta ordem:
1. Configuração da Parcela (se existir)
2. Configuração Global (fallback)
3. Valores Padrão (se nada existir)
```

---

## 📊 Exemplo de Uso

### **Cenário 1: Usar Valores Padrão**
- Não altere nada no formulário
- Os valores da configuração global serão usados

### **Cenário 2: Personalizar um Lançamento**
- Exemplo: Aluno com bolsa → Juros 0%, Multa 0%
- Altere apenas os campos desejados
- Outros campos mantêm valores padrão

### **Cenário 3: Lançamento Especial**
- Exemplo: Taxa de matrícula → Mensagem diferente
- Personalize a "Mensagem do Banco"
- Ajuste "Não Receber Após" para 15 dias

---

## 🔍 Verificar se Está Funcionando

### **Teste 1: Campos Aparecem no Formulário**
✅ Acesse Cadastro de Lançamento  
✅ Veja seção "Configurações do Boleto"  
✅ Campos pré-preenchidos com valores

### **Teste 2: Valores São Salvos**
✅ Altere um valor (ex: juros para 1.50)  
✅ Salve o lançamento  
✅ Verifique no banco: `SELECT juros_percentual FROM si_parcelas WHERE id = X`

### **Teste 3: Boleto Usa Valores Corretos**
✅ Gere o boleto  
✅ Verifique as instruções no PDF  
✅ Confirme que usa os valores personalizados

---

## ❌ Problemas Comuns

### **Erro: "Unknown column 'juros_percentual'"**
**Causa**: SQL não foi executado  
**Solução**: Execute `ALTER_PARCELAS_ADD_BOLETO_CONFIG.sql`

### **Campos Não Aparecem no Formulário**
**Causa**: Código não foi atualizado  
**Solução**: Faça `git pull` novamente

### **Valores Não São Salvos**
**Causa**: Permissões do banco ou cache  
**Solução**: Limpe o cache do CodeIgniter em `writable/cache/`

---

## 📝 Observações

- ✅ Configuração Global continua funcionando
- ✅ Lançamentos antigos usam configuração global
- ✅ Novos lançamentos podem ser personalizados
- ✅ Cada parcela pode ter configurações diferentes
- ✅ Valores NULL usam configuração global como fallback

---

## 🎉 Pronto!

Agora você pode personalizar as configurações de boleto para cada lançamento individualmente!

**Dúvidas?** Verifique os logs em `writable/logs/`
