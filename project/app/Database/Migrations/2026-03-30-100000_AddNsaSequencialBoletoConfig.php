<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Adicionar NSA sequencial na tabela si_boleto_config
 *
 * O NSA (Número Sequencial de Arquivo) é exigido pela Caixa Econômica Federal
 * no campo 19.0 (pos 158-163) do Header de Arquivo CNAB 240.
 * Deve ser sequencial e único para cada remessa gerada.
 *
 * A Caixa informou que a próxima remessa deve iniciar com NSA = 002158.
 * O campo nsa_sequencial armazena o ÚLTIMO NSA utilizado.
 * A cada nova remessa, o sistema incrementa este valor em 1.
 */
class AddNsaSequencialBoletoConfig extends Migration
{
    public function up()
    {
        $this->forge->addColumn('si_boleto_config', [
            'nsa_sequencial' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'default'    => 2157,
                'after'      => 'ativo',
                'comment'    => 'Último NSA utilizado. Próxima remessa usará nsa_sequencial + 1. Caixa iniciou em 002158.',
            ],
        ]);

        // Atualizar o registro existente para o valor inicial correto
        // O próximo NSA a ser usado será 2158 (nsa_sequencial + 1)
        $this->db->table('si_boleto_config')
            ->where('ativo', 1)
            ->update(['nsa_sequencial' => 2157]);
    }

    public function down()
    {
        $this->forge->dropColumn('si_boleto_config', 'nsa_sequencial');
    }
}
