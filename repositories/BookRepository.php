<?php

namespace app\repositories;

use app\models\Book;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BookRepository extends Book
{
    public function rules()
    {
        return [
            [['id', 'year'], 'integer'],
            [['title', 'description', 'isbn'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // Наследуем сценарии родителя
        return Model::scenarios();
    }

    /**
     * Создает экземпляр DataProvider с примененными фильтрами
     */
    public function search($params)
    {
        $query = Book::find();

        // Подгружаем авторов сразу (жадная загрузка), чтобы избежать 1+N запросов
        $query->with('authors');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Если валидация не прошла, возвращаем всё без фильтров
            return $dataProvider;
        }

        // Фильтрация по точным значениям
        $query->andFilterWhere([
            'id' => $this->id,
            'year' => $this->year,
        ]);

        // Фильтрация по частичному совпадению (LIKE)
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'isbn', $this->isbn]);

        return $dataProvider;
    }
}