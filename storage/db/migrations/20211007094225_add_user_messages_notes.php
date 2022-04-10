<?php

use Phinx\Db\Adapter\MysqlAdapter;

class AddUserMessagesNotes extends Phinx\Migration\AbstractMigration
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
        ->addColumn('notes', 'text', [
            'null' => true,
            'limit' => MysqlAdapter::TEXT_LONG,
            'collation' => 'utf8mb4_general_ci',
            'encoding' => 'utf8mb4',
            'after' => 'users_id',
        ])
        ->save();
    }
}
