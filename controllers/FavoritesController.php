<?php

namespace app\controllers;

use Yii;
use app\models\Favorites;
use app\models\FavoritesSearch;
use app\models\Certificates;
use app\models\Previus;
use app\models\ProgrammeModule;
use app\models\Programs;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FavoritesController implements the CRUD actions for Favorites model.
 */
class FavoritesController extends Controller
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
     * Lists all Favorites models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FavoritesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Favorites model.
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
     * Creates a new Favorites model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Favorites();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionNew($id)
    {
        $model = new Favorites();

        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        $program = Programs::findOne($id);

        $model->certificate_id = $certificate['id'];
        $model->program_id = $id;
        $model->organization_id = $program['organization_id'];
        $model->type = 1;

        if ($model->validate() && $model->save()) {
            return $this->redirect('/programs/search');
        }
    }
    
    
    public function actionTerminate($id)
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();
        
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('favorites')
            ->where(['program_id' => $id])
            ->andWhere(['certificate_id' => $certificate->id])
            ->one();
        
        $this->findModel($rows['id'])->delete();

            return $this->redirect('/programs/search');
    }
    
    public function actionTerminate2($id)
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();
        
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('favorites')
            ->where(['program_id' => $id])
            ->andWhere(['certificate_id' => $certificate->id])
            ->one();
        
        $this->findModel($rows['id'])->delete();

            return $this->redirect('/personal/certificate-favorites');
    }
    
    public function actionPrev($id)
    {
        $model = new Previus();

        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        $year = ProgrammeModule::findOne($id);
        $program = Programs::findOne($year->program_id);

        $model->certificate_id = $certificate['id'];
        $model->year_id = $id;
        $model->organization_id = $program['organization_id'];
        $model->program_id = $program['id'];
        $model->actual = 1;

        if ($model->validate() && $model->save()) {
            return $this->redirect(['/programs/view', 'id' => $year->program_id]);
        }
    }
    
    public function actionDisprev($id)
    {
        $year = ProgrammeModule::findOne($id);
        
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('previus')
            ->where(['year_id' => $year->id])
            ->andWhere(['actual' => 1])
            ->one();
        
        $model = Previus::findOne($rows['id']);

        $model->actual = 0;

        if ($model->validate() && $model->save()) {
            return $this->redirect(['/programs/view', 'id' => $year->program_id]);
        }
    }
    /**
     * Updates an existing Favorites model.
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
     * Deletes an existing Favorites model.
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
     * Finds the Favorites model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Favorites the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Favorites::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
