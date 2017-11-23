<?php

namespace app\controllers;

use app\models\CertGroup;
use app\models\Cooperate;
use app\models\forms\ConfirmRequestForm;
use app\models\forms\CooperateChangeTypeForm;
use app\models\OperatorSettings;
use app\models\Payers;
use app\models\PayersSearch;
use app\models\User;
use app\traits\AjaxValidationTrait;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PayersController implements the CRUD actions for Payers model.
 */
class PayersController extends Controller
{
    use AjaxValidationTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Payers models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();
        $allPayers = ArrayHelper::getColumn(Payers::find()->asArray()->all(), 'id');
        $currentPayers = ArrayHelper::getColumn($user->organization->cooperates, 'payer_id');

        $searchModel = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates'   => '0,100',
            'id'           => array_diff($allPayers, $currentPayers),
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payers model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $payer = $this->findModel($id);

        /** @var OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $confirmRequestForm = new ConfirmRequestForm;
        $this->performAjaxValidation($confirmRequestForm);

        $activeCooperate = $payer->getCooperation(Cooperate::STATUS_ACTIVE);

        if (\Yii::$app->user->can('organizations') && $confirmRequestForm->load(Yii::$app->request->post())) {
            $confirmRequestForm->setModel($activeCooperate);

            if ($confirmRequestForm->changeCooperateType()) {
                Yii::$app->session->setFlash('success', 'Вы успешно изменили соглашение.');
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при изменении соглашения.');
            }

            return $this->refresh();
        }

        return $this->render('view', [
            'model' => $payer,
            'confirmRequestForm' => $confirmRequestForm,
            'operatorSettings' => $operatorSettings,
            'activeCooperateExists' => $activeCooperate ? true: false,
        ]);
    }

    /**
     * Creates a new Payers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Payers(['operator_id' => Yii::$app->operator->identity->id]);
        $user = new User();

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        if ($user->load(Yii::$app->request->post()) && $user->validate() && $model->load(Yii::$app->request->post()) && $model->validate()) {

            //return var_dump($user->password);

            if (!$user->password) {
                $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            } else {
                $password = $user->password;
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            }

            if ($user->save()) {
                $userRole = Yii::$app->authManager->getRole('payer');
                Yii::$app->authManager->assign($userRole, $user->id);

                $model->user_id = $user->id;
                //$model->mun = implode(",", $model->mun);
                $model->directionality = $model->directionality_1rob . "," . $model->directionality_1 . "," . $model->directionality_2 . "," . $model->directionality_3 . "," . $model->directionality_4 . "," . $model->directionality_5 . "," . $model->directionality_6;

                if ($model->save()) {
                    foreach (Yii::$app->params['groups'] as $value) {
                        $group = new CertGroup();
                        $group->payer_id = $model->id;
                        $group->group = $value[0];
                        $group->amount = 0;
                        $group->nominal = $value[1];
                        $group->nominal_f = $value[1];
                        $group->is_special = !empty($value[2]) ? 1 : null;
                        $group->save();
                    }

                    return $this->render('/user/view', [
                        'model'    => $user,
                        'password' => $password,
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'user'  => $user,
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'user'  => $user,
        ]);
    }

    /**
     * Updates an existing Payers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = User::findOne($model->user_id);

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        if ($user->load(Yii::$app->request->post())) {
            if ($model->load(Yii::$app->request->post())) {

                $model->save();
            }

            if ($user->newlogin == 1 || $user->newpass == 1) {

                if ($user->newpass == 1) {
                    if (!$user->password) {
                        $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                        $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                    } else {
                        $password = $user->password;
                        $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                    }
                }

                if ($user->validate() && $user->save()) {
                    $user->password = $password;

                    return $this->render('/user/view', [
                        'model' => $user,
                    ]);
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return $this->redirect(['/payers/view', 'id' => $model->id]);
            }
        }

        //$model->mun = explode(',', $model->mun);

        $model->directionality = explode(',', $model->directionality);
        if (in_array('Техническая (робототехника)', $model->directionality)) {
            $model->directionality_1rob = 'Техническая (робототехника)';
        }
        if (in_array('Техническая (иная)', $model->directionality)) {
            $model->directionality_1 = 'Техническая (иная)';
        }
        if (in_array('Естественнонаучная', $model->directionality)) {
            $model->directionality_2 = 'Естественнонаучная';
        }
        if (in_array('Физкультурно-спортивная', $model->directionality)) {
            $model->directionality_3 = 'Физкультурно-спортивная';
        }
        if (in_array('Художественная', $model->directionality)) {
            $model->directionality_4 = 'Художественная';
        }
        if (in_array('Туристско-краеведческая', $model->directionality)) {
            $model->directionality_5 = 'Туристско-краеведческая';
        }
        if (in_array('Социально-педагогическая', $model->directionality)) {
            $model->directionality_6 = 'Социально-педагогическая';
        }


        return $this->render('update', [
            'model' => $model,
            'user'  => $user,
        ]);

    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');

                return $this->redirect('/personal/payer-statistic');
            }
        }

        $model->directionality = explode(',', $model->directionality);

        if (in_array('Техническая (робототехника)', $model->directionality)) {
            $model->directionality_1rob = 'Техническая (робототехника)';
        }
        if (in_array('Техническая (иная)', $model->directionality)) {
            $model->directionality_1 = 'Техническая (иная)';
        }
        if (in_array('Естественнонаучная', $model->directionality)) {
            $model->directionality_2 = 'Естественнонаучная';
        }
        if (in_array('Физкультурно-спортивная', $model->directionality)) {
            $model->directionality_3 = 'Физкультурно-спортивная';
        }
        if (in_array('Художественная', $model->directionality)) {
            $model->directionality_4 = 'Художественная';
        }
        if (in_array('Туристско-краеведческая', $model->directionality)) {
            $model->directionality_5 = 'Туристско-краеведческая';
        }
        if (in_array('Социально-педагогическая', $model->directionality)) {
            $model->directionality_6 = 'Социально-педагогическая';
        }

        return $this->render('edit', [
            'model' => $model,
        ]);

    }


    public function actionSaveParams()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            $model = Yii::$app->user->identity->payer;
            if ($model->load($post) && $model->save(false, ['certificate_can_use_future_balance'])) {
                return true;
            }
        }

        return null;
    }

    /**
     * Deletes an existing Payers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                $model = $this->findModel($id);

                User::findOne($model['user_id'])->delete();

                return $this->redirect(['/personal/operator-payers']);
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/personal/operator-payers']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
        ]);
    }

    /**
     * Finds the Payers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
