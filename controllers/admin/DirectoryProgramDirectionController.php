<?php

namespace app\controllers\admin;

use Yii;
use app\models\statics\DirectoryProgramDirection;
use app\models\search\DirectoryProgramDirectionSearch;
use app\models\search\DirectoryProgramActivitySearch;
use yii\web\NotFoundHttpException;

/**
 * Class DirectoryProgramDirectionController
 * @package app\controllers\admin
 */
class DirectoryProgramDirectionController extends BaseAdminController
{
    /**
     * Lists all DirectoryProgramDirection models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DirectoryProgramDirectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new DirectoryProgramDirection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DirectoryProgramDirection();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DirectoryProgramDirection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchModel = new DirectoryProgramActivitySearch();
        $activityProvider = $searchModel->search(Yii::$app->request->queryParams);
        $activityProvider->query->andWhere(['direction_id' => $model->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'activityProvider' => $activityProvider,
        ]);
    }

    /**
     * Deletes an existing DirectoryProgramDirection model.
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
     * Finds the DirectoryProgramDirection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DirectoryProgramDirection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DirectoryProgramDirection::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
