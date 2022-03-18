<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%task_status}}`.
 */
class m220317_071157_add_code_column_to_task_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task_status', 'code', $this->string(30)->unique()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('task_status', 'code');
    }
}
