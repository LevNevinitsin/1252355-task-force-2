<?php

use yii\db\Migration;

/**
 * Class m220317_071506_insert_rows_into_task_status
 */
class m220317_071506_insert_rows_into_task_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    }
}
