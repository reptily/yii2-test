<?php

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use app\models\Subscription;
use app\repositories\BookRepository;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete', 'subscribe'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'subscribe'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['create', 'top-authors'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $id = Yii::$app->request->get('id');
                            $model = Book::findOne($id);
                            return $model && $model->created_by == Yii::$app->user->id;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new BookRepository();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Book();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                if ($model->imageFile) {
                    $fileName = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                    if ($model->imageFile->saveAs('uploads/' . $fileName)) {
                        $model->image = $fileName;
                    }
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Книга '{$model->title}' успешно добавлена!");
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        $authors = ArrayHelper::map(Author::find()->all(), 'id', 'full_name');

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->authorIds = ArrayHelper::getColumn($model->authors, 'id');

        if ($model->load($this->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->imageFile) {
                $path = Yii::getAlias('@webroot/uploads/');
                $fileName = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                if ($model->imageFile->saveAs($path . $fileName)) {
                    $model->image = $fileName;
                }
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $authors = ArrayHelper::map(Author::find()->all(), 'id', 'full_name');

        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->image) {
            $filePath = Yii::getAlias('@webroot/uploads/') . $model->image;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Книга успешно удалена.');

        return $this->redirect(['index']);
    }

    public function actionSubscribe($author_id)
    {
        $author = Author::findOne($author_id);


        if (!$author) {
            throw new NotFoundHttpException("Автор не найден");
        }

        $model = new Subscription();
        $model->author_id = $author_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Вы подписались на автора: {$author->full_name}");
            return $this->redirect(['index']);
        }

        return $this->render('subscribe', [
            'model' => $model,
            'author' => $author
        ]);
    }

    public function actionTopAuthors($year = null)
    {
        $year = $year ?: date('Y');

        $authors = Author::find()->topByYear($year);

        return $this->render('top-authors', [
            'authors' => $authors,
            'year' => $year,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}