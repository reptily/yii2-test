<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\repositories\BookRepository $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Каталог книг';
?>
<div class="book-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    if (!Yii::$app->user->isGuest): ?>
        <p><?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'image',
                'format' => 'html',
                'value' => function ($model) {
                    return $model->image
                        ? Html::img('@web/uploads/' . $model->image, ['width' => '50'])
                        : 'нет фото';
                },
            ],
            'title',
            [
                'label' => 'Авторы',
                'format' => 'raw',
                'value' => function ($model) {
                    $links = [];
                    foreach ($model->authors as $author) {
                        $links[] = Html::encode($author->full_name) . '<br>' .
                            Html::a('Подписаться', ['subscribe', 'author_id' => $author->id], ['class' => 'btn btn-xs btn-outline-info']);
                    }
                    return implode('<br>', $links);
                }
            ],
            'year',
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update' => !Yii::$app->user->isGuest,
                    'delete' => !Yii::$app->user->isGuest,
                ]
            ],
        ],
    ]); ?>
</div>