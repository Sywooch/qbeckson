<?php

namespace app\controllers;

use app\components\EditableOperations;
use app\helpers\FlashHelper;
use app\models\module\ModuleNormativePriceCalculator;
use app\models\module\ModuleVerificator;
use app\models\ProgrammeModule;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ModuleController implements the CRUD actions for ProgrammeModule model.
 */
class ModuleController extends Controller
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
                    'save' => ['POST'],
                    'normpricesave' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProgrammeModule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProgrammeModule::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgrammeModule model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the ProgrammeModule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return ProgrammeModule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgrammeModule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCertificateCalcNormative($id)
    {
        $model = $this->findModel($id);
        $model->setVerificationWaitAndSave();
        if (Yii::$app->request->isPost) {
            $calculator = new ModuleNormativePriceCalculator($model);
            ($calculator->save()) || FlashHelper::flashFirst($calculator);
        }

        return $this->render('verificate/calcNormative', [
            'model' => $model,
        ]);

    }

    public function actionNormpricesave()
    {
        $programModuleSaveResult = EditableOperations::getInstance(
            Yii::$app->request->post(),
            ProgrammeModule::className()
        )->setAttributes('p21z', 'p22z', 'normative_price')
            ->exec();
        ($response = $programModuleSaveResult)
        || ($response = ['output' => '', 'message' => 'Неизвестная ошибка']);

        return $this->asJson($response);
    }

    public function actionSave($id)
    {
        $verificator = new ModuleVerificator($id);
        if (!$verificator->save()) {
            FlashHelper::flashFirst($verificator);
        }

        return $this->redirect('/personal/operator-programs');
    }

    /**
     * Updates an existing ProgrammeModule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())
            && $model->setNeedVerification()->save()
        ) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
}
