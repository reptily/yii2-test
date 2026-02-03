<?php

use yii\db\Migration;

class m260203_124512_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
/*
 * $user = new \app\models\User();
$user->username = 'admin';
$user->setPassword('admin123');
$user->generateAuthKey();
$user->save();
 */
