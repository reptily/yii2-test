<?php

namespace app\repositories;

use yii\db\ActiveQuery;

class AuthorRepository extends ActiveQuery
{
    public function topByYear($year)
    {
        return $this->select([
            'authors.full_name',
            'COUNT(book_author.book_id) AS books_count'
        ])
            ->joinWith('books', false)
            ->where(['books.year' => $year])
            ->groupBy(['authors.id'])
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }
}