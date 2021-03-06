<?php

namespace app\controllers;

use app\models\Certificates;
use app\models\Contracts;
use app\models\Cooperate;
use app\models\forms\ConfirmRequestForm;
use app\models\forms\CooperateForFuturePeriodForm;
use app\models\forms\CooperateForFuturePeriodTypeForm;
use app\models\Informs;
use app\models\Mun;
use app\models\OperatorSettings;
use app\models\Organization;
use app\models\OrganizationPayerAssignment;
use app\models\Programs;
use app\models\search\OrganizationSearch;
use app\models\User;
use app\models\UserIdentity;
use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

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
                'class'   => VerbFilter::className(),
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
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organization model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $organization = $this->findModel($id);
        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR) && $organization->status === Organization::STATUS_REFUSED) {
            Yii::$app->session->setFlash('warning', 'Деятельность приостановлена, причина: ' . $organization->refuse_reason);
        }

        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
            && $organization->status === Organization::STATUS_NEW
            && $organization->refuse_reason) {
            Yii::$app->session->setFlash('info', 'Причина предыдущего отказа: ' . $organization->refuse_reason);
        }

        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            /** @var $certificate Certificates */
            $certificate = Yii::$app->user->identity->certificate;
            if (!count($organization->getCooperatesByPayerId($certificate->payer_id, 1))) {
                Yii::$app->session->setFlash('warning', 'К сожалению, на данный момент Вы не можете записаться на обучение в данную организацию. Уполномоченная организация пока не заключила с ней необходимое соглашение.');
            }
        }

        /** @var OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
            $currentPeriodCooperate = $organization->getCooperation(Cooperate::STATUS_ACTIVE, Cooperate::PERIOD_CURRENT);
            $futurePeriodCooperate = $organization->getCooperation(Cooperate::STATUS_ACTIVE, Cooperate::PERIOD_FUTURE);

            if ($currentPeriodCooperate) {
                $confirmRequestForm = new ConfirmRequestForm(['type' => $currentPeriodCooperate->document_type, 'value' => number_format($currentPeriodCooperate->total_payment_limit, 0, '', '')]);
            } else {
                $confirmRequestForm = new ConfirmRequestForm();
            }
            $cooperateForFuturePeriodTypeForm = $futurePeriodCooperate ? new CooperateForFuturePeriodTypeForm(['type' => $futurePeriodCooperate->document_type, 'maximumAmount' => number_format($futurePeriodCooperate->total_payment_limit, 0, '', '')]) : null;

            $cooperateForFuturePeriodForm = new CooperateForFuturePeriodForm();
//return $this->asJson([$cooperateForFuturePeriodForm->load(\Yii::$app->request->post()), $cooperateForFuturePeriodForm->useCurrentCooperateType]);
            if (Yii::$app->request->isAjax) {
                if ($cooperateForFuturePeriodForm->load(\Yii::$app->request->post())) {
                    $cooperateForFuturePeriodForm->setCurrentPeriodCooperate($currentPeriodCooperate);

                    return $this->asJson(ActiveForm::validate($cooperateForFuturePeriodForm));
                }

                if ($cooperateForFuturePeriodTypeForm && $cooperateForFuturePeriodTypeForm->load(\Yii::$app->request->post())) {
                    return $this->asJson(ActiveForm::validate($cooperateForFuturePeriodTypeForm));
                }

                if ($confirmRequestForm->load(Yii::$app->request->post())) {
                    return $this->asJson(ActiveForm::validate($confirmRequestForm));
                }
            }

            if ($cooperateForFuturePeriodForm->load(Yii::$app->request->post())) {
                $cooperateForFuturePeriodForm->createFuturePeriodCooperate($organization->id);
                $cooperateForFuturePeriodForm->setCurrentPeriodCooperate($currentPeriodCooperate);

                if ($cooperateForFuturePeriodForm->save()) {
                    \Yii::$app->session->setFlash('success', 'Договор на будущий период создан.');
                } else {
                    \Yii::$app->session->setFlash('error', 'Ошибка создания договора на будущий период.');
                }

                return $this->redirect(Url::to(['/organization/view', 'id' => $id]));
            }

            if ($cooperateForFuturePeriodTypeForm && $cooperateForFuturePeriodTypeForm->load(\Yii::$app->request->post())) {
                $cooperateForFuturePeriodTypeForm->setCooperate($futurePeriodCooperate);

                if ($cooperateForFuturePeriodTypeForm->changeCooperateType()) {
                    \Yii::$app->session->setFlash('success', 'Вы успешно изменили соглашение будущего периода.');
                } else {
                    \Yii::$app->session->setFlash('error', 'Возникла ошибка при изменении соглашения будущего периода.');
                }

                return $this->refresh();
            }

            if ($confirmRequestForm->load(Yii::$app->request->post())) {
                $confirmRequestForm->setModel($currentPeriodCooperate);
                if ($confirmRequestForm->changeCooperateType()) {
                    \Yii::$app->session->setFlash('success', 'Вы успешно изменили соглашение.');
                } else {
                    \Yii::$app->session->setFlash('error', 'Возникла ошибка при изменении соглашения.');
                }

                return $this->refresh();
            }
        }

        return $this->render('view', [
            'model' => $organization,
            'operatorSettings' => $operatorSettings,
            'confirmRequestForm' => $confirmRequestForm,
            'cooperateForFuturePeriodForm' => $cooperateForFuturePeriodForm,
            'currentPeriodCooperate' => $currentPeriodCooperate,
            'futurePeriodCooperate' => $futurePeriodCooperate,
            'cooperateForFuturePeriodTypeForm' => $cooperateForFuturePeriodTypeForm,
        ]);
    }

    /**
     * Finds the Organization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
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

    /**
     * Displays a single Organization model.
     *
     * @param integer $id
     *
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
            'user'  => $user,
        ]);
    }

    public function actionRequest()
    {
        // TODO: Разобраться с правами, as accessbehavior из конфига не работает, так что костыль
        if (!Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Недостаточно прав');
        }

        $model = new Organization([
            'status'                 => Organization::STATUS_NEW,
            'scenario'               => Organization::SCENARIO_GUEST,
            'actual'                 => 0,
            'cratedate'              => date("Y-m-d"),
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
                    $model->link('operators', Yii::$app->operator->identity);
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

    protected function findModelByToken($token)
    {
        if (($model = Organization::findOne(['anonymous_update_token' => $token])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
     *
     * @param integer $id
     *
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

        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
            && $model->status === Organization::STATUS_NEW
            && $model->refuse_reason) {
            Yii::$app->session->setFlash('info', 'Причина предыдущего отказа: ' . $model->refuse_reason);
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
            } else {
                unset($user->password);
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
            if ($model->isRefused) {
                Yii::$app->session->setFlash('success', 'Организации направлен отказ во включении в реестр поставщиков образовательных услуг');

                return $this->redirect('/personal/operator-organizations');
            }
            if ($showUserInfo === true) {
                return $this->render('/user/view', [
                    'model'    => $user,
                    'password' => $password,
                ]);
            }


            return $this->redirect(['/organization/view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'user'  => $user,
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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

        if ($model->load(Yii::$app->request->post()) && $model->save(true, ['max_child'])) {
            return $this->redirect(['/organization/view', 'id' => $model->id]);
        }

        return $this->render('newlimit', [
            'model' => $model,
        ]);

    }

    public function actionAlllimit()
    {
        $org = Organization::find()
            ->select(['`organization`.id'])
            ->joinWith('municipality')
            ->andWhere('mun.operator_id = ' . Yii::$app->operator->identity->id)
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

            $coef_raiting = 0;
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

            if (!$model->save(true, ['max_child'])) {
                Yii::$app->session->setFlash('danger', 'При сохранении лимитов у некоторых организаций произошла ошибка.');
            }
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

        if ($count2 != 0) {
            $model->raiting = round($count / $count2, 2);
        } else {
            $model->raiting = null;
        }


        if ($model->save(true, ['raiting'])) {
            return $this->redirect(['/organization/view', 'id' => $model->id]);
        }
    }

    public function actionAllraiting()
    {
        $org = Organization::find()
            ->select(['`organization`.id'])
            ->joinWith('municipality')
            ->andWhere('mun.operator_id = ' . Yii::$app->operator->identity->id)
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

            if (!$model->save(true, ['raiting'])) {
                Yii::$app->session->setFlash('danger', 'При сохранении рейтинга у некоторых организаций произошла ошибка.');
            }
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
                    if ($model->period === Contracts::CURRENT_REALIZATION_PERIOD) {
                        $cert->balance += $contract->rezerv;
                        $cert->rezerv -= $contract->rezerv;
                    } elseif ($model->period === Contracts::FUTURE_REALIZATION_PERIOD) {
                        $cert->balance_f += $contract->rezerv;
                        $cert->rezerv_f -= $contract->rezerv;
                    } elseif ($model->period === Contracts::PAST_REALIZATION_PERIOD) {
                        $cert->balance_p += $contract->rezerv;
                        $cert->rezerv_p -= $contract->rezerv;
                    }
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
            'user'  => $user,
            'title' => 'Приостановить деятельность организации',
        ]);
    }

    /**
     * Deletes an existing Organization model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
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
            'user'  => $user,
            'title' => null
        ]);
    }
}
