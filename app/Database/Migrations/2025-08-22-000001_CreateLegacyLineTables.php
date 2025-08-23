<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLegacyLineTables extends Migration
{
    /**
     * @var list<string>
     */
    private array $tables = ['blue','brown','green','orange','pink','purple','red','yellow'];

    public function up()
    {
        // Note: Using DB for station lists is optional now.
        // The app can read from legacy HTML under /stations/*.php and does not require MySQL.
        // Run this migration only if you prefer DB-backed station lists.
        foreach ($this->tables as $tbl) {
            $this->forge->addField([
                'sid'     => ['type' => 'INT', 'null' => false],
                'station' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            ]);
            $this->forge->addKey('sid', true);
            // The second argument ensures IF NOT EXISTS behavior
            $this->forge->createTable($tbl, true);
        }
    }

    public function down()
    {
        foreach ($this->tables as $tbl) {
            // The second argument drops only if exists
            $this->forge->dropTable($tbl, true);
        }
    }
}
