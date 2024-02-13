<?php

use yii\db\Migration;

/**
 * Class m240209_182827_notes
 */
class m240209_182827_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notes}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'title' => $this->string(),
            'content' => $this->string(),
        ]);
        $this->addForeignKey(
            'userId',  // это "условное имя" ключа
            '{{%notes}}', // это название текущей таблицы
            'user_id', // это имя поля в текущей таблице, которое будет ключом
            'user', // это имя таблицы, с которой хотим связаться
            'id', // это поле таблицы, с которым хотим связаться
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240209_182827_notes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240209_182827_notes cannot be reverted.\n";

        return false;
    }
    */
}
