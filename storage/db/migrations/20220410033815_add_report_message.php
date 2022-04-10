<?php

use Phinx\Db\Adapter\MysqlAdapter;

class AddReportMessage extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('user_messages', [
            'id' => false,
            'primary_key' => ['messages_id', 'users_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('is_reported', 'boolean', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'is_shared',
            ])
            ->addIndex(['is_reported'], [
                'name' => 'is_reported',
                'unique' => false,
            ])
            ->save();
    }
}
