<?php

namespace app\controllers\admin;

use Yii;
use app\models\statics\DirectoryProgramDirection;
use app\models\statics\DirectoryProgramActivity;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * DirectoryProgramActivityController implements the CRUD actions for DirectoryProgramActivity model.
 */
class DirectoryProgramActivityController extends BaseAdminController
{
    /**
     * @return string
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() .
            DIRECTORY_SEPARATOR . 'admin/directory-program-direction/directory-program-activity';
    }

    /**
     * Creates a new DirectoryProgramActivity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $directionId
     * @return mixed
     * @throws HttpException
     */
    public function actionCreate($directionId)
    {
        $model = new DirectoryProgramActivity();
        $direction = DirectoryProgramDirection::findOne($directionId);
        if (!$direction) {
            throw new HttpException(400);
        }
        $model->direction_id = $direction->id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['admin/directory-program-direction/update', 'id' => $model->direction_id]);
        }
        return $this->render('create', [
            'model' => $model,
            'direction' => $direction,
        ]);
    }

    /**
     * Updates an existing DirectoryProgramActivity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['admin/directory-program-direction/update', 'id' => $model->direction_id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DirectoryProgramActivity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            return $this->redirect(['admin/directory-program-direction/update', 'id' => $model->direction_id]);
        }
        throw new \DomainException('Something wrong');
    }

    /**
     * Finds the DirectoryProgramActivity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DirectoryProgramActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DirectoryProgramActivity::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
