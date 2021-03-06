<?php

namespace app\controllers;

use app\models\Cooperate;
use app\models\forms\ConfirmRequestForm;
use app\models\forms\RejectContractForm;
use app\models\OperatorSettings;
use app\models\Organization;
use app\models\Payers;
use app\models\User;
use app\models\UserIdentity;
use app\traits\AjaxValidationTrait;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CooperateController implements the CRUD actions for Cooperate model.
 */
class CooperateController extends Controller
{
    use AjaxValidationTrait;

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'reject-contract' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRejectContract($id): Response
    {
        $model = new RejectContractForm($id);
        if ($model->reject()) {
            Yii::$app->session->setFlash('success', 'Вы успешно расторгли соглашение.');
        } else {
            Yii::$app->session->setFlash('error', 'Вы не можете расторгнуть соглашение.');
        }

        return $this->goBack();
    }

    /**
     * Подтверждение заявки на заключение договора плательщиком.
     *
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionConfirmRequest($id)
    {
        $model = $this->findCurrentModel(
            $id,
            Yii::$app->user->getIdentity()->payer->id,
            null,
            Cooperate::STATUS_NEW
        );
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }
        $form = new ConfirmRequestForm();
        $this->performAjaxValidation($form);
        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post())) {
            $form->setModel($model);
            if ($form->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно подтвердили заявку.');
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при подтверждении заявки.');
            }

            return $this->redirect(['organization/view', 'id' => $model->organization_id]);
        }

        return $this->goBack();
    }

    /**
     * Отправка заявки на заключение договора с плательщиком организации.
     *
     * @param $payerId
     * @param $period
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionRequest($payerId, $period)
    {
        /** @var OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        if (null !== $this->findCurrentModel(null, $payerId, Yii::$app->user->getIdentity()->organization->id, null, $period)) {
            throw new NotFoundHttpException('Model already exist!');
        }

        if (Cooperate::PERIOD_FUTURE == $period && !$operatorSettings->payerCanCreateFuturePeriodCooperate()) {
            \Yii::$app->session->setFlash('error', 'Вы не можете подать заявку на будущий период.');

            return $this->redirect(['payers/view', 'id' => $payerId]);
        }

        $model = new Cooperate([
            'payer_id' => $payerId,
            'organization_id' => Yii::$app->user->getIdentity()->organization->id,
            'period' => $period,
        ]);

        $model->create();
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подали заявку на подключение.');

            return $this->redirect(['personal/organization-payers']);
        }
        Yii::$app->session->setFlash('error', 'Возникла ошибка при подаче заявки на подключение.');

        return $this->redirect(['payers/view', 'id' => $payerId]);
    }

    /**
     * Отклонение заявки на заключение договора плательщиком с указанием причины.
     *
     * @param integer $id
     * @return \yii\web\Response|string
     * @throws NotFoundHttpException
     */
    public function actionRejectRequest($id)
    {
        $model = $this->findCurrentModel(
            $id,
            Yii::$app->user->getIdentity()->payer->id,
            null,
            Cooperate::STATUS_NEW
        );
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }
        $model->scenario = Cooperate::SCENARIO_REJECT;
        if ($model->load(Yii::$app->request->post())) {
            $model->reject();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно отклонили заявку.');

                return $this->redirect(['personal/payer-organizations']);
            }
            Yii::$app->session->setFlash('error', 'Возникла ошибка при отклонении заявки.');
        }

        return $this->goBack();
    }

    /**
     * Обжалование заявки на заключение договора организацией в случае отказа.
     *
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAppealRequest($id)
    {
        $model = $this->findCurrentModel(
            $id,
            null,
            Yii::$app->user->getIdentity()->organization->id,
            Cooperate::STATUS_REJECTED
        );
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }

        $model->scenario = Cooperate::SCENARIO_APPEAL;
        if ($model->load(Yii::$app->request->post())) {
            $model->appeal();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно подали жалобу.');

                return $this->redirect(['personal/organization-payers']);
            }
            Yii::$app->session->setFlash('error', 'Возникла ошибка при подаче жалобы.');
        }

        return $this->goBack();
    }

    public function actionPaymentLimit($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Cooperate::SCENARIO_LIMIT;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно изменили максимальную сумму.');

                return $this->redirect(['organization/view', 'id' => $model->organization_id]);
            }
            Yii::$app->session->setFlash('error', 'Возникла ошибка при заполнении реквизитов.');
        }

        return $this->goBack();
    }

    /**
     * Ввод реквизитов органзацией
     *
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRequisites($id)
    {
        $model = $this->findCurrentModel(
            $id,
            null,
            Yii::$app->user->getIdentity()->organization->id,
            [
                Cooperate::STATUS_CONFIRMED,
                Cooperate::STATUS_ACTIVE
            ]
        );
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }
        $model->scenario = Cooperate::SCENARIO_REQUISITES;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно заполнили реквизиты.');

                if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
                    return $this->redirect(['personal/organization-payers']);
                } else if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
                    return $this->redirect(['organization/view', 'id' => $model->organization_id]);
                }
            }
            Yii::$app->session->setFlash('error', 'Возникла ошибка при заполнении реквизитов.');
        }

        return $this->goBack();
    }

    /**
     * Отклонение заявки на заключение договора с жалобой оператором.
     *
     * @param integer $id
     * @return \yii\web\Response
     */
    public function actionRejectAppeal($id)
    {
        $model = $this->findModel($id);
        $model->reject();
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Заявка отклонена.');
        } else {
            Yii::$app->session->setFlash('error', 'Возникла ошибка при отклонении заявки.');
        }

        return $this->redirect(['personal/operator-cooperates']);
    }

    /**
     * Рассмотрение и подтверждение обжалования оператором.
     *
     * @param integer $id
     * @return \yii\web\Response
     */
    public function actionConfirmAppeal($id)
    {
        $model = $this->findModel($id);
        $model->setNew();
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Заявка отправлена в ожидающие подтверждения.');
        } else {
            Yii::$app->session->setFlash('error', 'Возникла ошибка при изменении статуса заявки заявки.');
        }

        return $this->redirect(['personal/operator-cooperates']);
    }

    /**
     * Финальное подтверждение заявки плательщиком.
     *
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionConfirmContract($id)
    {
        $model = $this->findCurrentModel(
            $id,
            Yii::$app->user->getIdentity()->payer->id,
            null,
            Cooperate::STATUS_CONFIRMED
        );
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }
        $model->activate();
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Заявка успешно подтверждена.');
        } else {
            Yii::$app->session->setFlash('error', 'Возникла ошибка при подтверждении заявки.');
        }

        return $this->redirect(['personal/payer-organizations']);
    }

    /**
     * @param null|integer $id
     * @param null|integer $payerId
     * @param null|integer $organizationId
     * @param null|integer $status
     * @param null/integer $period
     *
     * @return Cooperate
     */
    protected function findCurrentModel($id = null, $payerId = null, $organizationId = null, $status = null, $period = null)
    {
        if (null === $id && null === $payerId && null === $organizationId && null === $status) {
            throw new \DomainException('Something wrong');
        }
        $query = Cooperate::find();
        $query = null !== $id ? $query->andWhere(['id' => $id]) : $query;
        $query = null !== $payerId ? $query->andWhere(['payer_id' => $payerId]) : $query;
        $query = null !== $organizationId ? $query->andWhere(['organization_id' => $organizationId]) : $query;
        $query = null !== $status ? $query->andWhere(['status' => $status]) : $query;
        $query = null !== $period ? $query->andWhere(['period' => $period]) : $query;

        return $query->one();
    }

    /**
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
            ->andWhere(['<', 'status', 2])
            ->one();

        return $this->render('view', [
            'model' => $this->findModel($cooperate['id']),
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/personal/organization-payers']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {
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
            } else {
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
        if ($user->load(Yii::$app->request->post())) {
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
                $model->status = Cooperate::STATUS_REJECTED;
                $model->date_dissolution = date("Y-m-d");

                if ($model->save()) {
                    return $this->redirect('/personal/organization-payers');
                }
            } else {
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
}
