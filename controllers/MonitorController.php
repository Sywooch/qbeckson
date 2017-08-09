<?php

namespace app\controllers;

use app\models\UserMonitorAssignment;
use Yii;
use app\models\UserIdentity;
use app\models\search\MonitorSearch;
use app\models\UserForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MonitorController implements the CRUD actions for UserIdentity model.
 */
class MonitorController extends Controller
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
     * Lists all UserIdentity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MonitorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new UserIdentity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = $model->create()) {
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole(UserIdentity::ROLE_MONITOR), $user->id);
                $um = new UserMonitorAssignment([
                    'user_id' => Yii::$app->user->id,
                    'monitor_id' => $user->id,
                    'access_rights' => $model->accessRights,
                ]);
                $um->save();
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserIdentity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->model->userMonitorAssignment->access_rights = $model->accessRights;
            $model->model->userMonitorAssignment->save();

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserIdentity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserIdentity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserIdentity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserIdentity::findOne($id)) !== null) {
            $userModel = new UserForm(['model' => $model]);

            return $userModel;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
