<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class EntityNamespaceUserMessagesActivities extends AbstractMigration
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
            ->addColumn('entity_namespace', 'string', [
                'limit' => 255,
                'null' => true,
                'collation' => 'utf8mb4_unicode_520_ci',
                'encoding' => 'utf8mb4',
                'after' => 'from_entity_id',
            ])
            ->save();
    }
}
