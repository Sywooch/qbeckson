<?php

namespace app\controllers;

use app\helpers\FormattingHelper;
use app\models\Cooperate;
use app\models\forms\CertificateVerificationForm;
use app\models\forms\ContractConfirmForm;
use app\models\forms\ContractRequestForm;
use app\models\forms\SelectGroupForm;
use app\models\UserIdentity;
use app\traits\AjaxValidationTrait;
use Yii;
use app\models\Contracts;
use app\models\User;
use app\models\ContractsSearch;
use app\models\ContractsoSearch;
use app\models\ContractsInvoiceSearch;
use app\models\ContractsDecInvoiceSearch;
use yii\base\Response;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Informs;
use app\models\Programs;
use app\models\Certificates;
use app\models\Organization;
use app\models\Favorites;
use app\models\ProgrammeModule;
use app\models\Groups;
use app\models\GroupsSearch;
use app\models\Payers;
use mPDF;
use kartik\mpdf\Pdf;
use yii\helpers\Json;
use app\models\ContractspreInvoiceSearch;
use app\models\Completeness;

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
     * @param string $groupId
     * @param null $certificateId
     * @return string|\yii\web\Response
     */
    public function actionRequest($groupId, $certificateId = null)
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            $certificateId = Yii::$app->user->getIdentity()->certificate->id;
        }

        $contract = Contracts::findOne(['group_id' => $groupId, 'certificate_id' => $certificateId]);

        if (null !== $contract && null !== $contract->status && $contract->status !== Contracts::STATUS_REFUSED) {
            throw new \DomainException('Контракт уже заключён!');
        }

        $model = new ContractRequestForm($groupId, $certificateId, $contract);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (false === ($contract = $model->save())) {
                Yii::$app->session->setFlash('danger', 'Что-то не так.');

                return $this->refresh();
            }
        }

        if (null !== $contract) {
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

        return $this->render('request', [
            'model' => $model,
            'contract' => $contract ?: null,
            'confirmForm' => $confirmForm ?: null,
        ]);
    }

    /**
     * @param $id
     * @param null|string $certificateId
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
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $preinvoice = Completeness::findPreinvoiceByContract($model->id, date('n'), date('Y'));
            $preinvoice->group_id = $model->group_id;
            $preinvoice->save(false, ['group_id']);

            return $this->redirect(['/groups/contracts', 'id' => $model->group_id]);
        }

        return $this->render('newgroup', [
            'model' => $model,
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
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

            $cert = Certificates::findOne($model->certificate_id);
            $program = Programs::findOne($model->program_id);

            $org = Organization::findOne($model->organization_id);

            $org->amount_child = $org->amount_child + 1;

            $org->save();

            $program->last_contracts = $program->last_contracts + 1;
            $program->save();

            if ($model->period == Contracts::CURRENT_REALIZATION_PERIOD) {
                $cert->updateCounters([
                    'rezerv' => $model->payer_first_month_payment * -1,
                ]);
            } elseif ($model->period == Contracts::FUTURE_REALIZATION_PERIOD) {
                $cert->updateCounters([
                    'rezerv_f' => $model->payer_first_month_payment * -1,
                ]);
            }

            $model->paid = $model->payer_first_month_payment;
            $model->rezerv = $model->rezerv - ($model->payer_first_month_payment);
            $model->status = 1;

            $previousMonth = strtotime('first day of previous month');
            $currentMonth = strtotime('first day of this month');
            $nextMonth = strtotime('first day of next month');

            if ($model->save()) {
                $completeness = new Completeness();
                $completeness->group_id = $model->group_id;
                $completeness->contract_id = $model->id;

                $start_edu_contract = explode("-", $model->start_edu_contract);

                if (date('m') == 12) {
                    $completeness->month = date('m');
                    $completeness->year = $start_edu_contract[0];
                } else {
                    $completeness->month = date('m') - 1;
                    $completeness->year = $start_edu_contract[0];
                }
                $completeness->preinvoice = 0;
                $completeness->completeness = 100;

                $month = $start_edu_contract[1];

                if (date('m') == 12) {
                    if ($month == 12) {
                        $price = $model->payer_first_month_payment;
                    } else {
                        $price = $model->payer_other_month_payment;
                    }
                } else {
                    if ($month == date('m') - 1) {
                        $price = $model->payer_first_month_payment;
                    } else {
                        $price = $model->payer_other_month_payment;
                    }
                }

                $completeness->sum = round(($price * $completeness->completeness) / 100, 2);

                if (date('m') != 1 && $model->start_edu_contract < date('Y-m-d', $currentMonth)) {
                    $completeness->save();
                }

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

                if ($model->start_edu_contract < date('Y-m-d', $nextMonth)) {
                    $preinvoice->save();

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
        }

        $cert = Certificates::findOne($model->certificate_id);
        $group = Groups::findOne($model->group_id);
        $program = Programs::findOne($model->program_id);

        return $this->render('verificate', [
            'model' => $model,
            'cert' => $cert,
            'group' => $group,
            'program' => $program,
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
            throw new ForbiddenHttpException('Действите запрещено.');
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
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setCooperate();
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
        $informs = new Informs();
        //TODO добавить транзакцию
        if ($informs->load(Yii::$app->request->post())) {
            $cert = Certificates::findOne($model->certificate_id);
            $cert->changeBalance($model);

            $model->rezerv = 0;
            $model->status = 2;

            if ($model->save()) {
                $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $model->organization_id;
                $informs->text = 'Отказано в записи. Причина: ' . $informs->dop;
                $informs->from = 3;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {
                    $inform = new Informs();
                    $inform->program_id = $model->program_id;
                    $inform->contract_id = $model->id;
                    $inform->prof_id = $model->certificate_id;
                    $inform->text = 'Отказано в записи. Причина: ' . $informs->dop;
                    $inform->from = 4;
                    $inform->date = date("Y-m-d");
                    $inform->read = 0;

                    if ($inform->save()) {
                        return $this->redirect('/personal/organization-contracts');
                    }
                }
            }
        }

        return $this->render('/informs/comment', [
            'informs' => $informs,
        ]);
    }

    public function actionTermrequest($id)
    {
        $model = $this->findModel($id);
        if (!in_array($model->status, [Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED])) {
            throw new NotAcceptableHttpException('Контракт не может быть расторгнут, поскольку уже переведен в "действующие договоры"');
        }

        $cert = Certificates::findOne($model->certificate_id);
        $cert->changeBalance($model);

        $model->rezerv = 0;
        $model->status = 2;
        if ($model->save()) {
            return $this->redirect('/personal/certificate-archive#panel2');
        }
    }

    public function actionTerminate($id)
    {
        $model = $this->findModel($id);
        $informs = new Informs();
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

        if ($informs->load(Yii::$app->request->post())) {
            if ($model->wait_termnate > 0) {
                throw new ForbiddenHttpException('Действие запрещено.');
            }

            if (isset($roles['certificate'])) {
                $model->terminator_user = 1;
            }
            if (isset($roles['organizations'])) {
                $model->terminator_user = 2;
            }

            $model->wait_termnate = 1;
            $model->date_initiate_termination = date('Y-m-d');
            $model->status_comment = $informs->dop;

            $cert = Certificates::findOne($model->certificate_id);
            $cert->changeBalance($model);

            $model->rezerv = 0;
            if ($model->save()) {
                if (isset($roles['certificate'])) {
                    Yii::$app->session->setFlash('info', 'Пожалуйста, оцените программу.');
                }

                return $this->redirect(['contracts/view', 'id' => $model->id]);
            }
        }

        if (isset($roles['certificate'])) {
            return $this->render('/informs/comment', [
                'informs' => $informs,
            ]);
        }
        if (isset($roles['organizations'])) {
            return $this->render('/informs/cause', [
                'informs' => $informs,
                'model' => $model,
            ]);
        }
    }

    public function actionInvoice()
    {
        $payers = new Contracts();

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        if ($payers->load(Yii::$app->request->post())) {
            $searchContracts = new ContractsInvoiceSearch();
            $searchContracts->payer_id = $payers->payer_id;
            $ContractsProvider = $searchContracts->search(Yii::$app->request->queryParams);

            return $this->render('invoice', [
                'payers' => $payers,
                'searchContracts' => $searchContracts,
                'ContractsProvider' => $ContractsProvider,
            ]);
        }

        return $this->render('payer', [
            'payers' => $payers,
            'organization' => $organization,
        ]);
    }

    public function actionDec()
    {
        $payers = new Contracts();

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        if ($payers->load(Yii::$app->request->post())) {

            $searchContracts = new ContractsDecInvoiceSearch();
            $searchContracts->payer_id = $payers->payer_id;
            $ContractsProvider = $searchContracts->search(Yii::$app->request->queryParams);

            // return '<pre>'.var_dump($contracts).'</pre>';
            return $this->render('decinvoice', [
                'payers' => $payers,
                'searchContracts' => $searchContracts,
                'ContractsProvider' => $ContractsProvider,
            ]);
        }

        return $this->render('decpayer', [
            'payers' => $payers,
            'organization' => $organization,
        ]);
    }

    public function actionPreinvoice()
    {
        $payers = new Contracts();

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        if ($payers->load(Yii::$app->request->post())) {

            $searchContracts = new ContractspreInvoiceSearch();
            $searchContracts->payer_id = $payers->payer_id;
            $ContractsProvider = $searchContracts->search(Yii::$app->request->queryParams);

            return $this->render('preinvoice', [
                'payers' => $payers,
                'searchContracts' => $searchContracts,
                'ContractsProvider' => $ContractsProvider,
            ]);
        }

        return $this->render('prepayer', [
            'payers' => $payers,
            'organization' => $organization,
        ]);
    }

    public function actionMpdf($id, $ok = null)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $model = $this->findModel($id);
        $organization = Organization::findOne($model->organization_id);
        $program = Programs::findOne($model->program_id);
        $group = Groups::findOne($model->group_id);
        $year = ProgrammeModule::findOne($model->year_id);
        $payer = Payers::findOne($model->payer_id);

        $date_elements_user = explode("-", $model->start_edu_contract);

        $cooperate = (new \yii\db\Query())
            ->select(['number', 'date'])
            ->from('cooperate')
            ->where(['organization_id' => $model->organization_id])
            ->andWhere(['payer_id' => $model->payer_id])
            ->andWhere(['status' => 1])
            ->one();
        $date_cooperate = explode("-", $cooperate['date']);

        if ($program->form == 1) {
            $programform = 'Очная';
        }
        if ($program->form == 2) {
            $programform = 'Очно-заочная';
        }
        if ($program->form == 3) {
            $programform = 'Заочная';
        }
        if ($program->form == 4) {
            $programform = 'Очная с применением дистанционных технологий и/или электронного обучения';
        }
        if ($program->form == 5) {
            $programform = 'Очно-заочная с применением дистанционных технологий и/или электронного обучения';
        }
        if ($program->form == 6) {
            $programform = 'Заочная с применением дистанционных технологий и/или электронного обучения';
        }

        $headerText = $organization->contractSettings->header_text;
        $headerText = str_replace(
            '№0000000000',
            '№' . $model->certificate->number . ' (обладатель сертификата - ' . $model->certificate->fio_child . ')',
            $headerText
        );
        $html = <<<EOD
<div style="font-size:12;" > 
<p style="text-align: center;">Договор об образовании №$model->number</p>
<br>
<div align="justify">$headerText о нижеследующем:</div>
</div>
EOD;

        if ($program->year > 1) {
            $chast = 'части';
        } else {
            $chast = '';
        }

        if ($program->year > 1) {
            $text5 = 'частью Программы';
        } else {
            $text5 = 'Программой';
        }

        if ($program->year >= 2) {
            $text144 = 'Полный срок реализации Программы - ' . $program->getCountMonths() . ' месяц(ев).';
        }


        if ($program->year == 1) {

            $month = (new \yii\db\Query())
                ->select(['month'])
                ->from('years')
                ->where(['id' => $model->year_id])
                ->one();

            if ($month['month'] == 1) {
                $text144 = 'Полный срок реализации Программы - ' . $month['month'] . ' месяц.';
            }

            if ($month['month'] >= 2 and $month['month'] <= 4) {
                $text144 = 'Полный срок реализации Программы - ' . $month['month'] . ' месяцa.';
            }

            if ($month['month'] >= 5) {
                $text144 = 'Полный срок реализации Программы - ' . $month['month'] . ' месяцев.';
            }
        }


        if ($model->sposob == 1) {
            $text77 = 'за наличный расчет';
        } else {
            $text77 = 'в безналичном порядке на счет Исполнителя, реквизиты которого указанны в разделе X настоящего Договора,';
        }


        if ($model->other_m_price == 0) {
            $text88 = floor($model->payer_first_month_payment) . ' руб. ' .
                round(($model->payer_first_month_payment - floor($model->payer_first_month_payment)) * 100, 0) . ' коп.';
            $text89 = floor($model->parents_first_month_payment) . ' руб. ' .
                round(($model->parents_first_month_payment - floor($model->parents_first_month_payment)) * 100, 0) . ' коп.';
        } else {
            $text88 = floor($model->payer_first_month_payment) . ' руб. ' .
                round(($model->payer_first_month_payment - floor($model->payer_first_month_payment)) * 100, 0) . ' коп. - за первый месяц периода обучения по Договору, ' .
                floor($model->payer_other_month_payment) . ' руб. ' .
                round(($model->payer_other_month_payment - floor($model->payer_other_month_payment)) * 100, 0) . ' коп. - за каждый последующий месяц периода обучения по Договору.';
            $text89 = floor($model->parents_first_month_payment) . ' руб. ' .
                round(($model->parents_first_month_payment - floor($model->parents_first_month_payment)) * 100, 0) . ' коп. - за первый месяц периода обучения по Договору, ' .
                floor($model->parents_other_month_payment) . ' руб. ' .
                round(($model->parents_other_month_payment - floor($model->parents_other_month_payment)) * 100, 0) . ' коп. - за каждый последующий месяц периода обучения по Договору.';
        }

        $directivity = FormattingHelper::directivityForm($program->directivity);

        if ($model->all_parents_funds > 0) {
            $text1 = ', а также оплатить часть образовательной услуги в объеме и на условиях, предусмотренных разделом V настоящего Договора ';

            $text3 = '4.2.1. Своевременно вносить плату за образовательную услугу в размере и порядке, определенных настоящим Договором, а также предоставлять платежные документы, подтверждающие такую оплату.<br>
             4.2.2. Создавать условия для получения Обучающимся образовательной услуги.<br>';

            $text4 = '5.1. Полная стоимость образовательной услуги за период обучения по Договору составляет ' . floor($model->all_funds) . ' руб. 
    ' . round(($model->all_funds - floor($model->all_funds)) * 100, 0) . ' коп., в том числе:<br>
                5.1.1. Будет оплачено за счет средств сертификата дополнительного образования Обучающегося - ' . floor($model->funds_cert) . ' руб. ' . round(($model->funds_cert - floor($model->funds_cert)) * 100, 0) . ' коп.<br>
                5.1.2. Будет оплачено за счет средств Заказчика - ' . floor($model->all_parents_funds) . ' руб. ' . round(($model->all_parents_funds - floor($model->all_parents_funds)) * 100, 0) . ' коп.<br />
            5.2. Оплата за счет средств сертификата осуществляется в рамках договора ' . (Yii::$app->operator->identity->settings->document_name === Cooperate::DOCUMENT_NAME_FIRST ? 'о возмещении затрат' : 'об оплате дополнительного образования') . ' № ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . ', заключенного между Исполнителем и ' . $payer->name_dat . ' (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: ' . $text88 . '<br>
            5.3. Заказчик осуществляет оплату ежемесячно ' . $text77 . ' не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: ' . $text89 . '<br>';

            if ($model->payment_order === 1) {
                $text4 .= '5.4. Оплата за счет средств сертификата и Заказчика за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
                $text4 .= '5.5. В случае отмены со стороны Исполнителя проведения одного или нескольких занятий в рамках оказания образовательной услуги объем оплаты по договору за месяц, в котором указанные занятия должны были быть проведены, уменьшается пропорционально доле таких занятий в общей продолжительности занятий в указанном месяце.<br>';
            } else {
                $text4 .= '5.4. Оплата за счет средств сертификата за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
                $text4 .= '5.5. Оплата за счет средств Заказчика за месяц периода обучения по Договору осуществляется пропорционально фактическому посещению  Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
                $text4 .= '5.6. В случае отмены со стороны Исполнителя проведения одного или нескольких занятий в рамках оказания образовательной услуги объем оплаты по договору за месяц, в котором указанные занятия должны были быть проведены, уменьшается пропорционально доле таких занятий в общей продолжительности занятий в указанном месяце.<br>';
            }
        } else {
            $text1 = '';
            $text3 = '4.2.1. Создавать условия для получения Обучающимся образовательной услуги.<br>';
            $text4 = '5.1. Полная стоимость образовательной услуги за период обучения по Договору составляет ' . floor($model->all_funds) . ' руб. ' . round(($model->all_funds - floor($model->all_funds)) * 100, 0) . ' коп.. Вся сумма будет оплачена за счет средств сертификата дополнительного образования Обучающегося.<br>         
            5.2. Оплата за счет средств сертификата осуществляется в рамках договора ' . (Yii::$app->operator->identity->settings->document_name === Cooperate::DOCUMENT_NAME_FIRST ? 'о возмещении затрат' : 'об оплате дополнительного образования') . ' № ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . ', заключенного между Исполнителем и ' . $payer->name_dat . ' (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: ' . $text88 . '<br>
            5.3. Оплата за счет средств сертификата за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
            $text4 .= '5.4. В случае отмены со стороны Исполнителя проведения одного или нескольких занятий в рамках оказания образовательной услуги объем оплаты по договору за месяц, в котором указанные занятия должны были быть проведены, уменьшается пропорционально доле таких занятий в общей продолжительности занятий в указанном месяце.<br>';
        }


        if ($year->kvdop == 0 and $year->hoursindivid == 0) {
            $text2 = '
                4.1.5.2. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                ' . $program->norm_providing . '<br>
                4.1.5.3. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.4. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.5. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($model->cert_dol != 0) {
                $text2 .= '3.1.5.6. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }


        if ($year->kvdop == 0 and $year->hoursindivid != 0) {
            $text2 = '
                4.1.5.2. Обеспечить индивидуальное консультирование обучающегося в рамках оказания образовательной услуги в объеме не менее ' . $year->hoursindivid . ' ак. час.<br>
                4.1.5.3. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                ' . $program->norm_providing . '<br>
                4.1.5.4. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.5. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.6. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($model->cert_dol != 0) {
                $text2 = $text2 . '4.1.5.7. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }


        if ($year->kvdop != 0 and $year->hoursindivid == 0) {
            $text2 = '
                4.1.5.2. Обеспечить одновременное сопровождение группы детей не менее чем двумя педагогическими работниками, за счет привлечения к оказанию услуги дополнительного(ых) педагогического(их) работника(ов), квалификация которого(ых) соответствует следующим условиям:<br>
                ' . $year->kvdop . '<br>
                4.1.5.3. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                ' . $program->norm_providing . '<br>
                4.1.5.4. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.5. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.6. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($model->cert_dol != 0) {
                $text2 = $text2 . '4.1.5.7. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }

        if ($year->kvdop != 0 and $year->hoursindivid != 0) {
            $text2 = '
                4.1.5.2. Обеспечить индивидуальное консультирование обучающегося в рамках оказания образовательной услуги в объеме не менее ' . $year->hoursindivid . ' ак. час.<br>
                4.1.5.3. Обеспечить одновременное сопровождение группы детей не менее чем двумя педагогическими работниками, за счет привлечения к оказанию услуги дополнительного(ых) педагогического(их) работника(ов), квалификация которого(ых) соответствует следующим условиям:<br>
                ' . $year->kvdop . '<br>
                4.1.5.4. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br> 
                «' . $program->norm_providing . '»<br>
                4.1.5.5. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.6. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.7. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($model->cert_dol != 0) {
                $text2 = $text2 . '4.1.5.8. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }

        $finishStudyDate = $model->period == Contracts::CURRENT_REALIZATION_PERIOD ? Yii::$app->operator->identity->settings->current_program_date_to : Yii::$app->operator->identity->settings->future_program_date_to;

        if ($finishStudyDate > $group->datestop) {
            $finishStudyDate = $group->datestop;
        }

        $text = '
        <div style="font-size: ' . $model->fontsize . '" >
        <p style="text-align:center">I. Общие положения и правовое основание Договора-оферты</p>
        
        <div align="justify">
            1.1. Настоящий договор является официальным предложением (офертой) Исполнителя Заказчику к заключению договора на оказание платной образовательной услуги, указанной в разделе II настоящего Договора, содержит все существенные условия договора на оказание платных образовательных услуг по образовательным программам дополнительного образования и публикуется в глобальной компьютерной сети Интернет на сайте: http://pfdo.ru (далее – Сайт). <br>
            1.2. Правовой основой регулирования отношений между Сторонами, возникших в силу заключения настоящего Договора, являются следующие нормативные документы: Гражданский кодекс Российской Федерации, Федеральный закон «Об образовании в Российской Федерации» от 29 декабря 2012 года №273-ФЗ, Правила оказания платных образовательных услуг, утвержденные постановлением Правительства РФ от 15 августа 2013 года №706.<br>
            1.3. В качестве необходимого и достаточного действия, определяющего безусловное принятие (акцепт) условий Договора со стороны Заказчика в соответствии со ст. 438 ГК РФ, определяется подписание Заказчиком заявления о зачислении Обучающегося на обучение по дополнительной образовательной программе, в рамках образовательной услуги, указанной в разделе II настоящего Договора.<br>
            1.4. Заявление о зачислении на Обучающегося на обучение по дополнительной образовательной программе, указанное в пункте 1.3 настоящего Договора, является неотъемлемой частью настоящего Договора и должно содержать указание на принятие Заказчиком условий настоящего Договора, а также следующие предусмотренные Правилами оказания платных образовательных услуг сведения:<br>
                а) фамилия, имя, отчество (при наличии) Заказчика, телефон заказчика;<br>
                б) место жительства Заказчика;<br>
                в) фамилия, имя, отчество (при наличии) Обучающегося, его место жительства, телефон.<br>
            1.5. Совершая действия по акцепту настоящего Договора Заказчик гарантирует, что он имеет законные права вступать в договорные отношения с Исполнителем. <br>
            1.6. Осуществляя акцепт настоящего Договора в порядке, определенном пунктом 1.3 Договора-оферты, Заказчик гарантирует, что ознакомлен, соглашается, полностью и безоговорочно принимает все условия настоящего Договора в том виде, в каком они изложены в тексте настоящего Договора. <br>
            1.7. Настоящий Договор может быть отозван Исполнителем до момента получения акцепта со стороны Заказчика.<br>
            1.8. Настоящий Договор не требует скрепления печатями и/или подписания Заказчиком и Исполнителем, сохраняя при этом полную юридическую силу.<br>
        </div>
        
        
        <p style="text-align:center">II. Предмет Договора</p>

<div align="justify">
	2.1. Исполнитель обязуется оказать Обучающемуся образовательную услугу по реализации ' . $chast . ' дополнительной общеобразовательной программы ' . $directivity . ' направленности «' . $program->name . '» ' . ((null === $model->module->name) ? ('модуля (года) - ' . $model->module->year) : 'модуля: «' . $model->module->name . '»') . ' (далее – Образовательная услуга, Программа), в пределах учебного плана программы, предусмотренного на период обучения по Договору.<br>
    2.2. Форма обучения и используемые образовательные технологии: ' . $programform . '<br>
	2.3. Заказчик обязуется содействовать получению Обучающимся образовательной услуги' . $text1 . '.<br>
	2.4. ' . $text144 . ' Период обучения по Договору: с ' . Yii::$app->formatter->asDate($model->start_edu_contract) . ' по ' . Yii::$app->formatter->asDate($finishStudyDate) . '.
</div>

<p style="text-align:center">III. Права Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
    3.1.  Исполнитель вправе:<br>
    3.1.1. Самостоятельно осуществлять образовательный процесс, устанавливать системы оценок, формы, порядок и периодичность проведения промежуточной аттестации Обучающегося.<br>
    3.1.2. Применять к Обучающемуся меры поощрения и меры дисциплинарного взыскания в соответствии с законодательством Российской Федерации, учредительными документами Исполнителя, настоящим Договором и локальными нормативными актами Исполнителя.<br>
    3.1.3. В случае невозможности проведения необходимого числа занятий, предусмотренных учебным планом, на определенный месяц оказания образовательной услуги, обеспечить оказание образовательной услуги в полном объеме за счет проведения дополнительных занятий в последующие месяцы действия настоящего Договора.<br>
    3.2. Заказчик вправе:<br>
    3.2.1. Получать информацию от Исполнителя по вопросам организации и обеспечения надлежащего оказания образовательной услуги.<br>
    3.2.2. Обращаться к Исполнителю по вопросам, касающимся образовательного процесса.<br>
    3.2.3. Участвовать в оценке качества образовательной услуги, проводимой в рамках системы персонифицированного финансирования.<br>
    3.3. Обучающемуся предоставляются академические права в соответствии с частью 1 статьи 34 Федерального закона от 29 декабря 2012 г. №273-ФЗ "Об образовании в Российской Федерации". Обучающийся также вправе:<br>
    3.3.1. Получать информацию от Исполнителя по вопросам организации и обеспечения надлежащего оказания образовательной услуги.<br>
    3.3.2. Обращаться к Исполнителю по вопросам, касающимся образовательного процесса.<br>
    3.3.3. Пользоваться в порядке, установленном локальными нормативными актами, имуществом Исполнителя, необходимым для освоения Программы.<br>
    3.3.4. Принимать в порядке, установленном локальными нормативными актами, участие в социально-культурных, оздоровительных и иных мероприятиях, организованных Исполнителем.<br>
    3.3.5. Получать полную и достоверную информацию об оценке своих знаний, умений, навыков и компетенций, а также о критериях этой оценки.
</div>

<p style="text-align:center">IV. Обязанности Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
	4.1. Исполнитель обязан:<br>
    4.1.1. Зачислить Обучающегося в качестве учащегося на обучение по Программе.<br>
    4.1.2. Довести до Заказчика информацию, содержащую сведения о предоставлении платных образовательных услуг в порядке и объеме, которые предусмотрены Законом Российской Федерации "О защите прав потребителей" и Федеральным законом "Об образовании в Российской Федерации"<br>
    4.1.3. Организовать и обеспечить надлежащее предоставление образовательных услуг, предусмотренных разделом I настоящего Договора. Образовательные услуги оказываются в соответствии с учебным планом Программы и расписанием занятий Исполнителя.<br>
    4.1.4. Обеспечить полное выполнение учебного плана Программы, предусмотренного на период обучения по Договору. В случае отмены проведения части занятий, предусмотренных в учебном плане на конкретный месяц, провести их дополнительно в том же или последующем месяце, либо провести перерасчет стоимости оплаты за месяц, предусмотренный разделом V настоящего Договора.<br>
    4.1.5. Обеспечить Обучающемуся предусмотренные Программой условия ее освоения, в том числе:<br>
        4.1.5.1. Обеспечить сопровождение оказания услуги педагогическим работником, квалификация которого соответствует следующим условиям:<br> «' . $year->kvfirst . '»<br>
        ' . $text2 . '
        
    4.2. Заказчик обязан:<br>
        ' . $text3 . '
        
    4.3. Обучающийся обязан:<br>
        4.3.1. Выполнять задания для подготовки к занятиям, предусмотренным учебным планом Программы<br>
        4.3.2. Извещать Исполнителя о причинах отсутствия на занятиях.<br>
        4.3.3. Обучаться по образовательной программе с соблюдением требований, установленных учебным планом Программы<br>
        4.3.4. Соблюдать требования учредительных документов, правила внутреннего распорядка и иные локальные нормативные акты Исполнителя.<br>
        4.3.5. Соблюдать иные требования, установленные в статье 43 Федерального закона от 29 декабря 2012 г. №273-ФЗ "Об образовании в Российской Федерации"<br>
</div>

<p style="text-align:center">V. Стоимость услуги, сроки и порядок их оплаты</p>
</div>
';

        $mpdf = new mPDF();
        $mpdf->WriteHtml($html); // call mpdf write html
        $mpdf->WriteHtml($text); // call mpdf write html

        $mpdf->WriteHtml('<div align="justify"  style="font-size: ' . $model->fontsize . '">' . $text4 . '</div>');


        $mpdf->WriteHtml('
<div style="font-size: ' . $model->fontsize . '" >
<p style="text-align:center">VI. Основания изменения и порядок расторжения договора</p>

<div align="justify">
    6.1. Условия, на которых заключен настоящий Договор, могут быть изменены по соглашению Сторон или в соответствии с законодательством Российской Федерации.<br>
    6.2. Настоящий Договор может быть расторгнут по соглашению Сторон.<br>
    6.3. Настоящий Договор может быть расторгнут по инициативе Исполнителя в одностороннем порядке в случаях:<br>
    установления нарушения порядка приема Обучающегося на обучение по Программе, повлекшего по вине Обучающегося его незаконное зачисление на обучение по Программе;<br>
    просрочки оплаты стоимости образовательной услуг со стороны Уполномоченной организации и/или Заказчика.
    невозможности надлежащего исполнения обязательства по оказанию образовательной услуги вследствие действий (бездействия) Обучающегося;<br>
    приостановления действия сертификата дополнительного образования Обучающегося;<br>
    получения предписания о расторжении договора от Уполномоченной организации, направляемой Уполномоченной организацией Исполнителю в соответствии с Соглашением;<br>
    в иных случаях, предусмотренных законодательством Российской Федерации.<br>
    6.4. Настоящий Договор может быть расторгнут по инициативе Заказчика.<br>
    6.5. Исполнитель вправе отказаться от исполнения обязательств по Договору при условии полного возмещения Заказчику убытков.<br>
    6.6. Заказчик вправе отказаться от исполнения настоящего Договора при условии оплаты Исполнителю фактически понесенных им расходов, связанных с исполнением обязательств по Договору.<br>
    6.7. Для расторжения договора Заказчик направляет Исполнителю уведомление о расторжении настоящего Договора. Датой расторжения договора является последний день месяца, в котором было направлено указанное уведомление о расторжении настоящего Договора.<br>
    6.8. Для расторжения договора Исполнитель направляет Заказчику уведомление о расторжении настоящего Договора, в котором указывает причину расторжения договора. Датой расторжения договора является последний день месяца, в котором было направлено указанное уведомление о расторжении настоящего Договора.<br>
</div>

<p style="text-align:center">VII. Ответственность Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
    7.1. За неисполнение или ненадлежащее исполнение своих обязательств по Договору Стороны несут ответственность, предусмотренную законодательством Российской Федерации и Договором.<br>
    7.2. При обнаружении недостатка образовательной услуги, в том числе оказания ее не в полном объеме, предусмотренном ' . $text5 . ', Заказчик вправе по своему выбору потребовать:<br>
    7.2.1. Безвозмездного оказания образовательной услуги.<br>
    7.2.2. Возмещения понесенных им расходов по устранению недостатков оказанной образовательной услуги своими силами или третьими лицами.<br>
    7.3. Заказчик вправе отказаться от исполнения Договора и потребовать полного возмещения убытков, если в срок недостатки образовательной услуги не устранены Исполнителем. Заказчик также вправе отказаться от исполнения Договора, если им обнаружен существенный недостаток оказанной образовательной услуги или иные существенные отступления от условий Договора.<br>
    7.4. Если Исполнитель нарушил сроки оказания образовательной услуги (сроки начала и (или) окончания оказания образовательной услуги и (или) промежуточные сроки оказания образовательной услуги) либо если во время оказания образовательной услуги стало очевидным, что она не будет осуществлена в срок, Заказчик вправе по своему выбору:<br>
    7.4.1. Назначить Исполнителю новый срок, в течение которого Исполнитель должен приступить к оказанию образовательной услуги и (или) закончить оказание образовательной услуги.<br>
    7.4.2. Поручить оказать образовательную услугу третьим лицам за разумную цену и потребовать от Исполнителя возмещения понесенных расходов.<br>
    7.4.3. Расторгнуть Договор.<br>
    7.5. Заказчик вправе потребовать полного возмещения убытков, причиненных ему в связи с нарушением сроков начала и (или) окончания оказания образовательной услуги, а также в связи с недостатками образовательной услуги.<br>
</div>

<p style="text-align:center">VIII. Срок действия Договора</p>

<div align="justify">
    8.1. Настоящий Договор вступает в силу с ' . $date_elements_user[2] . '.' . $date_elements_user[1] . '.' . $date_elements_user[0] . ' и действует до полного исполнения Сторонами своих обязательств.<br>
</div>

<p style="text-align:center">IX. Заключительные положения</p>

<div align="justify">
    9.1. Сведения,  указанные  в  настоящем  Договоре,    соответствуют информации,  размещенной  на  официальном  сайте  Исполнителя    в   сети "Интернет" на дату заключения настоящего Договора.<br>
    9.2. Под периодом обучения по Договору  понимается  промежуток  времени  с  даты проведения первого занятия по дату проведения последнего занятия в рамках оказания образовательной услуги.<br>
    9.3. Настоящий Договор составлен в простой письменной форме в электронном виде и размещен на Сайте с обеспечение доступа к нему Заказчика и Исполнителя.  Изменения и дополнения настоящего Договора могут производиться только посредством формирования дополнительных оферт со стороны Заказчика и их акцепта со стороны Исполнителя.<br>
    9.4. Изменения Договора оформляются дополнительными соглашениями к Договору.<br>
    9.5. Изменения раздела IV настоящего договора допускаются лишь при условии согласования указанных изменений с Уполномоченной организацией.<br>


<p style="text-align:center">X. Адреса и реквизиты сторон</p>
</div>
<table align="center" <div style="font-size: ' . $model->fontsize . '" > border="0" cellpadding="10" cellspacing="10">
	<tbody>
		<tr>
			<td width="300" style="vertical-align: top;">
            <p>Исполнитель</p>
            <br>
			<p>' . $organization->name . '</p>

			<p>Юридический адрес: ' . $organization->address_legal . '</p>

			<p>Адрес местонахождения: ' . $organization->address_actual . '</p>

			<p>Наименование банка: ' . $organization->bank_name . '</p>
            
            <p>Город банка: ' . $organization->bank_sity . '</p>

			<p>БИК: ' . $organization->bank_bik . '</p>

			<p>к/с (л/с): ' . $organization->korr_invoice . '</p>

			<p>р/с: ' . $organization->rass_invoice . '</p>
			
			' . (empty($organization->receiver) ? '' : "<p>Получатель: " . $organization->receiver . "</p>") . '
            
            <p>ИНН: ' . $organization->inn . '</p>
            
            <p>КПП: ' . $organization->KPP . '</p>
            
            <p>ОРГН/ОРГНИП: ' . $organization->OGRN . '</p>
            
			</td>
			<td width="300"  style="vertical-align: top;">
                <p>Заказчик</p>
                <br>
                <p>Сведения о Заказчике и Обучающемся указываются в заявлении на зачисление Обучающегося на обучение по дополнительной образовательной программе, указанном в пункте 1.3 настоящего Договора, являющемся неотъемлемой частью настоящего Договора</p>
			</td>
		</tr>
	</tbody>
</table>
</div>');

        if ($ok) {
            $mpdf->Output(Yii::getAlias('@webroot/uploads/contracts/') . $model->url, 'F');
            Yii::$app->session->setFlash('success', 'Оферта успешно оправлена заказчику.');

            return $this->redirect(['verificate', 'id' => $model->id]);
        }
        $mpdf->Output($model->url, 'D');
    }

    public function actionOk($id)
    {
        $model = $this->findModel($id);

        $model->status = Contracts::STATUS_ACCEPTED;
        if ($model->save()) {
            return $this->redirect(['mpdf', 'id' => $id, 'ok' => true]);
        }
    }

    /**
     * Updates an existing Contracts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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

                if ($program->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }

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
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {

                $cont = $this->findModel($id);
                // return var_dump($id);

                $certificates = new Certificates();
                $certificate = $certificates->getCertificates();


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

    /**
     * Finds the Contracts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
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
}
