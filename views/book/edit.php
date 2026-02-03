<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $authors - массив вида [id => full_name] для выпадающего списка */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Введите название книги']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'year')->textInput(['type' => 'number', 'placeholder' => 'Например: 2023']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'isbn')->textInput(['maxlength' => true, 'placeholder' => '978-3-16-148410-0']) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

    <?php if (!$model->isNewRecord && $model->image): ?>
        <div class="mb-3">
            <?= Html::img('@web/uploads/' . $model->image, ['width' => '150', 'class' => 'img-thumbnail']) ?>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <hr>

    <?= $form->field($model, 'authorIds')->listBox($authors, [
        'multiple' => true,
        'size' => 8,
        'class' => 'form-control',
        'style' => 'font-size: 1.1em;'
    ])->label('Выберите авторов (удерживайте Ctrl для выбора нескольких)') ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Сохранить данные', ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
