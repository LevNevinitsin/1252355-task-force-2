<?php

use yii\db\Migration;

/**
 * Class m220317_072043_insert_rows_into_role
 */
class m220317_072043_insert_rows_into_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('role', ['name'], [
            ['customer'],
            ['contractor'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('role');
    }
}
