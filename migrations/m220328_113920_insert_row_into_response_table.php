<?php

use yii\db\Migration;

/**
 * Class m220328_113920_insert_rows_into_responses_table
 */
class m220328_113920_insert_row_into_response_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('response', [
            'task_id' => 3,
            'user_id' => 10,
            'price' => 1100,
            'comment' => 'test',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('response');
    }
}
