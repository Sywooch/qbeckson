<?php

namespace app\controllers;

use Yii;
use app\models\Disputes;
use app\models\DisputesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DisputesController implements the CRUD actions for Disputes model.
 */
class DisputesController extends Controller
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
     * Lists all Disputes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DisputesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Disputes model.
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
     * Creates a new Disputes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Disputes();
        
        $disputes = (new \yii\db\Query())
            ->select(['id'])
            ->from('disputes')
            ->where(['contract_id' => $id])
            ->orderBy(['date' => SORT_ASC])
            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->contract_id = $id;
            $model->date = date("Y-m-d");
            $model->type = 1;
            $model->user_id = Yii::$app->user->id;

            if ($model->save()) {
                return $this->redirect(['/disputes/create', 'id' => $id]);   
            }
            
        } else {
            return $this->render('create', [
                'model' => $model,
                'contract' => $id,
                'disputes' => $disputes,
            ]);
        }
    }

        public function actionTerminate($id)
    {
        $model = new Disputes();

        $model->contract_id = $id;
        $model->month = date(m);
        $model->type = 2;

        if (!Yii::$app->user->isGuest) {
            $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            if ($roles['organizations']) {
                $model->from = 3;
                $model->validate();
                $model->save();
                return $this->redirect(['/personal/organization#panel4']);
            }
            if ($roles['certificate']) {
                $model->from = 4;
                $model->validate();
                $model->save();
                return $this->redirect(['/personal/certificate#panel3']);
            }
        }
    }

    /**
     * Updates an existing Disputes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Disputes model.
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
     * Finds the Disputes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Disputes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Disputes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
