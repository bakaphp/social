<?php

use Phinx\Db\Adapter\MysqlAdapter;

class UpdateLikes extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('messages', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('total_liked', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'comments_count',
            ])
            ->removeColumn('total_likes')
            ->removeIndexByName('total_likes')
            ->addIndex(['total_liked'], [
                'name' => 'total_likes',
                'unique' => false,
            ])
            ->save();
    }
}
