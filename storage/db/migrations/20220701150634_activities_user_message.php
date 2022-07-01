<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ActivitiesUserMessage extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('user_messages_activities')
            ->addColumn('user_messages_id', 'integer', ['null' => false])
            ->addColumn('from_entity_id', 'integer', ['null' => false])
            ->addColumn('type', 'string', ['null' => false])
            ->addColumn('text', 'text', ['null' => false, 'collation' => 'utf8mb4_general_ci'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->addColumn('is_deleted', 'integer', ['default' => 0])
            ->create();
    }
}
