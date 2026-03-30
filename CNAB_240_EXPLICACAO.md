# 📘 CNAB 240 - Explicação Completa

## O que é CNAB?

**CNAB** = **Centro Nacional de Automação Bancária**

É um padrão de arquivo usado para comunicação entre empresas e bancos no Brasil para:
- Enviar boletos para registro no banco (**Remessa**)
- Receber informações de pagamentos (**Retorno**)

---

## 🔄 Fluxo Completo do CNAB

```
┌─────────────────────────────────────────────────────────────┐
│                    SISTEMA COLÉGIOPORTAL                    │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ 1. Gera Boletos
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              ARQUIVO DE REMESSA (.REM)                      │
│  - Lista de boletos para registrar no banco                 │
│  - Formato: CNAB 240 (240 caracteres por linha)             │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ 2. Upload no Internet Banking
                           ↓
┌─────────────────────────────────────────────────────────────┐
│                    BANCO (Caixa, BB, etc)                   │
│  - Registra os boletos                                      │
│  - Processa pagamentos                                      │
│  - Gera arquivo de retorno                                  │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ 3. Download do Internet Banking
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              ARQUIVO DE RETORNO (.RET)                      │
│  - Lista de pagamentos realizados                           │
│  - Baixas, liquidações, erros                               │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ 4. Importa no Sistema
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              SISTEMA ATUALIZA AUTOMATICAMENTE               │
│  - Marca parcelas como pagas                                │
│  - Registra data de pagamento                               │
│  - Atualiza status financeiro                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 📤 Arquivo de Remessa

### **O que contém:**
- Header do arquivo (dados da empresa)
- Header do lote (dados do convênio)
- Detalhes dos boletos (um por linha)
  - Nosso número
  - Valor
  - Vencimento
  - Dados do pagador
  - Instruções (juros, multa, etc)
- Trailer do lote (totalizadores)
- Trailer do arquivo (totalizadores gerais)

### **Quando gerar:**
- Diariamente (boletos do dia)
- Semanalmente (lote de boletos)
- Sob demanda (quando necessário)

### **Formato:**
```
CNAB 240 = 240 caracteres por linha
Cada campo tem posição e tamanho fixos
Exemplo:
001234567890123456789012345678901234567890... (240 caracteres)
```

---

## 📥 Arquivo de Retorno

### **O que contém:**
- Header do arquivo
- Header do lote
- Detalhes dos pagamentos
  - Nosso número (identifica o boleto)
  - Código de ocorrência (pago, baixado, erro, etc)
  - Data do pagamento
  - Valor pago
  - Tarifa bancária
- Trailer do lote
- Trailer do arquivo

### **Códigos de Ocorrência Comuns:**
- **02** - Entrada confirmada (boleto registrado)
- **06** - Liquidação (boleto pago)
- **09** - Baixa (boleto cancelado)
- **17** - Liquidação após baixa
- **28** - Débito de tarifas

### **Quando processar:**
- Diariamente (após baixar do banco)
- Automaticamente (via API, se disponível)

---

## 🎯 O que Vamos Implementar

### **Fase 3.1: Geração de Remessa** ✅
1. Tela para selecionar boletos
2. Botão "Gerar Arquivo de Remessa"
3. Download do arquivo .REM
4. Registro na tabela de controle

### **Fase 3.2: Processamento de Retorno** ✅
1. Tela para upload de arquivo .RET
2. Leitura e validação do arquivo
3. Atualização automática das parcelas
4. Relatório de processamento

### **Fase 3.3: Controle e Auditoria** ✅
1. Histórico de remessas enviadas
2. Histórico de retornos processados
3. Log de alterações nas parcelas
4. Conciliação bancária

---

## 🏦 Bancos Suportados

A biblioteca que vamos usar suporta:

- ✅ **Banco do Brasil** (001)
- ✅ **Caixa Econômica Federal** (104)
- ✅ **Bradesco** (237)
- ✅ **Itaú** (341)
- ✅ **Santander** (033)
- ✅ **Sicoob** (756)
- ✅ **Sicredi** (748)
- ✅ **Banrisul** (041)
- E outros...

**Seu banco**: Caixa Econômica Federal (104) ✅

---

## 📋 Requisitos do Banco

Para usar CNAB, você precisa:

### **1. Convênio Bancário**
- Número do convênio
- Carteira de cobrança
- Agência e conta

### **2. Acesso ao Internet Banking**
- Permissão para upload de remessa
- Permissão para download de retorno

### **3. Configurações**
- Código do cedente
- Número da operação (se aplicável)
- Variação da carteira (se aplicável)

---

## 🔒 Segurança

### **Cuidados Importantes:**
- ✅ Nunca gerar remessa duplicada
- ✅ Validar arquivo de retorno antes de processar
- ✅ Fazer backup antes de processar retorno
- ✅ Registrar todas as operações em log
- ✅ Não permitir reprocessamento do mesmo arquivo

---

## 🚀 Próximos Passos

1. **Instalar biblioteca CNAB** (cnab-php ou similar)
2. **Criar tabelas de controle**
3. **Implementar geração de remessa**
4. **Implementar processamento de retorno**
5. **Criar interface web**
6. **Testar com arquivo de exemplo**
7. **Testar com banco real** (homologação)

---

## 💡 Dicas

### **Para Testar:**
- Peça ao banco um arquivo de retorno de exemplo
- Use o ambiente de homologação do banco
- Gere remessas pequenas (poucos boletos) no início

### **Para Produção:**
- Configure rotina diária de geração de remessa
- Configure rotina diária de processamento de retorno
- Monitore erros e rejeições
- Mantenha histórico de arquivos por 5 anos (obrigação legal)

---

**Vamos começar?** 🎉
