<?php

namespace app\controllers;

use app\models\CertificateGroupQueue;
use app\models\Certificates;
use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\CertGroup;
use app\models\search\CertGroupSearch;

/**
 * CertGroupController implements the CRUD actions for CertGroup model.
 */
class CertGroupController extends Controller
{
    /**
     * Lists all CertGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CertGroupSearch(['payerId' => Yii::$app->user->identity->payer->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax && Yii::$app->request->post('hasEditable')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            $model = $this->findModel($post['editableKey']);
            $data = ['CertGroup' => current($post['CertGroup'])];

            if ($model->load($data) && $model->validate()) {
                if ((isset($data['CertGroup']['nominal']) || isset($data['CertGroup']['nominal_f'])) &&
                    (
                        empty($post['password']) ||
                        !Yii::$app->security->validatePassword($post['password'], Yii::$app->user->identity->password)
                    )
                ) {
                    return ['output' => '', 'message' => 'Неверный пароль.'];
                }

                if ($model->oldAttributes['amount'] != $model->amount) {
                    $certGroupCount = Certificates::getCountCertGroup($model->id);
                    $vacancies = $model->amount - $certGroupCount;
                    if ($vacancies > 0 && $queue = CertificateGroupQueue::getByCertGroup($model->id, $vacancies)) {
                        foreach ($queue as $item) {
                            $item->removeFromCertQueue();
                        }
                    }
                }

                $model->save(false);
            } else {
                $out = ['output' => $output, 'message' => 'Ошибка при сохранении.'];
            }

            return ['output' => '', 'message' => 'Ошибка валидации'];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CertGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CertGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the CertGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CertGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CertGroup::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
