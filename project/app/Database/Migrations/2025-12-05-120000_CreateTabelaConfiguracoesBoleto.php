<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTabelaConfiguracoesBoleto extends Migration
{
    public function up()
    {
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
                'default'    => '1.00',
                'comment'    => 'Juros ao mês (%)',
            ],
            'multa_percentual' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '2.00',
                'comment'    => 'Multa (%)',
            ],
            'multa_apos_dias' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 1,
                'comment'    => 'Aplicar multa após X dias do vencimento',
            ],
            'nao_receber_apos_dias' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 29,
                'comment'    => 'Não receber após X dias do vencimento',
            ],
            'protestar_apos_dias' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
                'comment'    => 'Protestar após X dias (0 = não protestar)',
            ],
            'aceite' => [
                'type'       => 'ENUM',
                'constraint' => ['S', 'N'],
                'default'    => 'N',
                'comment'    => 'Aceite do título',
            ],
            'tipo_titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'default'    => 'DM',
                'comment'    => 'Tipo de título (DM=Duplicata Mercantil, DS=Duplicata Serviço, etc)',
            ],
            'mensagem_sacado' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Mensagem na via do sacado (apenas boletos)',
            ],
            'mensagem_banco' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Mensagem na via do banco (boletos e carnês)',
            ],
            'desconto_percentual' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.00',
                'comment'    => 'Desconto até o vencimento (%)',
            ],
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=Ativo, 0=Inativo',
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
        $this->forge->createTable('si_configuracoes_boleto');

        // Inserir configuração padrão
        $this->db->table('si_configuracoes_boleto')->insert([
            'juros_percentual'       => 1.00,
            'multa_percentual'       => 2.00,
            'multa_apos_dias'        => 1,
            'nao_receber_apos_dias'  => 29,
            'protestar_apos_dias'    => 0,
            'aceite'                 => 'N',
            'tipo_titulo'            => 'DM',
            'mensagem_sacado'        => null,
            'mensagem_banco'         => 'NÃO RECEBER APÓS 29 DIAS DE ATRASO' . "\n" .
                                        'JUROS: 1,00% AO MÊS (DIAS CORRIDOS) A PARTIR DO VENCIMENTO' . "\n" .
                                        'MULTA: 2,00% APÓS O VENCIMENTO' . "\n" .
                                        'DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO',
            'desconto_percentual'    => 0.00,
            'ativo'                  => 1,
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('si_configuracoes_boleto');
    }
}
