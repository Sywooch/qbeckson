<?php

namespace app\controllers;

use Yii;
use app\models\Cooperate;
use app\models\CooperateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Organization;
use app\models\Payers;
use app\models\User;

/**
 * CooperateController implements the CRUD actions for Cooperate model.
 */
class CooperateController extends Controller
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
     * Lists all Cooperate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CooperateSearch([
            'status' => 1
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cooperate model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViews($id)
    {
         $payers = new Payers();
        $payer = $payers->getPayer();

        $cooperate = (new \yii\db\Query())
                ->select(['id'])
                ->from('cooperate')
                ->where(['organization_id' => $id])
                ->andWhere(['payer_id' => $payer['id']])
                ->andWhere([ '<', 'status', 2])
                ->one();

        return $this->render('view', [
            'model' => $this->findModel($cooperate['id']),
        ]);
    }

    /**
     * Creates a new Cooperate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Cooperate();

        if ($model->load(Yii::$app->request->post())) {
            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $model->payer_id = $id;
            $model->organization_id = $organization['id'];
            $model->status = 0;

            if ($model->validate() && $model->save()) {
                return $this->redirect('/personal/organization-payers#panel2');
            }

        } else {
            return $this->render('create', [
                'model' => $model,
                'id' => $id,
            ]);
        }
    }

    /**
     * Updates an existing Cooperate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/personal/organization-payers']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Cooperate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
         $user = User::findOne(Yii::$app->user->id);

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                $organizations = new Organization();
                $organization = $organizations->getOrganization();

                $cooperate = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('cooperate')
                        ->where(['organization_id' => $organization['id']])
                        ->andWhere(['payer_id' => $id])
                        ->andWhere(['status' => 0])
                        ->one();

                $this->findModel($cooperate['id'])->delete();

                return $this->redirect(['/personal/organization-payers#panel2']);
            }
            else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
                 return $this->redirect(['/personal/organization-payers#panel2']);
            }
        }
        return $this->render('/user/delete', [
            'user' => $user,
        ]);
    }

    public function actionDecooperate($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                $organizations = new Organization();
                $organization = $organizations->getOrganization();

                $cooperate = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('cooperate')
                        ->where(['organization_id' => $organization['id']])
                        ->andWhere(['payer_id' => $id])
                        ->andWhere(['status' => 1])
                        ->one();

                $model = $this->findModel($cooperate['id']);
                $model->status = 2;
                $model->date_dissolution = date("Y-m-d");

                if ($model->save()) {
                    return $this->redirect('/personal/organization-payers');
                }
            }
            else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
                 return $this->redirect(['/personal/organization-payers']);
            }
        }
        return $this->render('/user/delete', [
            'user' => $user,
        ]);
    }

    public function actionRead($id)
    {
        $model = Cooperate::findOne($id);
        $model->reade = 1;

        if ($model->save()) {
            return $this->redirect('/personal/payer-organizations');
        }
    }

    public function actionOkpayer($id)
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $cooperate = (new \yii\db\Query())
            ->select(['id'])
            ->from('cooperate')
            ->where(['organization_id' => $id])
            ->andWhere(['payer_id' => $payer['id']])
            ->andWhere(['status' => 0])
            ->one();

        $model = Cooperate::findOne($cooperate['id']);
        $model->status = 1;

        if ($model->save()) {
            return $this->redirect('/personal/payer-organizations');
        }
    }

    public function actionNopayer($id)
    {
     $user = User::findOne(Yii::$app->user->id);

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                
        $payers = new Payers();
        $payer = $payers->getPayer();

        $cooperate = (new \yii\db\Query())
            ->select(['id'])
            ->from('cooperate')
            ->where(['organization_id' => $id])
            ->andWhere(['payer_id' => $payer['id']])
            ->andWhere([ '<', 'status', 2])
            ->one();

        $model = Cooperate::findOne($cooperate['id']);
        $model->status = 2;

        if ($model->save()) {
            return $this->redirect('/personal/payer-organizations');
        }
                 }
            else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
                 return $this->redirect(['/personal/organization-payers#panel2']);
            }
        }
        return $this->render('/user/delete', [
            'user' => $user,
            'title' => 'Расторгнуть соглашение',
        ]);
    }
    

    /**
     * Finds the Cooperate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cooperate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cooperate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
