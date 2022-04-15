<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%category}}`
 * - `{{%city}}`
 * - `{{%task_status}}`
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m220331_101320_create_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'overview' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'location' => $this->string(255),
            'latitude' => $this->decimal(9, 6),
            'longitude' => $this->decimal(10, 6),
            'city_id' => $this->integer(),
            'budget' => $this->integer(),
            'deadline' => $this->date(),
            'task_status_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'contractor_id' => $this->integer(),
            'score' => $this->tinyInteger(),
            'feedback' => $this->text(),
            'date_updated' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_created' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `category_id`
        $this->createIndex(
            '{{%idx-task-category_id}}',
            '{{%task}}',
            'category_id'
        );

        // add foreign key for table `{{%category}}`
        $this->addForeignKey(
            '{{%fk-task-category_id}}',
            '{{%task}}',
            'category_id',
            '{{%category}}',
            'id',
            'RESTRICT'
        );

        // creates index for column `city_id`
        $this->createIndex(
            '{{%idx-task-city_id}}',
            '{{%task}}',
            'city_id'
        );

        // add foreign key for table `{{%city}}`
        $this->addForeignKey(
            '{{%fk-task-city_id}}',
            '{{%task}}',
            'city_id',
            '{{%city}}',
            'id',
            'RESTRICT'
        );

        // creates index for column `task_status_id`
        $this->createIndex(
            '{{%idx-task-task_status_id}}',
            '{{%task}}',
            'task_status_id'
        );

        // add foreign key for table `{{%task_status}}`
        $this->addForeignKey(
            '{{%fk-task-task_status_id}}',
            '{{%task}}',
            'task_status_id',
            '{{%task_status}}',
            'id',
            'RESTRICT'
        );

        // creates index for column `customer_id`
        $this->createIndex(
            '{{%idx-task-customer_id}}',
            '{{%task}}',
            'customer_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-task-customer_id}}',
            '{{%task}}',
            'customer_id',
            '{{%user}}',
            'id',
            'RESTRICT'
        );

        // creates index for column `contractor_id`
        $this->createIndex(
            '{{%idx-task-contractor_id}}',
            '{{%task}}',
            'contractor_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-task-contractor_id}}',
            '{{%task}}',
            'contractor_id',
            '{{%user}}',
            'id',
            'RESTRICT'
        );

        $testTasks = require Yii::getAlias('@app') . '/fixtures/data/task.php';

        $this->batchInsert('task', [
            'overview',
            'description',
            'category_id',
            'city_id',
            'budget',
            'deadline',
            'task_status_id',
            'customer_id',
            'contractor_id',
            'score',
            'feedback',
            'date_updated',
            'date_created',
        ], $testTasks);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('task');

        // drops foreign key for table `{{%category}}`
        $this->dropForeignKey(
            '{{%fk-task-category_id}}',
            '{{%task}}'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            '{{%idx-task-category_id}}',
            '{{%task}}'
        );

        // drops foreign key for table `{{%city}}`
        $this->dropForeignKey(
            '{{%fk-task-city_id}}',
            '{{%task}}'
        );

        // drops index for column `city_id`
        $this->dropIndex(
            '{{%idx-task-city_id}}',
            '{{%task}}'
        );

        // drops foreign key for table `{{%task_status}}`
        $this->dropForeignKey(
            '{{%fk-task-task_status_id}}',
            '{{%task}}'
        );

        // drops index for column `task_status_id`
        $this->dropIndex(
            '{{%idx-task-task_status_id}}',
            '{{%task}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-task-customer_id}}',
            '{{%task}}'
        );

        // drops index for column `customer_id`
        $this->dropIndex(
            '{{%idx-task-customer_id}}',
            '{{%task}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-task-contractor_id}}',
            '{{%task}}'
        );

        // drops index for column `contractor_id`
        $this->dropIndex(
            '{{%idx-task-contractor_id}}',
            '{{%task}}'
        );

        $this->dropTable('{{%task}}');
    }
}
