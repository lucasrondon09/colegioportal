# Instalação do Sistema de Configurações de Boleto

## 📋 Passo a Passo

### 1. Criar a Tabela no Banco de Dados

1. Abra o **phpMyAdmin** no WAMP (http://localhost/phpmyadmin)
2. Selecione o banco de dados do **ColégioPortal**
3. Clique na aba **SQL**
4. Copie e cole o conteúdo do arquivo `CREATE_BOLETO_CONFIG_TABLE.sql`
5. Clique em **Executar**
6. Verifique se a mensagem de sucesso aparece

### 2. Verificar Arquivos Criados

Certifique-se de que os seguintes arquivos foram criados no projeto:

#### Migration (para referência futura)
- ✅ `app/Database/Migrations/2025-12-05-120000_CreateBoletoConfig.php`

#### Model
- ✅ `app/Models/BoletoConfigModel.php`

#### Controller
- ✅ `app/Controllers/Admin/BoletoConfig.php`

#### View
- ✅ `app/Views/admin/BoletoConfig/index.php`

#### Biblioteca (modificada)
- ✅ `app/Libraries/BoletoService.php` (atualizado para usar configurações do banco)

#### Rotas (modificadas)
- ✅ `app/Config/Routes.php` (adicionadas rotas de configuração)

### 3. Acessar a Tela de Configurações

Após criar a tabela, acesse:

```
http://localhost/portal/Admin/boleto-config
```

Ou adicione um link no menu do sistema (veja instruções abaixo).

### 4. Adicionar Link no Menu (Opcional)

Para facilitar o acesso, adicione um link no menu lateral do sistema administrativo.

**Localizar o arquivo do menu:**
- Geralmente está em: `app/Views/admin/layout/sidebar.php` ou similar

**Adicionar o seguinte código no menu:**

```php
<!-- Menu de Configurações de Boleto -->
<li class="nav-item">
    <a href="<?= base_url('Admin/boleto-config') ?>" class="nav-link">
        <i class="nav-icon fas fa-cog"></i>
        <p>Configurações de Boleto</p>
    </a>
</li>
```

## 🎯 Funcionalidades Implementadas

### Configurações Disponíveis

1. **Juros e Multa**
   - Percentual de juros ao mês
   - Percentual de multa
   - Dias para aplicar multa
   - Desconto até o vencimento

2. **Instruções Bancárias**
   - Não receber após X dias
   - Protestar após X dias
   - Aceite (SIM/NÃO)
   - Tipo de título (DM, DS, NP, RC, OU)

3. **Mensagens Personalizadas**
   - Mensagem principal
   - 3 instruções adicionais customizadas

### Como Funciona

1. **Valores Padrão**: O sistema já vem com valores pré-configurados (juros 1%, multa 2%, não receber após 29 dias)

2. **Edição Fácil**: Acesse a tela de configurações, edite os valores desejados e clique em "Salvar"

3. **Aplicação Automática**: Todos os novos boletos gerados usarão automaticamente as configurações salvas

4. **Instruções Dinâmicas**: As instruções no boleto são geradas automaticamente baseadas nas configurações

## 🔧 Detalhes Técnicos

### Estrutura da Tabela

```sql
si_boleto_config
├── id (PK)
├── juros_percentual (DECIMAL 5,2)
├── multa_percentual (DECIMAL 5,2)
├── multa_apos_dias (INT 3)
├── desconto_percentual (DECIMAL 5,2)
├── nao_receber_apos_dias (INT 3)
├── aceite (ENUM 'S','N')
├── protestar_apos_dias (INT 3)
├── tipo_titulo (VARCHAR 2)
├── mensagem_banco (TEXT)
├── instrucao_1 (VARCHAR 255)
├── instrucao_2 (VARCHAR 255)
├── instrucao_3 (VARCHAR 255)
├── ativo (TINYINT 1)
├── created_at (DATETIME)
└── updated_at (DATETIME)
```

### Integração com BoletoService

O `BoletoService.php` foi modificado para:

1. **Carregar configurações do banco** no construtor
2. **Usar configurações dinâmicas** ao invés de valores hardcoded
3. **Gerar instruções automaticamente** baseado nas configurações
4. **Calcular juros e multa** conforme percentuais configurados

### Rotas Criadas

```php
GET  /Admin/boleto-config           → Exibe tela de configurações
POST /Admin/boleto-config/salvar    → Salva configurações
GET  /Admin/boleto-config/testar    → Gera boleto de teste (em desenvolvimento)
```

## ✅ Validações

O sistema valida:

- ✅ Percentuais entre 0 e 100
- ✅ Dias devem ser números inteiros positivos
- ✅ Aceite deve ser S ou N
- ✅ Tipo de título deve ter 2 caracteres
- ✅ Mensagens respeitam limites de caracteres

## 🎨 Interface

A tela de configurações foi desenvolvida com:

- **Layout responsivo** (2 colunas)
- **Cards organizados** por categoria
- **Validação em tempo real**
- **Mensagens de sucesso/erro**
- **Informações de ajuda** (tooltips)
- **Design consistente** com o restante do sistema

## 📝 Próximos Passos

Após a instalação e teste das configurações:

1. ✅ Testar geração de boleto com novas configurações
2. ✅ Verificar se instruções aparecem corretamente
3. ✅ Validar cálculos de juros e multa
4. 🔄 Implementar função de "Gerar Boleto de Teste"
5. 🔄 Prosseguir para **Fase 3: CNAB Remessa e Retorno**

## 🐛 Troubleshooting

### Erro: Tabela não encontrada
- Verifique se executou o SQL no banco correto
- Confirme que a tabela `si_boleto_config` existe

### Erro: Página não encontrada (404)
- Verifique se as rotas foram adicionadas em `app/Config/Routes.php`
- Limpe o cache do CodeIgniter: `php spark cache:clear`

### Erro: Configurações não aparecem
- Verifique se há um registro na tabela com `ativo = 1`
- Execute novamente o INSERT do SQL

## 📞 Suporte

Em caso de dúvidas ou problemas:
1. Verifique os logs em `writable/logs/`
2. Consulte a documentação do CodeIgniter 4
3. Revise este README
