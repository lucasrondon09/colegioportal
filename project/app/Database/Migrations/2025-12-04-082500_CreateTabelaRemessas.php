<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTabelaRemessas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'numero_sequencial' => [
                'type' => 'INT',
                'null' => false,
                'comment' => 'Número sequencial da remessa',
            ],
            'arquivo_nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'comment' => 'Nome do arquivo gerado',
            ],
            'arquivo_caminho' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => false,
                'comment' => 'Caminho completo do arquivo',
            ],
            'data_geracao' => [
                'type' => 'DATETIME',
                'null' => false,
                'comment' => 'Data/hora de geração',
            ],
            'qtd_titulos' => [
                'type' => 'INT',
                'null' => false,
                'default' => 0,
                'comment' => 'Quantidade de títulos na remessa',
            ],
            'valor_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
                'comment' => 'Valor total da remessa',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['GERADA', 'ENVIADA', 'PROCESSADA'],
                'default' => 'GERADA',
                'null' => false,
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
        
        $this->forge->createTable('si_remessas', true);

        // Adicionar índices usando SQL direto (compatível com CI 4.2.12)
        $this->db->query('ALTER TABLE `si_remessas` ADD INDEX `idx_status` (`status`)');
        $this->db->query('ALTER TABLE `si_remessas` ADD INDEX `idx_data_geracao` (`data_geracao`)');
        
        // Configurar charset e collation
        $this->db->query('ALTER TABLE `si_remessas` ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    public function down()
    {
        $this->forge->dropTable('si_remessas', true);
    }
}
