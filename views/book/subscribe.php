<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var app\models\Subscription $model */
/** @var app\models\Author $author */
?>

<div class="subscription-form">
    <h1>Подписка на автора: <?= Html::encode($author->full_name) ?></h1>
    <p>Введите ваш номер телефона, чтобы получать уведомления о новых книгах этого автора.</p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput([
        'placeholder' => '79991234567',
        'type' => 'tel'
    ])->label('Ваш номер телефона') ?>

    <div class="form-group">
        <?= Html::submitButton('Подписаться', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
