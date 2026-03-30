# 🚀 CNAB 240 - Guia de Instalação Rápida

## ✅ Passo 1: Executar SQL no phpMyAdmin

Execute o arquivo: **CREATE_CNAB_TABLES_FIXED.sql**

Este script cria 5 tabelas:
- `si_cnab_remessa`
- `si_cnab_remessa_detalhe`
- `si_cnab_retorno`
- `si_cnab_retorno_detalhe`
- `si_cnab_log`

---

## ✅ Passo 2: Atualizar Código do GitHub

```bash
cd C:\wamp64\www\colegioportal
git pull origin main
```

Ou faça o download manual dos arquivos atualizados.

---

## ✅ Passo 3: Verificar Arquivos Criados

### **Models** (app/Models/)
- SI_CnabRemessaModel.php
- SI_CnabRemessaDetalheModel.php
- SI_CnabRetornoModel.php
- SI_CnabRetornoDetalheModel.php
- SI_CnabLogModel.php

### **Controllers** (app/Controllers/Admin/)
- SI_CnabRemessa.php
- SI_CnabRetorno.php

### **Libraries** (app/Libraries/)
- CnabService.php

### **Views** (app/Views/admin/)
- CnabRemessa/index.php
- CnabRemessa/nova.php
- CnabRemessa/visualizar.php
- CnabRetorno/index.php
- CnabRetorno/upload.php
- CnabRetorno/visualizar.php

### **Rotas** (app/Config/Routes.php)
- Rotas CNAB adicionadas automaticamente

### **Menu** (app/Views/admin/template/sidebar.php)
- Menu "CNAB 240" adicionado com submenus

---

## ✅ Passo 4: Verificar Configurações no .env

Certifique-se de que as configurações da Caixa estão corretas:

```env
caixa.codigo_banco=104
caixa.razao_social=SOCIEDADE EDUC PORTAL DO ITALIA LTDA
caixa.numero_inscricao=00976721000131
caixa.agencia=1681
caixa.agencia_dv=0
caixa.conta=000000
caixa.conta_dv=0
caixa.codigo_beneficiario=592947
caixa.codigo_beneficiario_dv=0
caixa.carteira=RG
caixa.dir_remessas=remessas/
caixa.dir_retornos=retornos/
```

---

## ✅ Passo 5: Criar Diretórios

Os diretórios são criados automaticamente, mas você pode criá-los manualmente:

```
writable/remessas/
writable/retornos/
```

Certifique-se de que têm permissão de escrita.

---

## ✅ Passo 6: Acessar o Sistema

1. Acesse: **http://local.portal/Admin**
2. Faça login
3. No menu lateral, procure por: **CNAB 240**
4. Você verá dois submenus:
   - **Remessas** (para enviar ao banco)
   - **Retornos** (para processar do banco)

---

## 🎯 Teste Rápido

### **Teste 1: Gerar Remessa**
1. Acesse: **CNAB 240 → Remessas → Nova Remessa**
2. Se houver parcelas com boletos gerados, elas aparecerão na lista
3. Selecione algumas parcelas
4. Clique em "Gerar Arquivo de Remessa"
5. Faça download do arquivo .REM

### **Teste 2: Visualizar Remessas**
1. Acesse: **CNAB 240 → Remessas**
2. Você verá a lista de remessas geradas
3. Clique em "Visualizar" para ver detalhes

### **Teste 3: Processar Retorno**
1. Acesse: **CNAB 240 → Retornos → Processar Retorno**
2. A tela de upload aparecerá
3. (Aguarde ter um arquivo de retorno real do banco para testar)

---

## 🐛 Problemas Comuns

### **Erro: "Class 'App\Models\SI_ParcelasContratoModel' not found"**
- **Solução:** Certifique-se de que o model `SI_ParcelasContratoModel` existe

### **Erro: "Unable to locate the model"**
- **Solução:** Verifique se todos os arquivos foram copiados corretamente

### **Erro: "404 - Controller or its method is not found"**
- **Solução:** Limpe o cache do CodeIgniter:
  ```bash
  php spark cache:clear
  ```

### **Menu não aparece**
- **Solução:** Limpe o cache do navegador (Ctrl+F5)

---

## 📋 Checklist Final

- [ ] SQL executado com sucesso
- [ ] Código atualizado do GitHub
- [ ] Arquivos verificados
- [ ] Configurações do .env corretas
- [ ] Diretórios criados
- [ ] Sistema acessível
- [ ] Menu "CNAB 240" visível
- [ ] Teste de geração de remessa OK

---

## 📞 Próximos Passos

Após a instalação:

1. **Gere uma remessa de teste** com 1 ou 2 boletos
2. **Envie ao banco** via Internet Banking
3. **Aguarde o retorno** do banco (geralmente 1-2 dias úteis)
4. **Processe o retorno** no sistema
5. **Verifique** se as parcelas foram atualizadas

---

## 📚 Documentação Completa

Para mais detalhes, consulte: **CNAB_240_DOCUMENTACAO_COMPLETA.md**

---

**Instalação concluída!** ✅  
**Qualquer dúvida, consulte a documentação ou entre em contato.**
