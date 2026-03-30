<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBoletoConfig extends Migration
{
    public function up()
    {
        // Se a tabela já existe, pular a criação (evita erro ao rodar migrate em banco existente)
        if ($this->db->tableExists('si_boleto_config')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'juros_percentual' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 1.00,
                'comment'    => 'Percentual de juros ao mês'
            ],
            'multa_percentual' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 2.00,
                'comment'    => 'Percentual de multa'
            ],
            'multa_apos_dias' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 1,
                'comment'    => 'Aplicar multa após X dias do vencimento'
            ],
            'desconto_percentual' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'comment'    => 'Percentual de desconto até o vencimento'
            ],
            'nao_receber_apos_dias' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 29,
                'comment'    => 'Não receber após X dias de atraso'
            ],
            'aceite' => [
                'type'       => 'ENUM',
                'constraint' => ['S', 'N'],
                'default'    => 'N',
                'comment'    => 'Aceite do título (S=Sim, N=Não)'
            ],
            'protestar_apos_dias' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
                'comment'    => 'Protestar após X dias (0 = não protestar)'
            ],
            'tipo_titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'default'    => 'DM',
                'comment'    => 'Tipo de título (DM=Duplicata Mercantil, DS=Duplicata de Serviço, etc)'
            ],
            'mensagem_banco' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Mensagem customizada para o banco'
            ],
            'instrucao_1' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Instrução adicional 1'
            ],
            'instrucao_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Instrução adicional 2'
            ],
            'instrucao_3' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Instrução adicional 3'
            ],
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=Ativo, 0=Inativo'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('si_boleto_config');

        // Inserir configuração padrão
        $this->db->table('si_boleto_config')->insert([
            'juros_percentual'       => 1.00,
            'multa_percentual'       => 2.00,
            'multa_apos_dias'        => 1,
            'desconto_percentual'    => 0.00,
            'nao_receber_apos_dias'  => 29,
            'aceite'                 => 'N',
            'protestar_apos_dias'    => 0,
            'tipo_titulo'            => 'DM',
            'mensagem_banco'         => 'DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO',
            'instrucao_1'            => null,
            'instrucao_2'            => null,
            'instrucao_3'            => null,
            'ativo'                  => 1,
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('si_boleto_config');
    }
}
