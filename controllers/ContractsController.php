<?php

namespace app\controllers;

use app\helpers\FormattingHelper;
use app\models\Certificates;
use app\models\Completeness;
use app\models\Contracts;
use app\models\contracts\ContractRequest;
use app\models\contracts\GroupSwitcher;
use app\models\ContractsDecInvoiceSearch;
use app\models\ContractsInvoiceSearch;
use app\models\ContractspreInvoiceSearch;
use app\models\Cooperate;
use app\models\forms\CertificateVerificationForm;
use app\models\forms\ContractConfirmForm;
use app\models\forms\ContractRequestForm;
use app\models\forms\SelectGroupForm;
use app\models\Groups;
use app\models\GroupsSearch;
use app\models\Informs;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use app\models\User;
use app\models\UserIdentity;
use app\traits\AjaxValidationTrait;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use kartik\mpdf\Pdf;
use mPDF;
use Yii;
use yii\base\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

/**
 * ContractsController implements the CRUD actions for Contracts model.
 */
class ContractsController extends Controller
{
    use AjaxValidationTrait;

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
     * @return string|Response
     */
    public function actionCreate()
    {
        $validateForm = new CertificateVerificationForm();
        if ($validateForm->load(Yii::$app->request->post()) && $validateForm->validate()) {
            $selectForm = new SelectGroupForm();
            $selectForm->setCertificate($validateForm->getCertificate());
            if ($selectForm->load(Yii::$app->request->post()) && $selectForm->validate()) {
                return $this->redirect([
                    'request',
                    'groupId' => $selectForm->groupId,
                    'certificateId' => $selectForm->getCertificate()->id
                ]);
            }
        }

        return $this->render('create', [
            'validateForm' => $validateForm,
            'selectForm' => $selectForm ?? null,
        ]);
    }

