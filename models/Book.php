<?php

namespace app\models;

use app\components\jobs\SmsNotifyJob;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $image
 *
 * @property Author[] $authors
 */
class Book extends ActiveRecord
{
    public $imageFile;
    public $authorIds = [];

    public static function tableName()
    {
        return 'books';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => null,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['title', 'year'], 'required'],
            [['year'], 'integer'],
            [['description'], 'string'],
            [['title', 'isbn', 'image'], 'string', 'max' => 255],
            [['isbn'], 'unique'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            ['authorIds', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'image' => 'Фото обложки',
            'authorIds' => 'Авторы',
        ];
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (is_array($this->authorIds)) {
            $this->unlinkAll('authors', true);
            foreach ($this->authorIds as $authorId) {
                $author = Author::findOne($authorId);
                if ($author) {
                    $this->link('authors', $author);
                }
            }
        }

        unset($this->authors);

        if ($insert && !empty($this->authors)) {
            $this->notifySubscribers();
        }
    }

    protected function notifySubscribers()
    {
        $authorNames = implode(', ', ArrayHelper::getColumn($this->authors, 'full_name'));
        $message = "Вышла новая книга: '{$this->title}'. Автор(ы): {$authorNames}.";

        $phones = [];

        foreach ($this->authors as $author) {
            foreach ($author->subscriptions as $sub) {
                $phones[$sub->phone] = $sub->phone;
            }
        }

        if (!empty($phones)) {
            Yii::$app->queue->push(new SmsNotifyJob([
                'phones' => array_values($phones),
                'message' => $message,
            ]));
        }
    }
}