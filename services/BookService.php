<?php
namespace app\services;

use app\dto\BookDto;
use Yii;
use app\models\Book;
use yii\web\UploadedFile;

class BookService
{
    public function create(Book $model): bool
    {
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        if ($model->imageFile) {
            $fileName = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
            $path = Yii::getAlias('@webroot/uploads/');

            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            if ($model->imageFile->saveAs($path . $fileName)) {
                $model->image = $fileName;
            }
        }

        return $model->save();
    }

    public function save(Book $model, BookDto $dto): bool
    {
        $model->title = $dto->title;
        $model->year = $dto->year;
        $model->description = $dto->description;
        $model->isbn = $dto->isbn;
        $model->authorIds = $dto->authorIds;

        if ($dto->imageFile) {
            $path = \Yii::getAlias('@webroot/uploads/');
            $fileName = time() . '_' . $dto->imageFile->baseName . '.' . $dto->imageFile->extension;

            if ($dto->imageFile->saveAs($path . $fileName)) {
                if (!$model->isNewRecord && $model->image && file_exists($path . $model->image)) {
                    @unlink($path . $model->image);
                }
                $model->image = $fileName;
            }
        }

        return $model->save();
    }

    public function delete(Book $model): bool
    {
        if ($model->image) {
            $filePath = Yii::getAlias('@webroot/uploads/') . $model->image;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        return (bool)$model->delete();
    }
}
