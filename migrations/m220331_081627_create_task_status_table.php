<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task_status}}`.
 */
class m220331_081627_create_task_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
            'code' => $this->string(50)->notNull()->unique(),
        ]);

        $this->batchInsert('task_status', ['name', 'code'], [
            ['Новое', 'new'],
            ['Отменено', 'cancelled'],
            ['В работе', 'in_progress'],
            ['Провалено', 'failed'],
            ['Выполнено', 'done'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('task_status');
        $this->dropTable('{{%task_status}}');
    }
}
