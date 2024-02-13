<?php

use yii\db\Migration;

/**
 * Class m230723_134156_userInfo
 */
class m230723_134156_userInfo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
  {
        $this->createTable('{{%userInfo}}', [
            'id' => $this->primaryKey(),
            'surname' => $this->string(),
            'name' => $this->string(),
            'patronymic' => $this->string(),
            'iin' => $this->string(12)->unique(),
            'birth_data' => $this->date(),
            'photo_url' => $this->string(),
            'auth_id' => $this->integer(),
            ]);
        $this->addForeignKey(
                'auth_id',  // это "условное имя" ключа
                '{{%userInfo}}', // это название текущей таблицы
                'auth_id', // это имя поля в текущей таблице, которое будет ключом
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
        $this->dropTable('{{%userInfo}}');

        echo "m230723_134156_userInfo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230723_134156_userInfo cannot be reverted.\n";

        return false;
    }
    */
}
