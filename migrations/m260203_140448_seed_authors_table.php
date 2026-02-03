<?php

use yii\db\Migration;

class m260203_140448_seed_authors_table extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('authors', ['full_name'], [
            ['Александр Пушкин'],
            ['Лев Толстой'],
            ['Фёдор Достоевский'],
            ['Стивен Кинг'],
            ['Джордж Оруэлл'],
            ['Говард Лавкрафт'],
        ]);
    }

    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->truncateTable('authors');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}
