<?php

namespace app\controllers\operator;

use Yii;
use app\models\OperatorSettings;
use yii\web\Controller;

class OperatorSettingsController extends Controller
{
    public function actionIndex()
    {
        if (null === ($model = OperatorSettings::findOne(['operator_id' => Yii::$app->user->getIdentity()->operator->id]))) {
            $model = new OperatorSettings([
                'operator_id' => Yii::$app->user->getIdentity()->operator->id
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }
}
