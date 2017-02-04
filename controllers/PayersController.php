<?php

namespace app\controllers;

use Yii;
use app\models\Payers;
use app\models\PayersCooperateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Cooperate;
use app\models\Organization;
use app\models\CertGroup;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PayersController implements the CRUD actions for Payers model.
 */
class PayersController extends Controller
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
     * Lists all Payers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PayersCooperateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Payers();
        $user = new User();

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }

       if($user->load(Yii::$app->request->post()) && $user->validate() && $model->load(Yii::$app->request->post()) && $model->validate()) {

           //return var_dump($user->password);

           if (!$user->password) {
               $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
               $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
           }
           else {
               $password = $user->password;
               $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
           }

           if ($user->save()) {
               $userRole = Yii::$app->authManager->getRole('payer');
               Yii::$app->authManager->assign($userRole, $user->id);
           
               $model->user_id = $user->id;
               //$model->mun = implode(",", $model->mun);
               $model->directionality = $model->directionality_1rob.",".$model->directionality_1.",".$model->directionality_2.",".$model->directionality_3.",".$model->directionality_4.",".$model->directionality_5.",".$model->directionality_6;

               if ($model->save()) {
                   
                   $groups = Yii::$app->params['groups'];
                   
                   foreach ($groups as $value) {
                       $group = new CertGroup();
                       $group->payer_id = $model->id;
                       $group->group = $value[0];
                       $group->nominal = $value[1];
                       $group->save();
                    }
                   
                    $user->password = $password;
                    return $this->render('/user/view', [
                        'model' => $user,
                    ]);
                }
           }
           else {
                return $this->render('create', [
                    'model' => $model,
                    'user' => $user,
                ]);
            }
        }

        return $this->render('create', [
                'model' => $model,
                'user' => $user,
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

        if($user->load(Yii::$app->request->post())) {
            if($model->load(Yii::$app->request->post())) {

                //$model->mun = implode(",", $model->mun);

                $model->directionality = $model->directionality_1.",".$model->directionality_2.",".$model->directionality_3.",".$model->directionality_4.",".$model->directionality_5.",".$model->directionality_6;

                $model->validate();
                $model->save();
            }

            if ($user->newlogin == 1 || $user->newpass == 1) {

                if ($user->newpass == 1) {
                   if (!$user->password) {
                       $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                       $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                   }
                   else {
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

        if($model->load(Yii::$app->request->post())) {

            //$model->mun = implode(",", $model->mun);

            $model->directionality = $model->directionality_1rob.",".$model->directionality_1.",".$model->directionality_2.",".$model->directionality_3.",".$model->directionality_4.",".$model->directionality_5.",".$model->directionality_6;

            if ($model->validate() && $model->save()) {
                return $this->redirect(['/payers/view', 'id' => $model->id]);
            }
        }

        //$model->mun = explode(',', $model->mun);

        $model->directionality = explode(',', $model->directionality);
        if (in_array('Техническая (робототехника)', $model->directionality)) { $model->directionality_1rob = 'Техническая (робототехника)'; }
        if (in_array('Техническая (иная)', $model->directionality)) { $model->directionality_1 = 'Техническая (иная)'; }
        if (in_array('Естественнонаучная', $model->directionality)) { $model->directionality_2 = 'Естественнонаучная'; }
        if (in_array('Физкультурно-спортивная', $model->directionality)) { $model->directionality_3 = 'Физкультурно-спортивная'; }
        if (in_array('Художественная', $model->directionality)) { $model->directionality_4 = 'Художественная'; }
        if (in_array('Туристско-краеведческая', $model->directionality)) { $model->directionality_5 = 'Туристско-краеведческая'; }
        if (in_array('Социально-педагогическая', $model->directionality)) { $model->directionality_6 = 'Социально-педагогическая'; }


        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);

    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())) {

            //$model->mun = implode(",", $model->mun);

            $model->directionality = $model->directionality_1.",".$model->directionality_2.",".$model->directionality_3.",".$model->directionality_4.",".$model->directionality_5.",".$model->directionality_6;

            if ($model->validate() && $model->save()) {
                return $this->redirect('/personal/payer-statistic');
            }
        }

        $model->directionality = explode(',', $model->directionality);
        if (in_array('Техническая', $model->directionality)) { $model->directionality_1 = 'Техническая'; }
        if (in_array('Естественнонаучная', $model->directionality)) { $model->directionality_2 = 'Естественнонаучная'; }
        if (in_array('Физкультурно-спортивная', $model->directionality)) { $model->directionality_3 = 'Физкультурно-спортивная'; }
        if (in_array('Художественная', $model->directionality)) { $model->directionality_4 = 'Художественная'; }
        if (in_array('Туристско-краеведческая', $model->directionality)) { $model->directionality_5 = 'Туристско-краеведческая'; }
        if (in_array('Социально-педагогическая', $model->directionality)) { $model->directionality_6 = 'Социально-педагогическая'; }

        return $this->render('edit', [
            'model' => $model,
        ]);

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

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                $model = $this->findModel($id);

                User::findOne($model['user_id'])->delete();

                return $this->redirect(['/personal/operator-payers']);
            }
            else {
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
