<?php

namespace app\controllers;

use Yii;
use app\models\Mun;
use app\models\User;
use app\models\MunSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MunController implements the CRUD actions for Mun model.
 */
class MunController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Mun models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MunSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['MunSearch']['type'] = $searchModel::TYPE_MAIN;
        $dataProvider = $searchModel->search($queryParams);
        $queryParams['MunSearch']['type'] = $searchModel::TYPE_APPLICATION;
        $dataProviderApplication = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderApplication' => $dataProviderApplication,
        ]);
    }

    /**
     * Displays a single Mun model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Mun model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Mun(['operator_id' => Yii::$app->operator->identity->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Mun model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Если payer то подается заявка на изменение
        if (Yii::$app->user->can('payer')) {
            // Если пользователь уже подавал заявку, то выводим ее, иначе, создаем новую
            $application = Mun::find()->where([
                'user_id' => Yii::$app->user->id,
                'mun_id' => $model->id,
            ])->limit(1)->one();

            if(!$application) {
                $application = new Mun();
                $application->setAttributes($model->attributes);
                $application->user_id = \Yii::$app->user->id;
                $application->mun_id = $model->id;
            }

            $application->type = $application::TYPE_APPLICATION;
            $model = $application;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Mun model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {

                $this->findModel($id)->delete();

                return $this->redirect(['index']);
            }
            else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
                 return $this->redirect(['index']);
            }
        }
        return $this->render('/user/delete', [
            'user' => $user,
            'title' => 'Удалить муниципалитет',
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionConfirm($id) {
        $newModel = $this->findModel($id);
        $mainModel = $this->findModel($newModel->mun_id);
        $mainModel->setAttributes($newModel->attributes);
        $mainModel->confirmationFile = $newModel->confirmationFile;
        if ($mainModel->save()) {
            //обнуляем значения, чтобы не удалился файл при удалении модели
            $newModel->file = null;
            $newModel->base_url = null;
            if ($newModel->delete()) {
                //TODO: после принятия параметров должен производиться перерасчёт нормативной стоимости программ
                Yii::$app->session->setFlash('success', 'Данные изменены.');
                return $this->redirect(['view', 'id' => $mainModel->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Данные изменены, но не удалось удалить заявку.');
                return $this->redirect(['view', 'id' => $newModel->id]);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось изменить данные.');
            return $this->redirect(['view', 'id' => $newModel->id]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionReject($id) {
        $model = $this->findModel($id);
        $model->type = $model::TYPE_REJECTED;
        if ($model->save()) {
            //todo: Добавить оповещение пользователю, что его заявку отклонили
            Yii::$app->session->setFlash('success', 'Заявка отклонена.');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось отклонить заявку.');
            return $this->redirect(['view', 'id' => $id]);
        }
    }

    /**
     * Finds the Mun model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mun the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Mun::find()
            ->where([
                'id' => $id,
                'operator_id' => Yii::$app->operator->identity->id
            ])
            ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
