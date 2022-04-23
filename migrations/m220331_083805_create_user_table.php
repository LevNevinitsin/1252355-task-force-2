<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%city}}`
 * - `{{%role}}`
 */
class m220331_083805_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'email' => $this->string(319)->notNull()->unique(),
            'password' => $this->string(255)->notNull(),
            'city_id' => $this->integer()->notNull(),
            'birthdate' => $this->date(),
            'photo' => $this->string(255),
            'phone' => $this->string(11),
            'telegram' => $this->string(64),
            'self_description' => $this->text(),
            'role_id' => $this->integer()->notNull(),
            'hide_contacts' => $this->tinyInteger(),
            'date_registered' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `city_id`
        $this->createIndex(
            '{{%idx-user-city_id}}',
            '{{%user}}',
            'city_id'
        );

        // add foreign key for table `{{%city}}`
        $this->addForeignKey(
            '{{%fk-user-city_id}}',
            '{{%user}}',
            'city_id',
            '{{%city}}',
            'id',
            'RESTRICT'
        );

        // creates index for column `role_id`
        $this->createIndex(
            '{{%idx-user-role_id}}',
            '{{%user}}',
            'role_id'
        );

        // add foreign key for table `{{%role}}`
        $this->addForeignKey(
            '{{%fk-user-role_id}}',
            '{{%user}}',
            'role_id',
            '{{%role}}',
            'id',
            'RESTRICT'
        );

        $testUsers = require Yii::getAlias('@app') . '/fixtures/data/user.php';

        $this->batchInsert('user', [
            'name',
            'email',
            'password',
            'city_id',
            'birthdate',
            'photo',
            'phone',
            'telegram',
            'self_description',
            'role_id',
            'fails_count',
            'date_registered',
        ], $testUsers);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('user');

        // drops foreign key for table `{{%city}}`
        $this->dropForeignKey(
            '{{%fk-user-city_id}}',
            '{{%user}}'
        );

        // drops index for column `city_id`
        $this->dropIndex(
            '{{%idx-user-city_id}}',
            '{{%user}}'
        );

        // drops foreign key for table `{{%role}}`
        $this->dropForeignKey(
            '{{%fk-user-role_id}}',
            '{{%user}}'
        );

        // drops index for column `role_id`
        $this->dropIndex(
            '{{%idx-user-role_id}}',
            '{{%user}}'
        );

        $this->dropTable('{{%user}}');
    }
}
