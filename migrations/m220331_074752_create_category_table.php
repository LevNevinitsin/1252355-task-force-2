<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m220331_074752_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
            'icon' => $this->string(50)->notNull(),
        ]);

        $this->batchInsert('category', ['name', 'icon'], [
            ['Курьерские услуги', 'translation'],
            ['Уборка', 'clean'],
            ['Переезды', 'cargo'],
            ['Компьютерная помощь', 'neo'],
            ['Ремонт квартирный', 'flat'],
            ['Ремонт техники', 'repair'],
            ['Красота', 'beauty'],
            ['Фото', 'photo'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('category');
        $this->dropTable('{{%category}}');
    }
}
