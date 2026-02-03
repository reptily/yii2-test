<?php

namespace app\controllers;

use app\dto\BookDto;
use app\dto\SubscriptionDto;
use app\models\Author;
use app\models\Book;
use app\models\Subscription;
use app\repositories\BookRepository;
use app\services\BookService;
use app\services\SubscriptionService;
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
        $postData = Yii::$app->request->post('Book');

        if ($this->request->isPost && $postData) {
            $dto = new BookDto(
                title: $postData['title'] ?? '',
                year: (int)($postData['year'] ?? 0),
                description: $postData['description'] ?? null,
                isbn: $postData['isbn'] ?? null,
                authorIds: $postData['authorIds'] ?? [],
                imageFile: UploadedFile::getInstance($model, 'imageFile')
            );

            if ((new BookService())->save($model, $dto)) {
                Yii::$app->session->setFlash('success', "Книга '{$model->title}' добавлена.");
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => ArrayHelper::map(Author::find()->all(), 'id', 'full_name'),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $postData = Yii::$app->request->post('Book');

        if ($this->request->isPost && $postData) {
            $dto = new BookDto(
                title: $postData['title'] ?? '',
                year: (int)($postData['year'] ?? 0),
                description: $postData['description'] ?? null,
                isbn: $postData['isbn'] ?? null,
                authorIds: $postData['authorIds'] ?? [],
                imageFile: UploadedFile::getInstance($model, 'imageFile')
            );

            if ((new BookService())->save($model, $dto)) {
                Yii::$app->session->setFlash('success', "Изменения сохранены.");
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $model->authorIds = ArrayHelper::getColumn($model->authors, 'id');

        return $this->render('update', [
            'model' => $model,
            'authors' => ArrayHelper::map(Author::find()->all(), 'id', 'full_name'),
        ]);
    }


    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $service = new BookService();

        if ($service->delete($model)) {
            Yii::$app->session->setFlash('success', 'Книга и обложка успешно удалены.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении книги.');
        }

        return $this->redirect(['index']);
    }

    public function actionSubscribe($author_id)
    {
        $author = Author::findOne($author_id);

        if (!$author) {
            throw new NotFoundHttpException("Автор не найден");
        }

        $model = new Subscription(['author_id' => $author_id]);
        $postData = Yii::$app->request->post('Subscription');

        if (Yii::$app->request->isPost && $postData) {
            $dto = new SubscriptionDto(
                authorId: (int)($postData['author_id'] ?? $author_id),
                phone: (string)($postData['phone'] ?? ''),
            );

            if ((new SubscriptionService())->subscribe($model, $dto)) {
                Yii::$app->session->setFlash('success', "Вы подписались на: {$author->full_name}");
                return $this->redirect(['index']);
            }
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