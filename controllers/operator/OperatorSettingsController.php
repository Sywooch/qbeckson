<?php

namespace app\controllers\operator;

use Yii;
use app\models\OperatorSettings;
use app\models\search\OperatorSettingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OperatorSettingsController implements the CRUD actions for OperatorSettings model.
 */
class OperatorSettingsController extends Controller
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
     * Creates a new OperatorSettings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
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
