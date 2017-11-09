<?php

namespace app\controllers;

use app\models\Groups;
use Yii;
use app\models\MunicipalTaskContract;
use app\models\search\MunicipalTaskContractSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MunicipalTaskContractController implements the CRUD actions for MunicipalTaskContract model.
 */
class MunicipalTaskContractController extends Controller
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
     * Displays a single MunicipalTaskContract model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->pdf = $model->generatePdf();
            if ($model->approve()) {
                Yii::$app->session->setFlash('success', 'Вы успешно одобрили заявку.');
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('approve', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new MunicipalTaskContract model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($groupId)
    {
        if (!$group = Groups::findOne($groupId)) {
            return $this->redirect(['/personal/certificate-programs']);
        }

        $certificate = Yii::$app->user->identity->certificate;
        if ($group->program->canCreateMunicipalTaskContract($certificate)) {
            $model = new MunicipalTaskContract([
                'certificate_id' => $certificate->id,
                'payer_id' => $certificate->payer_id,
                'organization_id' => $group->organization_id,
                'program_id' => $group->program_id,
                'group_id' => $group->id,
            ]);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно подали заявку на участие в муниципальном задании.');
            }
        }

        return $this->redirect(['/programs/view-task', 'id' => $group->program_id]);
    }

    /**
     * Updates an existing MunicipalTaskContract model.
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
     * Deletes an existing MunicipalTaskContract model.
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
     * Finds the MunicipalTaskContract model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MunicipalTaskContract the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MunicipalTaskContract::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
