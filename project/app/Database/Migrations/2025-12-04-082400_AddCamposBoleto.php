<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposBoleto extends Migration
{
    public function up()
    {
        // Adicionar campos para controle de boletos na tabela si_parcelas_contrato
        $fields = [
            'nosso_numero' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'comment' => 'Nosso Número do boleto gerado pelo banco',
            ],
            'linha_digitavel' => [
                'type' => 'VARCHAR',
                'constraint' => '54',
                'null' => true,
                'comment' => 'Linha digitável do boleto',
            ],
            'codigo_barras' => [
                'type' => 'VARCHAR',
                'constraint' => '44',
                'null' => true,
                'comment' => 'Código de barras do boleto',
            ],
            'boleto_gerado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Data e hora de geração do boleto',
            ],
            'id_remessa' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'FK para tabela de remessas',
            ],
        ];

        $this->forge->addColumn('si_parcelas_contrato', $fields);

        // Adicionar índices usando SQL direto (compatível com CI 4.2.12)
        $this->db->query('ALTER TABLE `si_parcelas_contrato` ADD INDEX `idx_nosso_numero` (`nosso_numero`)');
        $this->db->query('ALTER TABLE `si_parcelas_contrato` ADD INDEX `idx_id_remessa` (`id_remessa`)');
    }

    public function down()
    {
        // Remover índices
        $this->db->query('ALTER TABLE `si_parcelas_contrato` DROP INDEX `idx_nosso_numero`');
        $this->db->query('ALTER TABLE `si_parcelas_contrato` DROP INDEX `idx_id_remessa`');

        // Remover campos
        $this->forge->dropColumn('si_parcelas_contrato', [
            'nosso_numero',
            'linha_digitavel',
            'codigo_barras',
            'boleto_gerado_em',
            'id_remessa',
        ]);
    }
}
