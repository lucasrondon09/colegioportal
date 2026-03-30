<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTabelaRetornos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'arquivo_nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'comment' => 'Nome do arquivo de retorno',
            ],
            'arquivo_caminho' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => false,
                'comment' => 'Caminho completo do arquivo',
            ],
            'data_processamento' => [
                'type' => 'DATETIME',
                'null' => false,
                'comment' => 'Data/hora de processamento',
            ],
            'qtd_pagamentos' => [
                'type' => 'INT',
                'null' => false,
                'default' => 0,
                'comment' => 'Quantidade de pagamentos confirmados',
            ],
            'qtd_rejeicoes' => [
                'type' => 'INT',
                'null' => false,
                'default' => 0,
                'comment' => 'Quantidade de rejeições',
            ],
            'valor_total_pago' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
                'comment' => 'Valor total pago',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        
        $this->forge->createTable('si_retornos', true);

        // Adicionar índice usando SQL direto (compatível com CI 4.2.12)
        $this->db->query('ALTER TABLE `si_retornos` ADD INDEX `idx_data_processamento` (`data_processamento`)');
        
        // Configurar charset e collation
        $this->db->query('ALTER TABLE `si_retornos` ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    public function down()
    {
        $this->forge->dropTable('si_retornos', true);
    }
}
