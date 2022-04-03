<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chosen_category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%category}}`
 */
class m220331_085836_create_chosen_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chosen_category}}', [
            'user_id' => $this->integer(),
            'category_id' => $this->integer(),
        ]);

        $this->addPrimaryKey('pk-user-category', 'chosen_category', ['user_id', 'category_id']);

        // creates index for column `category_id`
        $this->createIndex(
            '{{%idx-chosen_category-category_id}}',
            '{{%chosen_category}}',
            'category_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-chosen_category-user_id}}',
            '{{%chosen_category}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // add foreign key for table `{{%category}}`
        $this->addForeignKey(
            '{{%fk-chosen_category-category_id}}',
            '{{%chosen_category}}',
            'category_id',
            '{{%category}}',
            'id',
            'RESTRICT'
        );

        $testChosenCategories = require Yii::getAlias('@app') . '/fixtures/data/chosenCategory.php';

        $this->batchInsert('chosen_category', [
            'user_id',
            'category_id',
        ], $testChosenCategories);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('chosen_category');

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-chosen_category-user_id}}',
            '{{%chosen_category}}'
        );

        // drops foreign key for table `{{%category}}`
        $this->dropForeignKey(
            '{{%fk-chosen_category-category_id}}',
            '{{%chosen_category}}'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            '{{%idx-chosen_category-category_id}}',
            '{{%chosen_category}}'
        );

        $this->dropTable('{{%chosen_category}}');
    }
}
