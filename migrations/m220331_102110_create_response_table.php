<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%response}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%task}}`
 * - `{{%user}}`
 */
class m220331_102110_create_response_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%response}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'price' => $this->integer(),
            'comment' => $this->text(),
            'date_created' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `task_id`
        $this->createIndex(
            '{{%idx-response-task_id}}',
            '{{%response}}',
            'task_id'
        );

        // add foreign key for table `{{%task}}`
        $this->addForeignKey(
            '{{%fk-response-task_id}}',
            '{{%response}}',
            'task_id',
            '{{%task}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-response-user_id}}',
            '{{%response}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-response-user_id}}',
            '{{%response}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $testResponses = require Yii::getAlias('@app') . '/fixtures/data/response.php';

        $this->batchInsert('response', [
            'task_id',
            'user_id',
            'price',
            'comment',
            'date_created',
        ], $testResponses);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('response');

        // drops foreign key for table `{{%task}}`
        $this->dropForeignKey(
            '{{%fk-response-task_id}}',
            '{{%response}}'
        );

        // drops index for column `task_id`
        $this->dropIndex(
            '{{%idx-response-task_id}}',
            '{{%response}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-response-user_id}}',
            '{{%response}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-response-user_id}}',
            '{{%response}}'
        );

        $this->dropTable('{{%response}}');
    }
}
