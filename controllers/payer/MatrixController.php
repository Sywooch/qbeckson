<?php

namespace app\controllers\payer;

use yii\base\Model;
use app\models\forms\MatrixForm;
use Yii;
use yii\web\Controller;

class MatrixController extends Controller
{
    public function actionParams()
    {
        $model = new MatrixForm(Yii::$app->user->identity->payer->id);

        if (Model::loadMultiple($model->matrix, Yii::$app->request->post()) && Model::validateMultiple($model->matrix)) {
            foreach ($model->matrix as $item) {
                $item->save(false);
            }
            Yii::$app->session->setFlash('success', 'Параметры успешно обновлены.');

            return $this->refresh();
        }

        return $this->render('params', [
            'model' => $model,
        ]);
    }
}
