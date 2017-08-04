<?php

namespace app\controllers;

use app\models\OrganizationPayerAssignment;
use Yii;
use app\models\Organization;
use app\models\Contracts;
use app\models\Certificates;
use app\models\Informs;
use app\models\OrganizationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Mun;
use app\models\Programs;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\DynamicModel;

/**
 * OrganizationController implements the CRUD actions for Organization model.
 */
class OrganizationController extends Controller
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
     * Lists all Organization models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organization model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if (isset($roles['certificate'])) {

            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();

            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('cooperate')
                ->where(['payer_id' => $certificate['payer_id']])
                ->andWhere(['organization_id' => $model['id']])
                ->andWhere(['status' => 1])
                ->count();
            if ($rows == 0) {
                Yii::$app->session->setFlash('warning', 'К сожалению, на данный момент Вы не можете записаться на обучение в данную организацию. Уполномоченная организация пока не заключила с ней необходимое соглашение.');
            }
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Organization model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewSubordered($id)
    {
        $model = $this->findModel($id);

        return $this->render('view-subordered', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Organization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Organization();
        $user = new User();

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        if ($user->load(Yii::$app->request->post()) && $user->validate() && $model->load(Yii::$app->request->post())) {

            //return var_dump($user->password);

            if (!$user->password) {
                $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            } else {
                $password = $user->password;
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            }

            if ($user->save()) {
                $userRole = Yii::$app->authManager->getRole('organizations');
                Yii::$app->authManager->assign($userRole, $user->id);

                $model->user_id = $user->id;
                $model->actual = 1;
                $model->cratedate = date("Y-m-d");

                $mun = Mun::findOne($model->mun);

                $model->max_child = floor((($mun->deystv / ($mun->countdet * 0.7)) * Yii::$app->coefficient->data->potenc) * $model->last);

                if ($model->save()) {
                    $model->link('operators', Yii::$app->operator->identity);
                    $user->password = $password;

                    return $this->render('/user/view', [
                        'model' => $user,
                    ]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionRequest()
    {
        // TODO: Разобраться с правами, as accessbehavior из конфига не работает, так что костыль
        if (!Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Недостаточно прав');
        }

        $model = new Organization([
            'status' => Organization::STATUS_NEW,
            'scenario' => Organization::SCENARIO_GUEST,
            'actual' => 0,
            'cratedate' => date("Y-m-d"),
            'anonymous_update_token' => Yii::$app->security->generateRandomString(10),
        ]);
        $user = new User([
            'username' => Yii::$app->security->generateRandomString(6),
            'password' => Yii::$app->security->generatePasswordHash(Yii::$app->security->generateRandomString(6), 10),
            'auth_key' => Yii::$app->security->generateRandomString(),
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user->save()) {
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole('organizations'), $user->id);
                $model->user_id = $user->id;
                $model->max_child = floor((($model->municipality->deystv / ($model->municipality->countdet * 0.7)) * Yii::$app->coefficient->data->potenc) * $model->last);

                if ($model->save(false)) {
                    $model->sendRequestEmail();
                    Yii::$app->session->setFlash('success', 'Вы успешно отправили заявку на регистрацию поставщика образовательных услуг!');

                    return $this->redirect(['/site/index']);
                }
            }

            Yii::$app->session->setFlash('danger', 'Ошибка при отправке заявки на регистрацию.');
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

    public function actionRequestUpdate($token)
    {
        // TODO: Разобраться с правами, as accessbehavior из конфига не работает, так что костыль
        if (!Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Недостаточно прав');
        }

        $model = $this->findModelByToken($token);
        $model->scenario = Organization::SCENARIO_GUEST;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setNew();
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Вы успешно отредактировали заявку на регистрацию поставщика образовательных услуг!');

            return $this->redirect(['/site/index']);
        }

        return $this->render('request-update', [
            'model' => $model,
        ]);
    }

    public function actionCheckStatus($token = null)
    {
        // TODO: Разобраться с правами, as accessbehavior из конфига не работает, так что костыль
        if (!Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Недостаточно прав');
        }

        if (!empty($token)) {
            $model = DynamicModel::validateData(compact('token'), [
                ['token', 'string', 'min' => 1],
                ['token', 'exist', 'targetClass' => Organization::className(), 'targetAttribute' => 'anonymous_update_token'],
            ]);
            if ($model->hasErrors()) {
                Yii::$app->session->setFlash('danger', 'Заявки с указанным номером не найдено.');

                return $this->redirect(['check-status']);
            } else {
                $this->redirect(['request-update', 'token' => $token]);
            }
        }

        return $this->render('check-status');
    }

    /**
     * Updates an existing Organization model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = $model->user;
        if ($model->status == Organization::STATUS_NEW) {
            $model->scenario = Organization::SCENARIO_MODERATOR;
            $user->newpass = 1;
        }

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        $showUserInfo = false;

        if ($user->load(Yii::$app->request->post()) && ($user->newlogin > 0 || $user->newpass > 0)) {
            $password = null;
            if ($user->newpass > 0) {
                if (!$user->password) {
                    $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                    $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                } else {
                    $password = $user->password;
                    $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                }
            }

            if ($user->save()) {
                $showUserInfo = true;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->isModerating) {
                $post = Yii::$app->request->post();
                if (isset($post['accept-button'])) {
                    $model->setActive();

                } elseif (isset($post['refuse-button'])) {
                    $model->setRefused();
                }
                $model->sendModerateEmail($password);
            }

            $model->save(false);

            if ($showUserInfo === true) {
                return $this->render('/user/view', [
                    'model' => $user,
                    'password' => $password,
                ]);
            }

            return $this->redirect(['/organization/view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionCancelSubording($id)
    {
        $model = $this->findModel($id);
        $suborder = Yii::$app->user->identity->payer->getOrganizations($model->id)->one();
        if ($suborder->status == OrganizationPayerAssignment::STATUS_PENDING) {
            $model->unlink('suborderPayer', Yii::$app->user->identity->payer, true);
        }

        $this->redirect(['organization/view-subordered', 'id' => $id]);
    }

    public function actionSetAsSubordinated($id)
    {
        $model = $this->findModel($id);
        if ($model->canBeSubordered(Yii::$app->user->identity->payer)) {
            $model->link('suborderPayer', Yii::$app->user->identity->payer);
            Yii::$app->session->setFlash('success', 'Запрос на указание подведомственности успешно послан организации. Пожалуйста, дождитесь подтверждения!');
        }

        $this->redirect(['organization/view-subordered', 'id' => $id]);
    }

    public function actionEdit()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $model = $this->findModel($organization['id']);

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['/personal/organization-info', 'id' => $model->id]);
        }

        return $this->render('edit', [
            'model' => $model,
        ]);

    }

    public function actionNewlimit($id)
    {
        $model = $this->findModel($id);

        $mun = Mun::findOne($model->mun);

        $lastyear = date("Y") - 1;
        $llastyear = date("Y") - 2;

        if (date("m") >= 9) {
            $mindate = $lastyear . '-09-01';
            $maxdate = date("Y") . '-09-01';
        } else {
            $mindate = $llastyear . '-09-01';
            $maxdate = $lastyear . '-09-01';
        }

        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['organization_id' => $model->id])
            ->andWhere(['<', 'date_termnate', $maxdate])
            ->andWhere(['>', 'date_termnate', $mindate])
            ->count();

        $model->last_year_contract = $contracts;

        if ($model->raiting < Yii::$app->coefficient->data->ngr) {
            $coef_raiting = 0;
        }
        if ($model->raiting == null) {
            $coef_raiting = 1;
        }
        if ($model->raiting >= Yii::$app->coefficient->data->ngr and $model->raiting < Yii::$app->coefficient->data->sgr) {
            $coef_raiting = ($model->raiting - Yii::$app->coefficient->data->chr1) / Yii::$app->coefficient->data->zmr1;
        }
        if ($model->raiting >= Yii::$app->coefficient->data->sgr and $model->raiting < Yii::$app->coefficient->data->vgr) {
            $coef_raiting = 1;
        }
        if ($model->raiting > Yii::$app->coefficient->data->vgr) {
            $coef_raiting = ($model->raiting - Yii::$app->coefficient->data->chr2) / Yii::$app->coefficient->data->zmr2;
        }


        if ($model->cratedate >= $mindate) {
            $model->max_child = floor(((($mun->deystv / ($mun->countdet * 0.7)) * Yii::$app->coefficient->data->potenc) * $model->last) * $coef_raiting);
        } else {
            $model->max_child = floor(($model->last_year_contract * ($mun->deystv / $mun->lastdeystv)) * $coef_raiting);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['/organization/view', 'id' => $model->id]);
        }

        return $this->render('newlimit', [
            'model' => $model,
        ]);

    }

    public function actionAlllimit()
    {
        $org = (new \yii\db\Query())
            ->select(['id'])
            ->from('organization')
            ->column();

        foreach ($org as $organization_id) {

            $model = $this->findModel($organization_id);

            $lastyear = date("Y") - 1;
            $llastyear = date("Y") - 2;

            if (date("m") >= 9) {
                $mindate = $lastyear . '-09-01';
                $maxdate = date("Y") . '-09-01';
            } else {
                $mindate = $llastyear . '-09-01';
                $maxdate = $lastyear . '-09-01';
            }

            $contracts = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['organization_id' => $model->id])
                ->andWhere(['<', 'date_termnate', $maxdate])
                ->andWhere(['>', 'date_termnate', $mindate])
                ->count();

            $model->last_year_contract = $contracts;


            $mun = Mun::findOne($model->mun);

            if ($model->raiting < Yii::$app->coefficient->data->ngr) {
                $coef_raiting = 0;
            }
            if ($model->raiting == null) {
                $coef_raiting = 1;
            }
            if ($model->raiting >= Yii::$app->coefficient->data->ngr and $model->raiting < Yii::$app->coefficient->data->sgr) {
                $coef_raiting = ($model->raiting - Yii::$app->coefficient->data->chr1) / Yii::$app->coefficient->data->zmr1;
            }
            if ($model->raiting >= Yii::$app->coefficient->data->sgr and $model->raiting < Yii::$app->coefficient->data->vgr) {
                $coef_raiting = 1;
            }
            if ($model->raiting > Yii::$app->coefficient->data->vgr) {
                $coef_raiting = ($model->raiting - Yii::$app->coefficient->data->chr2) / Yii::$app->coefficient->data->zmr2;
            }


            if ($model->cratedate >= $mindate) {
                $model->max_child = floor(((($mun->deystv / ($mun->countdet * 0.7)) * Yii::$app->coefficient->data->potenc) * $model->last) * $coef_raiting);
            } else {
                $model->max_child = floor(($model->last_year_contract * ($mun->deystv / $mun->lastdeystv)) * $coef_raiting);
            }


            $model->save();
        }

        return $this->redirect(['/personal/operator-organizations']);
    }

    public function actionRaiting($id)
    {
        $model = $this->findModel($id);

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $programs = (new \yii\db\Query())
            ->select(['id'])
            ->from('programs')
            ->where(['organization_id' => $model->id])
            ->andWhere(['>', 'rating', 0])
            ->column();


        $count = 0;
        $count2 = 0;
        foreach ($programs as $program) {
            $model_program = Programs::findOne($program);

            $count += $model_program->rating * $model_program->last_contracts;
            $count2 += $model_program->last_contracts;

        }


        //return var_dump();
        if ($count2 != 0) {
            $model->raiting = round($count / $count2, 2);
        } else {
            $model->raiting = null;
        }


        if ($model->save()) {
            return $this->redirect(['/organization/view', 'id' => $model->id]);
        }
    }

    public function actionAllraiting()
    {
        $org = (new \yii\db\Query())
            ->select(['id'])
            ->from('organization')
            ->column();

        foreach ($org as $organization_id) {

            $model = $this->findModel($organization_id);

            $programs = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['organization_id' => $model->id])
                ->andWhere(['>', 'rating', 0])
                ->column();

            $count = 0;
            $count2 = 0;
            foreach ($programs as $program) {
                $model_program = Programs::findOne($program);

                $count += $model_program->rating * $model_program->last_contracts;
                $count2 += $model_program->last_contracts;

            }

            if ($count2 != 0) {
                $model->raiting = round($count / $count2, 2);
            } else {
                $model->raiting = null;
            }

            $model->save();
        }

        return $this->redirect(['/personal/operator-organizations']);
    }

    public function actionActual($id)
    {
        $model = $this->findModel($id);

        $model->actual = 1;

        $model->save();

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionNoactual($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {

                $model = $this->findModel($id);

                $zajavki = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['organization_id' => $model->id])
                    ->andWhere(['status' => 0])
                    ->column();

                foreach ($zajavki as $value) {
                    $contract = Contracts::findOne($value);

                    $cert = Certificates::findOne($contract->certificate_id);
                    $cert->balance = $cert->balance + $contract->rezerv;
                    $cert->rezerv = $cert->rezerv - $contract->rezerv;
                    $cert->save();

                    $contract->rezerv = 0;
                    $contract->status = 2;

                    if ($contract->save()) {
                        $inform = new Informs();
                        $inform->program_id = $contract->program_id;
                        $inform->contract_id = $contract->id;
                        $inform->prof_id = $contract->certificate_id;
                        $inform->text = 'Заявка отменена. Причина: приостановлена деятельность организации';
                        $inform->from = 4;
                        $inform->date = date("Y-m-d");
                        $inform->read = 0;

                        $inform->save();
                    }
                }


                $model->actual = 0;

                $model->save();

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/personal/operator-organizations']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
            'title' => 'Приостановить деятельность организации',
        ]);
    }

    /**
     * Deletes an existing Organization model.
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

                return $this->redirect(['/personal/operator-organizations']);
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/personal/operator-organizations']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
        ]);
    }

    /**
     * Finds the Organization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Organization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Organization::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelByToken($token)
    {
        if (($model = Organization::findOne(['anonymous_update_token' => $token])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
