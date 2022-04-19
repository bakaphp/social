<?php

use Phinx\Db\Adapter\MysqlAdapter;

class UpdateFeedIndex extends Phinx\Migration\AbstractMigration
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
            ->addColumn('is_liked', 'boolean', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'notes',
            ])
            ->addColumn('is_saved', 'boolean', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'is_liked',
            ])
            ->addColumn('is_shared', 'boolean', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'is_saved',
            ])
            ->addColumn('reactions', 'text', [
                'null' => true,
                'limit' => MysqlAdapter::TEXT_LONG,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'is_shared',
            ])
            ->addColumn('saved_lists', 'text', [
                'null' => true,
                'limit' => MysqlAdapter::TEXT_LONG,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'reactions',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'saved_lists',
            ])
            ->changeColumn('is_deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'created_at',
            ])
            ->addIndex(['is_liked'], [
                'name' => 'is_liked',
                'unique' => false,
            ])
            ->addIndex(['is_saved'], [
                'name' => 'is_saved',
                'unique' => false,
            ])
            ->addIndex(['is_shared'], [
                'name' => 'is_shared',
                'unique' => false,
            ])
            ->addIndex(['reactions'], [
                'name' => 'reactions',
                'unique' => false,
                'limit' => [
                    'reactions' => '768',
                ],
            ])
            ->addIndex(['saved_lists'], [
                'name' => 'saved_lists',
                'unique' => false,
                'limit' => [
                    'saved_lists' => '768',
                ],
            ])
            ->save();

        $this->table('users_interactions', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('app_module_message', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('tags', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('flags', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->save();

        $this->table('users_follows', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('message_types', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->save();

        $this->table('channels', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->changeColumn('slug', 'char', [
                'null' => true,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'name',
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->addIndex(['created_at'], [
                'name' => 'created_at',
                'unique' => false,
            ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['slug'], [
                'name' => 'slug',
                'unique' => false,
            ])
            ->save();

        $this->table('message_variables', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'value',
            ])
            ->addIndex(['created_at'], [
                'name' => 'created_at',
                'unique' => false,
            ])
            ->save();

        $this->table('interactions', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->save();

        $this->table('messages', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->changeColumn('parent_unique_id', 'char', [
                'null' => true,
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'parent_id',
            ])
            ->changeColumn('reactions_count', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'message',
            ])
            ->changeColumn('comments_count', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'reactions_count',
            ])
            ->addColumn('total_likes', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'comments_count',
            ])
            ->addColumn('total_saved', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'total_likes',
            ])
            ->addColumn('total_shared', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'total_saved',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'total_shared',
            ])
            ->changeColumn('updated_at', 'datetime', [
                'null' => true,
                'after' => 'created_at',
            ])
            ->changeColumn('is_deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'updated_at',
            ])
            ->addIndex(['total_likes'], [
                'name' => 'total_likes',
                'unique' => false,
            ])
            ->addIndex(['total_saved'], [
                'name' => 'total_saved',
                'unique' => false,
            ])
            ->addIndex(['total_shared'], [
                'name' => 'total_shared',
                'unique' => false,
            ])
            ->addIndex(['parent_id'], [
                'name' => 'parent_id',
                'unique' => false,
            ])
            ->addIndex(['parent_unique_id'], [
                'name' => 'parent_unique_id',
                'unique' => false,
            ])
            ->save();

        $this->table('channel_messages', [
            'id' => false,
            'primary_key' => ['channel_id', 'messages_id', 'users_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('users_reactions', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('channel_users', [
            'id' => false,
            'primary_key' => ['channel_id', 'users_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->save();

        $this->table('message_comments', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('message_tags', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->save();

        $this->table('reactions', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_bin',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['created_at'], [
                'name' => 'created_at',
                'unique' => false,
            ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('users_lists_entities', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->save();

        $this->table('users_lists', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->changeColumn('slug', 'char', [
                'null' => true,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_unicode_520_ci',
                'encoding' => 'utf8mb4',
                'after' => 'name',
            ])
            ->addIndex(['updated_at'], [
                'name' => 'updated_at',
                'unique' => false,
            ])
            ->addIndex(['slug'], [
                'name' => 'slug',
                'unique' => false,
            ])
            ->save();
    }
}
