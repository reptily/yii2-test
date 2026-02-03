<?php

use yii\db\Migration;

class m260203_130448_create_library_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable('authors', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string()->notNull(),
        ], $tableOptions);

        $this->createTable('books', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'year' => $this->integer()->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(20)->unique(),
            'image' => $this->string(),
            'created_by' => $this->integer(),
        ], $tableOptions);

        $this->createTable('book_author', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'PRIMARY KEY (book_id, author_id)',
        ], $tableOptions);

        $this->createTable('subscriptions', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-book_author-book', 'book_author', 'book_id', 'books', 'id', 'CASCADE');
        $this->addForeignKey('fk-book_author-author', 'book_author', 'author_id', 'authors', 'id', 'CASCADE');
        $this->addForeignKey('fk-sub-author', 'subscriptions', 'author_id', 'authors', 'id', 'CASCADE');
        $this->addForeignKey('fk-book-user', 'books', 'created_by', 'users', 'id', 'SET NULL');
    }


    public function safeDown()
    {
        $this->dropTable('subscriptions');
        $this->dropTable('book_author');
        $this->dropTable('books');
        $this->dropTable('authors');
    }
}
