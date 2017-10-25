<?php

namespace app\controllers\payer;

use app\models\forms\MatrixForm;
use app\models\MunicipalTaskMatrix;
use Yii;
use yii\web\Controller;

class MatrixController extends Controller
{
    public function actionParams()
    {
        $model = new MatrixForm();
        print_r($model->matrix);exit;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }
}
