<?php

namespace app\controllers;

use app\models\Coefficient;
use app\models\Notification;
use app\models\NotificationUser;
use app\models\ProgrammeModule;
use app\models\Programs;
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
        $model = Mun::find()->where([
            'mun_id' => $id,
            'user_id' => Yii::$app->user->id,
            'type' => Mun::TYPE_APPLICATION
        ])->limit(1)->one();

        if (!$model) {
            $model = $this->findModel($id);
        }

        return $this->render('view', [
            'model' => $model,
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
        $isPayer = Yii::$app->user->can('payer');

        // Если payer и это основная запись муниципалитета, то подается заявка на изменение
        if ($isPayer && $model->type === $model::TYPE_MAIN) {
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

            $model = $application;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($isPayer) {
                $model->type = $model::TYPE_APPLICATION;
                $model->setScenario($model::SCENARIO_APPLICATION);
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
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
            //обнуляем значения, чтобы не удалился файл при удалении заявки
            $newModel->file = null;
            $newModel->base_url = null;
            if (!$this->recountNormativePrice($mainModel->id)) {
                Yii::$app->session->addFlash('error', 'Не удалось пересчитать нормативную стоимость программ.');
            }
            if ($newModel->delete()) {
                Yii::$app->session->addFlash('success', 'Данные муниципалитета изменены.');
                return $this->redirect(['view', 'id' => $mainModel->id]);
            } else {
                Yii::$app->session->addFlash('error', 'Данные муниципалитета изменены, но не удалось удалить заявку.');
                return $this->redirect(['view', 'id' => $newModel->id]);
            }
        } else {
            Yii::$app->session->addFlash('error', 'Не удалось изменить данные муниципалитета.');
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
            // Добавляем оповещение пользователю, что его заявку отклонили
            $message = 'В применении новых параметров отказано.';
            $notification = Notification::getExistOrCreate($message, 1, Notification::TYPE_MUN_APPLICATION_REJECT);
            if ($notification) {
                NotificationUser::assignToUsers([$model->user_id], $notification->id);
            }
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

    /**
     * @param $munId
     * @return bool
     */
    protected function recountNormativePrice($munId)
    {
        $munTable = Mun::tableName();
        $modules = ProgrammeModule::find()
            ->joinWith('program.municipality')
            ->andWhere([$munTable . '.[[id]]' => $munId])
            ->andWhere(['programs.verification' => [
                Programs::VERIFICATION_UNDEFINED,
                Programs::VERIFICATION_WAIT,
                Programs::VERIFICATION_DONE,
                Programs::VERIFICATION_DENIED,
            ]])
            ->all();

        /** @var Coefficient $coefficientData */
        $coefficientData = Yii::$app->coefficient->data;

        /** @var ProgrammeModule $module */
        foreach ($modules as $module) {
            $program = $module->program;
            if ($program) {
                $module->normative_price = $module->getNormativePrice($coefficientData);
                if (false === $module->normative_price || !$module->save()) {
                    return false;
                }
            }
        }
        return true;
    }
}
