<?php

namespace app\modules\reports\controllers;

use app\modules\reports\models\DuplicateComplitnesses;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * DuplicateComplitnessesController implements the CRUD actions for Contracts model.
 */
class DuplicateComplitnessesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    /**
     * Lists all Contracts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DuplicateComplitnesses();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