    public function actionSelectModule()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents) {
                $programId = $parents[0];
                /** @var ProgrammeModule[] $rows */
                $rows = ProgrammeModule::find()
                    ->andWhere(['program_id' => $programId, 'open' => 1])
                    ->all();

                foreach ($rows as $value) {
                    $out[] = ['id' => $value->id, 'name' => $value->getFullname()];
                }

                echo Json::encode(['output' => $out, 'selected' => '']);

                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionSelectGroup()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            list($programId, $moduleId) = $_POST['depdrop_parents'];
            if ($programId && $moduleId) {
                $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('groups')
                    ->where(['program_id' => $programId])
                    ->andWhere(['year_id' => $moduleId])
                    ->all();

                $maxchild = (new \yii\db\Query())
                    ->select(['maxchild'])
                    ->from('years')
                    ->where(['id' => $moduleId])
                    ->one();

                foreach ($rows as $value) {

                    $contract = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status' => [0, 1, 3]])
                        ->andWhere(['group_id' => $value['id']])
                        ->count();

                    $sum = $maxchild['maxchild'] - $contract;

                    if ($sum > 0) {
                        $out[] = ['id' => $value['id'], 'name' => $value['name']];
                    }
                }

                echo Json::encode(['output' => $out, 'selected' => '']);

                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * проверить может ли контракт сертификата быть автопролонгирован в модуле
     */
    public function actionContractCanAutoProlongInModule()
    {
        $certificate = Certificates::findOne(\Yii::$app->request->post('certificateId', null));

        if (!$certificate) {
            return $this->asJson(false);
        }

        if (!$certificate->contractCanAutoProlongInModule(\Yii::$app->request->post('programId'), \Yii::$app->request->post('moduleId'))) {
            return $this->asJson(false);
        }

        return $this->asJson(true);
    }

    /**
     * @param string $groupId
     * @param null   $certificateId
     *
     * @return string|\yii\web\Response
     */
    public function actionRequest($groupId, $certificateId = null)
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            $certificateId = Yii::$app->user->getIdentity()->certificate->id;
        }

        $contract = Contracts::findOne([
            'group_id' => $groupId,
            'certificate_id' => $certificateId,
            'status' => null,
            ]);

        $group = Groups::findOne(['id' => $groupId]);
        if ($group && !$group->freePlaces) {
            Yii::$app->session->setFlash('modal-danger', 'К сожалению заявка на обучение по программе не будет отправлена, пока Вы ее составляли кто-то опередил Вас и подал заявку раньше, тем самым заняв последнее место в группе. Пожалуйста, посмотрите еще варианты зачисления на обучение (например, места могут оказаться в других группах)');

            return $this->redirect('/personal/certificate-programs');

        }
        if ($group && !$group->organization->existsFreePlace()) {
            Yii::$app->session->setFlash('modal-danger', 'К сожалению заявка на обучение по программе не будет отправлена, пока Вы ее составляли кто-то опередил Вас и подал заявку раньше, тем самым заняв последнее место в организации. Пожалуйста, посмотрите еще варианты зачисления на обучение.');

            return $this->redirect('/personal/certificate-programs');
        }

        $contractRequestFormValid = false;
        $model = new ContractRequestForm($groupId, $certificateId, $contract);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $contractRequestFormValid = true;
            $contract = $model->save();
            if (!$contract) {
                Yii::$app->session->setFlash('danger', 'Что-то не так.');
                return $this->refresh();
            }
        }
        $confirmForm = null;
        if (null !== $contract && $contract->status == null) {
            $confirmForm = new ContractConfirmForm($contract, $certificateId);
            if ($confirmForm->load(Yii::$app->request->post()) && $confirmForm->validate()) {
                if ($confirmForm->save()) {
                    Yii::$app->session->setFlash('success', 'Вы успешно подали заявку на обучение.');

                    return $this->redirect([
                        Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION) ? 'verificate' : 'view',
                        'id' => $contract->id
                    ]);
                } else {
                    Yii::$app->session->setFlash('danger', 'Что-то не так.');

                    return $this->refresh();
                }
            }
        }

        $cooperateWithCorrespondingPeriodExists = null;
        if ($contract) {
            $cooperateWithCorrespondingPeriodExists = $contract->payer->getCooperates()->where([
                'cooperate.organization_id' => $contract->organization_id,
                'cooperate.status' => Cooperate::STATUS_ACTIVE,
                'cooperate.period' => Cooperate::getPeriodFromDate($model->dateFrom)
            ])->exists();
        }

        return $this->render('request', [
            'model' => $model,
            'contract' => $contract ?: null,
            'confirmForm' => $confirmForm ?: null,
            'contractRequestFormValid' => $contractRequestFormValid,
            'groupId' => $groupId,
            'certificateId' => $certificateId,
            'cooperateWithCorrespondingPeriodExists' => $cooperateWithCorrespondingPeriodExists,
        ]);
    }

    /**
     * @param $groupId
     * @param null $certificateId
     * @return array|null
     */
    public function actionValidateRequest($groupId, $certificateId = null)
    {
        $contract = Contracts::findOne([
            'group_id' => $groupId,
            'certificate_id' => $certificateId,
            'status' => null,
        ]);
        $model = new ContractRequestForm($groupId, $certificateId, $contract);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        return null;
    }

    /**
     * @param             $id
     * @param null|string $certificateId
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionRejectRequest($id, $certificateId = null): Response
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            $certificateId = Yii::$app->user->getIdentity()->certificate->id;
        }
        $contract = Contracts::findOne(['id' => $id, 'certificate_id' => $certificateId]);
        if (null === $contract) {
            throw new NotFoundHttpException('Model not found');
        }
        if (null !== $contract->status) {
            throw new \DomainException('Контракт уже заключён и вы не можете его отменить!');
        }
        $contract->delete();

        return $this->redirect(['programs/search']);
    }

    /**
     * Displays a single Contracts model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewContract', ['id' => $id])) {
            throw new ForbiddenHttpException('Нет прав на просмотр договора.');
        }
        $model = $this->findModel($id);
        $completenessQuery = $model->getTransactions();

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        if (!$model->canBeAccepted()) {
            $message = null;

            if (\Yii::$app->user->can('certificate') || \Yii::$app->user->can('operators')) {
                $message = 'Поставщик услуг пока не может выставить оферту по данному договору - нет реквизитов о договоре между ним и уполномоченной организацией';
            }
            if (\Yii::$app->user->can('payers')) {
                $message = 'Поставщик услуг не сможет выставить оферту по данному договору прежде чем Вы заключите с ним ' . Cooperate::documentNames()[$operatorSettings->document_name] . ' на соответствующий период';
            }

            \Yii::$app->session->addFlash('danger', $message);
        }

        return $this->render('view', [
            'model' => $model,
            'completenessQuery' => $completenessQuery
        ]);
    }

    /**
     * Finds the Contracts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Contracts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contracts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGroup($id)
    {
        $searchGroups = new GroupsSearch();
        $searchGroups->year_id = $id;
        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        $rows = (new \yii\db\Query())
            ->select(['program_id'])
            ->from('years')
            ->where(['id' => $id])
            ->one();

        $program = Programs::findOne($rows['program_id']);
        $year = ProgrammeModule::findOne($id);

        return $this->render('/contracts/groups', [
            'GroupsProvider' => $GroupsProvider,
            'program' => $program,
            'year' => $year,
        ]);
    }

    public function actionNewgroup($id)
    {
        $switcher = new GroupSwitcher($id);
        $groupsList = ArrayHelper::map($switcher->contractGroups, 'id', 'name');

        if (Yii::$app->request->isAjax && $switcher->load(Yii::$app->request->post())) {

            return $this->asJson(ActiveForm::validate($switcher));
        }

        if ($switcher->load(Yii::$app->request->post()) && $switcher->save()) {

            return $this->redirect(['/groups/contracts', 'id' => $switcher->group_id]);

        }

        return $this->render('newgroup', [
            'model' => $switcher,
            'groupsList' => $groupsList,
        ]);
    }

    public function actionCancel($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/programs/index']);
    }

    public function actionVerificate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Contracts::SCENARIO_CREATE_DATE;
        $cert = $model->certificate;
        $group = $model->group;
        $program = $model->program;

        $org = $model->organization;

        $parentContractExists = $model->parentExists();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($parentContractExists && $model->date != $model->oldAttributes['date']) {
                $model->date = $model->oldAttributes['date'];
            }

            if (empty($model->date)) {
                Yii::$app->session->setFlash('error', 'Введите дату договора.');

                return $this->render('/contracts/save', [
                    'model' => $model,
                ]);
            }

            $contracts = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['status' => 1])
                ->andWhere(['organization_id' => $model->organization_id])
                ->column();

            foreach ($contracts as $contract) {
                $cont = $this->findModel($contract);

                $date = explode("-", $model->date);
                $cdate = explode("-", $cont->date);

                if ($date[0] == $cdate[0]) {
                    if ($model->number == $cont->number) {
                        Yii::$app->session->setFlash('error', 'Договор с таким номером уже существует!');

                        return $this->render('/contracts/save', [
                            'model' => $model,
                            //'display' => 'Договор с таким номером уже существует!';
                        ]);
                    }
                }
            }



            $org->amount_child = $org->amount_child + 1;

            $org->save();

            $program->last_contracts = $program->last_contracts + 1;
            $program->save();

            $currentMonth = strtotime('first day of this month');
            $nextMonth = strtotime('first day of next month');
            $lastDayOfMonth = strtotime('last day of this month');

            if ($model->period == Contracts::CURRENT_REALIZATION_PERIOD) {
                $cert->updateCounters([
                    'rezerv' => $model->payer_first_month_payment * -1,
                ]);

                if ($model->start_edu_contract < date('Y-m-d', $currentMonth) && $model->prodolj_m_user > 1) {
                    $cert->updateCounters([
                        'rezerv' => $model->payer_other_month_payment * -1,
                    ]);
                }
            } elseif ($model->period == Contracts::FUTURE_REALIZATION_PERIOD) {
                $cert->updateCounters([
                    'rezerv_f' => $model->payer_first_month_payment * -1,
                ]);
            }

            $model->paid = $model->payer_first_month_payment;
            $model->rezerv = $model->rezerv - ($model->payer_first_month_payment);
            if ($model->start_edu_contract < date('Y-m-d', $currentMonth) && $model->prodolj_m_user > 1) {
                $model->paid += $model->payer_other_month_payment;
                $model->rezerv -= $model->payer_other_month_payment;
            }

            $model->status = 1;
            if ($model->stop_edu_contract <= date('Y-m-d', $lastDayOfMonth)) {
                $model->wait_termnate = 1;
                $model->termination_initiated_at = date('Y-m-d H:i:s');
            }

            $firstDayOfPreviousMonth = strtotime('first day of previous month');

            if ($model->save()) {
                if (date('m') != 1 && $model->stop_edu_contract >= date('Y-m-d', $firstDayOfPreviousMonth) && $model->start_edu_contract < date('Y-m-d', $currentMonth)) {
                    $completeness = new Completeness();
                    $completeness->group_id = $model->group_id;
                    $completeness->contract_id = $model->id;

                    $start_edu_contract = explode("-", $model->start_edu_contract);

                    $completeness->month = date('m') - 1;
                    $completeness->year = $start_edu_contract[0];

                    $completeness->preinvoice = 0;
                    $completeness->completeness = 100;

                    $month = $start_edu_contract[1];

                    if ($month == date('m') - 1) {
                        $price = $model->payer_first_month_payment;
                    } else {
                        $price = $model->payer_other_month_payment;
                    }

                    $completeness->sum = round(($price * $completeness->completeness) / 100, 2);
                    $completeness->save();
                }

                if (date('m') == 12 && $model->stop_edu_contract >= date('Y-m-d', $currentMonth) && $model->start_edu_contract <= date('Y-m-d', $lastDayOfMonth) ) {
                    $completeness = new Completeness();
                    $completeness->group_id = $model->group_id;
                    $completeness->contract_id = $model->id;

                    $start_edu_contract = explode("-", $model->start_edu_contract);

                    $completeness->month = date('m');
                    $completeness->year = date('Y');

                    $completeness->preinvoice = 0;
                    $completeness->completeness = 100;

                    $month = $start_edu_contract[1];

                    if ($month == date('m')) {
                        $price = $model->payer_first_month_payment;
                    } else {
                        $price = $model->payer_other_month_payment;
                    }

                    $completeness->sum = round(($price * $completeness->completeness) / 100, 2);
                    $completeness->save();
                }

                if ($model->start_edu_contract < date('Y-m-d', $nextMonth) && $model->stop_edu_contract >= date('Y-m-d', $currentMonth)) {
                    $preinvoice = new Completeness();
                    $preinvoice->group_id = $model->group_id;
                    $preinvoice->contract_id = $model->id;
                    $preinvoice->month = date('m');
                    $preinvoice->year = $start_edu_contract[0];
                    $preinvoice->preinvoice = 1;
                    $preinvoice->completeness = 80;

                    $start_edu_contract = explode("-", $model->start_edu_contract);
                    $month = $start_edu_contract[1];

                    if ($month == date('m')) {
                        $price = $model->payer_first_month_payment;
                    } else {
                        $price = $model->payer_other_month_payment;
                    }

                    $preinvoice->sum = round(($price * $preinvoice->completeness) / 100, 2);
                    $preinvoice->save();
                }

                $informs = new Informs();
                $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $cert->payer_id;
                $informs->text = 'Заключен договор';
                $informs->from = 2;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {
                    $inform = new Informs();
                    $inform->program_id = $model->program_id;
                    $inform->contract_id = $model->id;
                    $inform->prof_id = $model->certificate_id;
                    $inform->text = 'Заключен договор';
                    $inform->from = 4;
                    $inform->date = date("Y-m-d");
                    $inform->read = 0;

                    if ($inform->save()) {
                        return $this->redirect('/personal/organization-contracts');
                    }
                }
            }
        }

        return $this->render('verificate', [
            'model' => $model,
            'cert' => $cert,
            'group' => $group,
            'program' => $program,
            'parentContractExists' => $parentContractExists,
        ]);

    }

    public function actionApplicationPdf($id)
    {
        $model = $this->findModel($id);

        $content = $this->renderPartial('application-pdf', [
            'model' => $model,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'options' => ['title' => 'Заявление о приеме на обучение'],
            'methods' => [
                'SetHeader' => ['Заявление о приеме на обучение'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionApplicationClosePdf($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can($model->terminatorUserRole)) {
            throw new ForbiddenHttpException('Действие запрещено.');
        }

        $content = $this->renderPartial(Yii::$app->user->can('certificate') ? 'application-close-certificate-pdf' : 'application-close-organization-pdf', [
            'model' => $model,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'options' => ['title' => 'Уведомление о расторжении договора'],
            'methods' => [
                'SetHeader' => ['Уведомление о расторжении договора'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionGenerate($id)
    {
        $model = $this->findModel($id);

        if (!$model->canBeAccepted()) {
            return $this->redirect(Url::to(['/contracts/verificate', 'id' => $id]));
        }

        $model->setCooperate();
        $model->save();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);

            return $this->refresh();
        }

        return $this->render('/contracts/generate', [
            'model' => $model
        ]);
    }

    public function actionNo($id)
    {
        $model = $this->findModel($id);
        if ($model->status === Contracts::STATUS_REFUSED) {
            throw new NotAcceptableHttpException('Уже отменена');
        }
        if ($model->refusedWithInformer()) {
            return $this->redirect('/personal/organization-contracts');
        }

        return $this->render('/informs/comment', [
            'informs' => new Informs(),
            'model' => $model
        ]);
    }

    public function actionTermrequest($id)
    {
        $model = $this->findModel($id);
        if ($model->status === Contracts::STATUS_REFUSED) {
            throw new NotAcceptableHttpException('Уже отменена');
        }
        if (!in_array($model->status, [Contracts::STATUS_REQUESTED, Contracts::STATUS_ACCEPTED])) {
            throw new NotAcceptableHttpException('Контракт не может быть расторгнут, поскольку уже переведен в "действующие договоры"');
        }

        if ($model->refusedWithInformer()) {
            return $this->redirect('/personal/certificate-archive#panel2');
        }

        return $this->render('/informs/comment', [
            'informs' => new Informs(),
            'model' => $model

        ]);
    }

    public function actionTerminate($id)
    {
        $model = $this->findModel($id);
        if ($model->terminateWithInformer()) {
            if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
                Yii::$app->session->setFlash('info', 'Пожалуйста, оцените программу.');
            }

            return $this->redirect(['contracts/view', 'id' => $model->id]);
        }
        $informs = new Informs();
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            return $this->render('/informs/comment', [
                'informs' => $informs,
                'model' => $model
            ]);
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->render('/informs/cause', [
                'informs' => $informs,
                'model' => $model,
            ]);
        }

        throw new NotFoundHttpException();
    }

    public function actionInvoice()
    {
        $payers = new Contracts();

        if ($payers->load(Yii::$app->request->post())) {
            $searchContracts = new ContractsInvoiceSearch();
            $searchContracts->payer_id = $payers->payer_id;
            $ContractsProvider = $searchContracts->search(Yii::$app->request->queryParams);

            return $this->render('invoice', [
                'payers' => $payers,
                'ContractsProvider' => $ContractsProvider,
            ]);
        }

        return $this->render('payer', [
            'payers' => $payers,
        ]);
    }

    public function actionDec()
    {
        $payers = new Contracts();

        if ($payers->load(Yii::$app->request->post())) {
            $searchContracts = new ContractsDecInvoiceSearch();
            $searchContracts->payer_id = $payers->payer_id;
            $ContractsProvider = $searchContracts->search(Yii::$app->request->queryParams);

            return $this->render('decinvoice', [
                'payers' => $payers,
                'ContractsProvider' => $ContractsProvider,
                'payer' => $payers
            ]);
        }

        return $this->render('decpayer', [
            'payers' => $payers,
        ]);
    }

    public function actionPreinvoice()
    {
        $payers = new Contracts();
        if ($payers->load(Yii::$app->request->post())) {
            $searchContracts = new ContractspreInvoiceSearch();
            $searchContracts->payer_id = $payers->payer_id;
            $ContractsProvider = $searchContracts->search(Yii::$app->request->queryParams);

            return $this->render('preinvoice', [
                'payers' => $payers,
                'ContractsProvider' => $ContractsProvider,
            ]);
        }

        return $this->render('prepayer', [
            'payers' => $payers,
        ]);
    }

    public function actionMpdf($id, $ok = null)
    {
        $model = $this->findModel($id);

        $contractRequest = new ContractRequest();
        $mpdf = $contractRequest->makePdfForContract($model);

        if ($ok) {
            $mpdf->Output(Yii::getAlias('@pfdoroot/uploads/contracts/') . $model->url, 'F');
            Yii::$app->session->setFlash('success', 'Оферта успешно отправлена заказчику.');

            return $this->redirect(['verificate', 'id' => $model->id]);
        }
        $mpdf->Output($model->url, 'D');
    }

    public function actionOk($id)
    {
        $model = $this->findModel($id);

        if (!$model->canBeAccepted()) {

            return $this->redirect(Url::to(['/contracts/generate', 'id' => $id]));
        }

        $model->status = Contracts::STATUS_ACCEPTED;
        $model->accepted_at = date('Y-m-d H:i:s');
        if ($model->save()) {
            return $this->redirect(['mpdf', 'id' => $id, 'ok' => true]);
        }
    }

    /**
     * Updates an existing Contracts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionOcenka($id)
    {
        $model = $this->findModel($id);

        if ($model->ocen_fact == null) {
            $model->ocen_fact = 0;
        }
        if ($model->ocen_kadr == null) {
            $model->ocen_kadr = 0;
        }
        if ($model->ocen_mat == null) {
            $model->ocen_mat = 0;
        }
        if ($model->ocen_obch == null) {
            $model->ocen_obch = 0;
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->ocenka = 1;

            if ($model->save()) {
                $ocen_fact_1 = 0;
                $ocen_kadr_1 = 0;
                $ocen_mat_1 = 0;
                $ocen_obch_1 = 0;

                $contracts1 = (new \yii\db\Query())
                    ->select(['id', 'ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch'])
                    ->from('contracts')
                    ->where(['ocenka' => 1])
                    ->andWhere(['status' => 1])
                    ->all();

                $count1 = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['ocenka' => 1])
                    ->andWhere(['status' => 1])
                    ->count();

                foreach ($contracts1 as $contract) {
                    $ocen_fact_1 += $contract['ocen_fact'];
                    $ocen_kadr_1 += $contract['ocen_kadr'];
                    $ocen_mat_1 += $contract['ocen_mat'];
                    $ocen_obch_1 += $contract['ocen_obch'];
                    // return var_dump($contract);
                }


                $contracts2 = (new \yii\db\Query())
                    ->select(['id', 'ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch'])
                    ->from('contracts')
                    ->where(['ocenka' => 1])
                    ->andWhere(['status' => 4])
                    ->all();

                $count2 = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['ocenka' => 1])
                    ->andWhere(['status' => 4])
                    ->count();

                $ocen_fact_2 = 0;
                $ocen_kadr_2 = 0;
                $ocen_mat_2 = 0;
                $ocen_obch_2 = 0;
                foreach ($contracts2 as $contract) {
                    $ocen_fact_2 += $contract['ocen_fact'];
                    $ocen_kadr_2 += $contract['ocen_kadr'];
                    $ocen_mat_2 += $contract['ocen_mat'];
                    $ocen_obch_2 += $contract['ocen_obch'];
                    // return var_dump($contract);
                }

                //return var_dump($ocen_mat_1);


                $program = Programs::findOne($model->program_id);

                $program->quality_control = $count1 + $count2;

                $program->ocen_fact = (($ocen_fact_1 + 2 * $ocen_fact_2) / ($count1 + (2 * $count2)));
                $program->ocen_kadr = (($ocen_kadr_1 + 2 * $ocen_kadr_2) / ($count1 + (2 * $count2)));
                $program->ocen_mat = (($ocen_mat_1 + 2 * $ocen_mat_2) / ($count1 + (2 * $count2)));
                $program->ocen_obch = (($ocen_obch_1 + 2 * $ocen_obch_2) / ($count1 + (2 * $count2)));

                $program->save() || Yii::$app->session->setFlash('error', 'Не удалось сохранить!!!');

                return $this->redirect(['view', 'id' => $model->id]);
            }

            /*
            'ocen_fact' => 'Оценка достижения заявленных результатов',
            'ocen_kadr' => 'Оценка выполнения кадровых требований',
            'ocen_mat' => 'Оценка выполнения требований к средствам обучения',
            'ocen_obch' => 'Оценка общей удовлетворенности программой',
            'ocenka' => 'Наличие оценки', */

        } else {
            return $this->render('ocenka', [
                'model' => $model,
            ]);
        }
    }

    public function actionPereschet()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {

                $contracts5 = (new \yii\db\Query())
                    ->select(['id', 'certificate_id'])
                    ->from('contracts')
                    ->where(['status' => 1])
                    ->all();

                foreach ($contracts5 as $contract5) {

                    $model = $this->findModel($contract5['id']);

                    $com_pre = (new \yii\db\Query())
                        ->select(['completeness', 'id'])
                        ->from('completeness')
                        ->where(['contract_id' => $model->id])
                        ->andWhere(['month' => date('m')])
                        ->andWhere(['preinvoice' => 1])
                        ->one();

                    $com = (new \yii\db\Query())
                        ->select(['completeness', 'id'])
                        ->from('completeness')
                        ->where(['contract_id' => $model->id])
                        ->andWhere(['month' => date('m') - 1])
                        ->andWhere(['preinvoice' => 0])
                        ->one();

                    if (empty($com) && empty($com_pre)) {

                        $completeness = new Completeness();
                        $completeness->group_id = $model->group_id;
                        $completeness->contract_id = $model->id;

                        $start_edu_contract = explode("-", $model->start_edu_contract);


                        if (date('m') == 12) {
                            $completeness->month = 12;
                            $completeness->year = $start_edu_contract[0];
                        } else {
                            $completeness->month = date('m') - 1;
                            $completeness->year = $start_edu_contract[0];
                        }

                        $completeness->preinvoice = 0;
                        $completeness->completeness = 100;

                        $month = $start_edu_contract[1];

                        if ($month == date('m') - 1) {
                            $price = $model->payer_first_month_payment;
                        } else {
                            $price = $model->payer_other_month_payment;
                        }

                        $completeness->sum = ($price * $completeness->completeness) / 100;

                        if (date('m') != 1) {
                            $completeness->save();
                        }

                        $preinvoice = new Completeness();
                        $preinvoice->group_id = $model->group_id;
                        $preinvoice->contract_id = $model->id;
                        $preinvoice->month = date('m');
                        $preinvoice->year = date('Y');
                        $preinvoice->preinvoice = 1;
                        $preinvoice->completeness = 80;

                        $start_edu_contract = explode("-", $model->start_edu_contract);
                        $month = $start_edu_contract[1];

                        if ($month == date('m')) {
                            $price = $model->payer_first_month_payment;
                        } else {
                            $price = $model->payer_other_month_payment;
                        }

                        $preinvoice->sum = ($price * $preinvoice->completeness) / 100;
                        $preinvoice->save();
                    }
                }

                $contracts = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['wait_termnate' => 1])
                    ->column();

                foreach ($contracts as $contract) {
                    $cont = $this->findModel($contract);

                    $certificates = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('certificates')
                        ->andWhere(['id' => $cont->certificate_id])
                        ->column();

                    foreach ($certificates as $certificate) {
                        $cert = Certificates::findOne($certificate);

                        $cert->balance = $cert->balance + $cont->rezerv;

                        $cert->save();
                    }

                    $program = Programs::findOne($cont->program_id);

                    //return var_dump($cont->terminator_user);

                    if ($cont->terminator_user == 1) {
                        $program->last_s_contracts_rod = $program->last_s_contracts_rod + 1;
                        $program->last_s_contracts = $program->last_s_contracts + 1;
                    }
                    if ($cont->terminator_user == 2) {
                        $program->last_s_contracts = $program->last_s_contracts + 1;
                    }
                    //$program->last_contracts = $program->last_contracts+1;
                    $org = Organization::findOne($cont->organization_id);
                    $org->amount_child = $org->amount_child - 1;
                    $org->save();

                    $certificate = Certificates::findOne($cont->certificate_id);
                    $certificate->rezerv = $certificate->rezerv - $cont->rezerv;

                    $certificate->save();
                    $program->save();

                    $cont->rezerv = 0;
                    $cont->status = 4;
                    $cont->wait_termnate = 0;
                    if (date("m") == 1) {
                        $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, 12, date('Y') - 1);
                        $cont->date_termnate = (date("Y") - 1) . '-12-' . $cal_days_in_month;
                    } else {
                        $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m') - 1, date('Y'));
                        $cont->date_termnate = date("Y") . '-' . (date('m') - 1) . '-' . $cal_days_in_month;
                    }
                    $cont->save();

                    // по этим договорам возвращать резерв на баланс + оставшиеся месяца * ежемесячный платеж
                    // дата окончания - дата удаления
                }

                $contracts3 = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['like', 'stop_edu_contract', date("Y-m-")])
                    ->column();


                foreach ($contracts3 as $contract) {
                    $cont = $this->findModel($contract);
                    $cont->wait_termnate = 1;
                    $cont->termination_initiated_at = date('Y-m-d H:i:s');
                    $cont->save();
                }
                //return var_dump($contracts3);

                $datestart = date("Y-m") . '-01';

                $contracts2 = (new \yii\db\Query())
                    ->select(['id', 'certificate_id'])
                    ->from('contracts')
                    ->where(['status' => 1])
                    ->andWhere(['<', 'start_edu_contract', $datestart])
                    ->all();


                foreach ($contracts2 as $contract2) {
                    //return var_dump ($contract2);
                    $model = $this->findModel($contract2['id']);


                    //return var_dump($model);

                    $certificates = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('certificates')
                        ->where(['>', 'rezerv', 0])
                        ->andWhere(['id' => $contract2['certificate_id']])
                        ->column();


                    foreach ($certificates as $certificate) {
                        $cert = Certificates::findOne($certificate);
                        //$cert->balance = $cert->balance - $cert->rezerv;
                        $model->rezerv = $model->rezerv - ($model->payer_other_month_payment);
                        $model->paid = $model->paid + ($model->payer_other_month_payment);
                        $cert->rezerv = $cert->rezerv - ($model->payer_other_month_payment);

                        $model->save();
                        $cert->save();
                    }

                }

                $contracts4 = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['status' => [1, 4]])
                    ->column();

                if (date('m') == 1) {
                    $twomonth = 11;
                }
                if (date('m') == 2) {
                    $twomonth = 12;
                }
                if (date('m') > 2) {
                    $twomonth = date('m') - 2;
                }

                foreach ($contracts4 as $contract4) {
                    $contract = $this->findModel($contract4);


                    $completeness = (new \yii\db\Query())
                        ->select(['completeness'])
                        ->from('completeness')
                        ->where(['month' => $twomonth])
                        ->andWhere(['preinvoice' => 0])
                        ->andWhere(['contract_id' => $contract->id])
                        ->one();


                    if ($completeness['completeness'] < 100 && isset($completeness['completeness'])) {

                        $certificate = Certificates::findOne($contract->certificate_id);

                        $start_edu_contract = explode('-', $contract->start_edu_contract);

                        if ($start_edu_contract[1] == $twomonth) {
                            $certificate->balance = $certificate->balance + (($contract->payer_first_month_payment) / 100) * (100 - $completeness['completeness']);
                        } else {
                            $certificate->balance = $certificate->balance + (($contract->payer_other_month_payment) / 100) * (100 - $completeness['completeness']);
                        }

                        $certificate->save();
                    }
                }


                return $this->redirect(['/personal/operator-contracts']);

            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/personal/operator-organizations']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
            'title' => 'Выполнить пересчет',
        ]);
    }

    /**
     * Deletes an existing Contracts model.
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

                $cont = $this->findModel($id);
                // return var_dump($id);

                /** @var $certificate Certificates */
                $certificate = Yii::$app->user->identity->certificate;


                $cert = Certificates::findOne($certificate->id);

                if ($cont->period == Contracts::CURRENT_REALIZATION_PERIOD) {
                    $cert->balance += $cont->rezerv;
                } elseif ($cont->period == Contracts::FUTURE_REALIZATION_PERIOD) {
                    $cert->balance_f += $cont->rezerv;
                } elseif ($cont->period == Contracts::PAST_REALIZATION_PERIOD) {
                    $cert->balance_p += $cont->rezerv;
                }

                $cert->save();

                $cont->delete();

                return $this->redirect(['/personal/certificate-programs']);
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/personal/operator-organizations']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
            'title' => null,
        ]);
    }

    public function actionUpdatescert()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $inputFile = "uploads/contracts-4.xlsx";

        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFile);


        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();


        for ($row = 1; $row <= $highestRow; $row++) {
            $rowDada = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);

            if ($row == 1) {
                continue;
            }
            if (empty($rowDada[0][0])) {
                break;
            }

            $certificates = Certificates::findOne($rowDada[0][0]);
            $certificates->soname = $rowDada[0][1];
            $certificates->name = $rowDada[0][2];
            $certificates->phname = $rowDada[0][3];
            $certificates->fio_child = $rowDada[0][1] . ' ' . $rowDada[0][2] . ' ' . $rowDada[0][3];
            $certificates->save();

            print_r($certificates->getErrors());

            $model = Contracts::findOne($rowDada[0][5]);
            $model->change_fiochild = $rowDada[0][4];
            $model->save();

            print_r($model->getErrors());


        }
        echo "OK!";

    }

    public function actionUpdatesparent()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $inputFile = "uploads/contracts-5.xlsx";

        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFile);


        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();


        for ($row = 1; $row <= $highestRow; $row++) {
            $rowDada = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);

            if ($row == 1) {
                continue;
            }

            if (empty($rowDada[0][0])) {
                break;
            }

            $certificates = Certificates::findOne($rowDada[0][0]);
            $certificates->fio_parent = $rowDada[0][1];
            $certificates->save();

            print_r($certificates->getErrors());

            $model = Contracts::findOne($rowDada[0][3]);
            $model->change_fioparent = $rowDada[0][2];
            $model->save();

            print_r($model->getErrors());


        }
        echo "OK!";

    }
}
