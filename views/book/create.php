<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use kartik\select2\Select2; // Рекомендуется для мультиселекта авторов

/** @var yii\widgets\ActiveForm $form */
/** @var array $authors - список [id => full_name] */
?>

<div class="book-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?= $form->field($model, 'authorIds')->listBox($authors, [
        'multiple' => true,
        'size' => 10
    ])->label('Выберите авторов') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить книгу', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
