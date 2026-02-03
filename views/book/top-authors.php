<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var array $authors */
/** @var string $year */

$this->title = "ТОП 10 авторов за $year год";
?>

<div class="top-authors">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Форма фильтрации по году -->
    <div class="row mb-4">
        <div class="col-md-4">
            <?= Html::beginForm(['top-authors'], 'get', ['class' => 'form-inline']) ?>
            <div class="input-group">
                <?= Html::input('number', 'year', $year, ['class' => 'form-control', 'placeholder' => 'Введите год']) ?>
                <button type="submit" class="btn btn-primary">Показать</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if (!empty($authors)): ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>ФИО Автора</th>
                <th>Количество выпущенных книг</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($authors as $index => $author): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= Html::encode($author['full_name']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= $author['books_count'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">За <?= Html::encode($year) ?> год данных не найдено.</div>
    <?php endif; ?>
</div>
