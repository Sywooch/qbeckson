<?php

namespace app\controllers;

use app\assets\programsAsset\ProgramsAsset;
use app\behaviors\NotificationBehavior;
use app\models\Certificates;
use app\models\certificates\CertificateImportTemplate;
use app\models\certificates\CertificateToAccountingConfirmForm;
use app\models\Contracts;
use app\models\Contracts2Search;
use app\models\Contracts3Search;
use app\models\Contracts5Search;
use app\models\ContractsnSearch;
use app\models\ContractsOrgclearSearch;
use app\models\ContractsoSearch;
use app\models\ContractsPayerclearSearch;
use app\models\Cooperate;
use app\models\FavoritesSearch;
use app\models\forms\OrganizationSettingsForm;
use app\models\GroupsSearch;
use app\models\Invoices;
use app\models\LoginForm;
use app\models\Mun;
use app\models\MunicipalTaskContract;
use app\models\MunicipalTaskMatrix;
use app\models\MunicipalTaskPayerMatrixAssignment;
use app\models\Operators;
use app\models\OperatorSettings;
use app\models\Organization;
use app\models\OrganizationContractSettings;
use app\models\OrganizationPayerAssignment;
use app\models\Payers;
use app\models\PayersSearch;
use app\models\PersonalAssignment;
use app\models\PreviusSearch;
use app\models\ProgrammeModuleSearch;
use app\models\Programs;
use app\models\ProgramsclearSearch;
use app\models\search\CertificatesSearch;
use app\models\search\ContractsSearch;
use app\models\search\CooperateSearch;
use app\models\search\InvoicesSearch;
use app\models\search\MunicipalTaskContractSearch;
use app\models\search\OrganizationSearch;
use app\models\search\ProgramsSearch;
use app\models\UserIdentity;
use kartik\form\ActiveForm;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PersonalController
 * @package app\controllers
 */
class PersonalController extends Controller
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
                    'update-municipality' => ['post'],
                ],
            ],
            'notification' => [
                'class' => NotificationBehavior::className(),
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionOperatorCooperates()
    {
        $searchActive = new CooperateSearch([
            'status' => Cooperate::STATUS_ACTIVE,
            'modelName' => 'SearchActive',
        ]);
        $activeProvider = $searchActive->search(Yii::$app->request->queryParams);
        $allActiveProvider = $searchActive->search(Yii::$app->request->queryParams, 999999);

        $searchAppealed = new CooperateSearch([
            'status' => Cooperate::STATUS_APPEALED,
            'modelName' => 'SearchAppealed',
        ]);
        $appealedProvider = $searchAppealed->search(Yii::$app->request->queryParams);

        $searchNew = new CooperateSearch([
            'status' => Cooperate::STATUS_NEW,
            'modelName' => 'SearchNew',
        ]);
        $newProvider = $searchNew->search(Yii::$app->request->queryParams);

        $searchConfirmed = new CooperateSearch([
            'status' => Cooperate::STATUS_CONFIRMED,
            'modelName' => 'SearchConfirmed',
        ]);
        $confirmedProvider = $searchConfirmed->search(Yii::$app->request->queryParams);

        return $this->render('operator-cooperates', [
            'searchActive' => $searchActive,
            'activeProvider' => $activeProvider,
            'allActiveProvider' => $allActiveProvider,
            'searchAppealed' => $searchAppealed,
            'appealedProvider' => $appealedProvider,
            'searchNew' => $searchNew,
            'newProvider' => $newProvider,
            'searchConfirmed' => $searchConfirmed,
            'confirmedProvider' => $confirmedProvider,
        ]);
    }

    /**
     * Update user municipality binding.
     *
     * @param $munId
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateMunicipality($munId = null)
    {
        if (Mun::findOne($munId) || null === $munId) {
            /** @var UserIdentity $user */
            $user = Yii::$app->user->getIdentity();
            $user->mun_id = $munId;
            if (false === $user->save()) {
                Yii::$app->session->setFlash('danger', 'Что-то не так!');
            }

            return $this->redirect(Yii::$app->request->referrer);
        }
        throw new NotFoundHttpException('Model not found!');
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionOperatorStatistic()
    {
        return $this->render('operator-statistic', [
            'operator' => Yii::$app->user->identity->operator,
        ]);
    }

    /**
     * @return string
     */
    public function actionOperatorInvoices()
    {
        /** @var $operator Operators */
        $operator = Yii::$app->user->identity->operator;
        $exposedSearchInvoices = new InvoicesSearch([
            'sum' => '0,10000000',
            'status' => [Invoices::STATUS_NOT_VIEWED, Invoices::STATUS_IN_THE_WORK],
            'payers_id' => $operator->getPayersViaMun()->select(Payers::tableName() . '.id'),
        ]);
        $exposedInvoicesProvider = $exposedSearchInvoices->search(Yii::$app->request->queryParams);

        $paidSearchInvoices = new InvoicesSearch([
            'sum' => '0,10000000',
            'status' => [Invoices::STATUS_PAID],
            'payers_id' => $operator->getPayersViaMun()->select(Payers::tableName() . '.id'),
        ]);
        $paidInvoicesProvider = $paidSearchInvoices->search(Yii::$app->request->queryParams);

        $removedSearchInvoices = new InvoicesSearch([
            'sum' => '0,10000000',
            'status' => [Invoices::STATUS_REMOVED],
            'payers_id' => $operator->getPayersViaMun()->select(Payers::tableName() . '.id'),
        ]);
        $removedInvoicesProvider = $removedSearchInvoices->search(Yii::$app->request->queryParams);

        $munList = ArrayHelper::map($operator->mun, 'id', 'name');

        return $this->render('operator/operator-invoices', [
            'exposedSearchInvoices' => $exposedSearchInvoices,
            'exposedInvoicesProvider' => $exposedInvoicesProvider,
            'paidSearchInvoices' => $paidSearchInvoices,
            'paidInvoicesProvider' => $paidInvoicesProvider,
            'removedSearchInvoices' => $removedSearchInvoices,
            'removedInvoicesProvider' => $removedInvoicesProvider,
            'munList' => $munList
        ]);
    }


    /**
     * @return string
     */
    public function actionOperatorPayers()
    {
        $searchPayers = new PayersSearch([
            'cooperates' => '0,100',
            'certificates' => '0,150000',
        ]);
        $payersProvider = $searchPayers->search(Yii::$app->request->queryParams);
        $allPayersProvider = $searchPayers->search(Yii::$app->request->queryParams, 999999);

        return $this->render('operator-payers', [
            'searchPayers' => $searchPayers,
            'payersProvider' => $payersProvider,
            'allPayersProvider' => $allPayersProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOperatorOrganizations()
    {
        $searchRegistry = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_ACTIVE, Organization::STATUS_BANNED],
            'programs' => '0,1000',
            'children' => '0,10000',
            'amount_child' => '0,10000',
            'raiting' => '0,100',
            'max_child' => '0,10000',
            'modelName' => 'SearchRegistry',
        ]);
        $registryProvider = $searchRegistry->search(Yii::$app->request->queryParams);
        $allRegistryProvider = $searchRegistry->search(Yii::$app->request->queryParams, 999999);

        $searchRequest = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_NEW],
            'modelName' => 'SearchRequest',
        ]);
        $requestProvider = $searchRequest->search(Yii::$app->request->queryParams);

        $searchRefused = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_REFUSED],
            'modelName' => 'SearchRequest',
        ]);
        $refusedProvider = $searchRefused->search(Yii::$app->request->queryParams);

        return $this->render('operator-organizations', [
            'searchRegistry' => $searchRegistry,
            'registryProvider' => $registryProvider,
            'searchRequest' => $searchRequest,
            'requestProvider' => $requestProvider,

            'searchRefused' => $searchRefused,
            'refusedProvider' => $refusedProvider,

            'allRegistryProvider' => $allRegistryProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOperatorCertificates()
    {
        $searchCertificates = new CertificatesSearch([
            'enableContractsCount' => true,
            'nominal' => '0,150000',
            'rezerv' => '0,150000',
            'balance' => '0,150000',
        ]);
        $certificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams);
        $allCertificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams, 999999);

        return $this->render('operator-certificates', [
            'searchCertificates' => $searchCertificates,
            'certificatesProvider' => $certificatesProvider,
            'allCertificatesProvider' => $allCertificatesProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOperatorContracts()
    {
        $searchActiveContracts = new ContractsSearch([
            'status' => Contracts::STATUS_ACTIVE,
            'paid' => '0,150000',
            'rezerv' => '0,150000',
            'modelName' => 'SearchActiveContracts'
        ]);
        $activeContractsProvider = $searchActiveContracts->search(Yii::$app->request->queryParams);
        $allActiveContractsProvider = $searchActiveContracts->search(Yii::$app->request->queryParams, 999999);

        $searchConfirmedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_ACCEPTED,
            'modelName' => 'SearchConfirmedContracts'
        ]);
        $confirmedContractsProvider = $searchConfirmedContracts->search(Yii::$app->request->queryParams);

        $searchPendingContracts = new ContractsSearch([
            'status' => Contracts::STATUS_REQUESTED,
            'modelName' => 'SearchPendingContracts'
        ]);
        $pendingContractsProvider = $searchPendingContracts->search(Yii::$app->request->queryParams);
        $allPendingContractsProvider = $searchPendingContracts->search(Yii::$app->request->queryParams, 99999);

        $searchDissolvedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_CLOSED,
            'paid' => '0,150000',
            'modelName' => 'SearchDissolvedContracts'
        ]);
        $dissolvedContractsProvider = $searchDissolvedContracts->search(Yii::$app->request->queryParams);
        $allDissolvedContractsProvider = $searchDissolvedContracts->search(Yii::$app->request->queryParams, 99999);

        $searchRefusedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_REFUSED,
            'paid' => '0,150000',
            'modelName' => 'SearchDissolvedContracts'
        ]);
        $refusedContractsProvider = $searchRefusedContracts->search(Yii::$app->request->queryParams);
        $allRefusedContractsProvider = $searchRefusedContracts->search(Yii::$app->request->queryParams, 99999);


        $allContractsProvider = (new ContractsSearch())->search(Yii::$app->request->queryParams, 99999);

        return $this->render('operator-contracts', [
            'searchActiveContracts' => $searchActiveContracts,
            'activeContractsProvider' => $activeContractsProvider,
            'searchConfirmedContracts' => $searchConfirmedContracts,
            'confirmedContractsProvider' => $confirmedContractsProvider,
            'searchPendingContracts' => $searchPendingContracts,
            'pendingContractsProvider' => $pendingContractsProvider,
            'searchDissolvedContracts' => $searchDissolvedContracts,
            'dissolvedContractsProvider' => $dissolvedContractsProvider,

            'searchRefusedContracts' => $searchRefusedContracts,
            'refusedContractsProvider' => $refusedContractsProvider,


            'allActiveContractsProvider' => $allActiveContractsProvider,
            'allPendingContractsProvider' => $allPendingContractsProvider,
            'allDissolvedContractsProvider' => $allDissolvedContractsProvider,
            'allContractsProvider' => $allContractsProvider,
            'allRefusedContractsProvider' => $allRefusedContractsProvider
        ]);
    }

    /**
     * @return string
     */
    public function actionOperatorPrograms()
    {
        $searchOpenPrograms = new ProgramsSearch([
            'verification' => Programs::VERIFICATION_DONE,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchOpenPrograms',
        ]);
        $openProgramsProvider = $searchOpenPrograms->search(Yii::$app->request->queryParams);
        $allOpenProgramsProvider = $searchOpenPrograms->search(Yii::$app->request->queryParams, 999999);

        $searchWaitPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
            'open' => 0,
            'hours' => '0,2000',
            'modelName' => 'SearchWaitPrograms',
        ]);
        $waitProgramsProvider = $searchWaitPrograms->search(Yii::$app->request->queryParams);
        $allWaitProgramsProvider = $searchWaitPrograms->search(Yii::$app->request->queryParams, 999999);

        $searchClosedPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_DENIED],
            'hours' => '0,2000',
            'modelName' => 'SearchClosedPrograms',
        ]);
        $closedProgramsProvider = $searchClosedPrograms->search(Yii::$app->request->queryParams);
        $allClosedProgramsProvider = $searchClosedPrograms->search(Yii::$app->request->queryParams, 999999);

        $searchProgramsall = new ProgramsclearSearch();
        $ProgramsallProvider = $searchProgramsall->search(Yii::$app->request->queryParams);

        $searchYearsall = new ProgrammeModuleSearch();
        $YearsallProvider = $searchYearsall->search(Yii::$app->request->queryParams);

        $searchGroupsall = new GroupsSearch();
        $GroupsallProvider = $searchGroupsall->search(Yii::$app->request->queryParams);

        return $this->render('operator-programs', [
            'searchOpenPrograms' => $searchOpenPrograms,
            'openProgramsProvider' => $openProgramsProvider,
            'searchWaitPrograms' => $searchWaitPrograms,
            'waitProgramsProvider' => $waitProgramsProvider,
            'searchClosedPrograms' => $searchClosedPrograms,
            'closedProgramsProvider' => $closedProgramsProvider,
            'allOpenProgramsProvider' => $allOpenProgramsProvider,
            'allWaitProgramsProvider' => $allWaitProgramsProvider,
            'allClosedProgramsProvider' => $allClosedProgramsProvider,

            'ProgramsallProvider' => $ProgramsallProvider,
            'YearsallProvider' => $YearsallProvider,
            'GroupsallProvider' => $GroupsallProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerStatistic()
    {
        return $this->render('payer-statistic', [
            'payer' => Yii::$app->user->identity->payer,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerCertificates()
    {
        $searchCertificates = new CertificatesSearch([
            'enableContractsCount' => true,
            'onlyPayerIds' => Yii::$app->user->identity->payer->id,
            'nominal' => '0,150000',
            'rezerv' => '0,150000',
            'balance' => '0,150000',
        ]);
        $certificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams);
        $allCertificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams, 999999);

        $certificateToAccountingConfirmForm = new CertificateToAccountingConfirmForm;
        if (Yii::$app->request->isAjax && $certificateToAccountingConfirmForm->load(Yii::$app->request->post())) {
            return $this->asJson(ActiveForm::validate($certificateToAccountingConfirmForm));
        }

        if ($certificateToAccountingConfirmForm->load(Yii::$app->request->post()) && $certificateToAccountingConfirmForm->validate()) {
            return $this->redirect('/certificates/change-to-accounting-type');
        }

        $certificateImportTemplateExists = CertificateImportTemplate::exists();

        return $this->render('payer-certificates', [
            'certificatesProvider' => $certificatesProvider,
            'searchCertificates' => $searchCertificates,
            'allCertificatesProvider' => $allCertificatesProvider,
            'certificateToAccountingConfirmForm' => $certificateToAccountingConfirmForm,
            'certificateImportTemplateExists' => $certificateImportTemplateExists,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerContracts()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();
        $payer = $user->payer;
        $searchActiveContracts = new ContractsSearch([
            'payer_id' => $payer->id,
            'status' => Contracts::STATUS_ACTIVE,
            'paid' => '0,150000',
            'rezerv' => '0,150000',
            'modelName' => 'SearchActiveContracts'
        ]);
        $activeContractsProvider = $searchActiveContracts->search(Yii::$app->request->queryParams);

        $searchConfirmedContracts = new ContractsSearch([
            'payer_id' => $payer->id,
            'status' => Contracts::STATUS_ACCEPTED,
            'modelName' => 'SearchConfirmedContracts'
        ]);
        $confirmedContractsProvider = $searchConfirmedContracts->search(Yii::$app->request->queryParams);

        $searchPendingContracts = new ContractsSearch([
            'payer_id' => $payer->id,
            'status' => Contracts::STATUS_REQUESTED,
            'modelName' => 'SearchPendingContracts'
        ]);
        $pendingContractsProvider = $searchPendingContracts->search(Yii::$app->request->queryParams);

        $searchDissolvedContracts = new ContractsSearch([
            'payer_id' => $payer->id,
            'status' => Contracts::STATUS_CLOSED,
            'paid' => '0,150000',
            'modelName' => 'SearchDissolvedContracts'
        ]);
        $dissolvedContractsProvider = $searchDissolvedContracts->search(Yii::$app->request->queryParams);

        $searchContractsall = new ContractsPayerclearSearch();
        $ContractsallProvider = $searchContractsall->search(Yii::$app->request->queryParams);

        return $this->render('payer-contracts', [
            'searchActiveContracts' => $searchActiveContracts,
            'activeContractsProvider' => $activeContractsProvider,
            'searchConfirmedContracts' => $searchConfirmedContracts,
            'confirmedContractsProvider' => $confirmedContractsProvider,
            'searchPendingContracts' => $searchPendingContracts,
            'pendingContractsProvider' => $pendingContractsProvider,
            'searchDissolvedContracts' => $searchDissolvedContracts,
            'dissolvedContractsProvider' => $dissolvedContractsProvider,

            'ContractsallProvider' => $ContractsallProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerOrganizations()
    {
        $searchRegistry = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_ACTIVE],
            'cooperateStatus' => Cooperate::STATUS_ACTIVE,
            'cooperatePayerId' => Yii::$app->user->getIdentity()->payer->id,
            'programs' => '0,1000',
            'children' => '0,10000',
            'amount_child' => '0,10000',
            'raiting' => '0,100',
            'max_child' => '0,10000',
            'modelName' => 'SearchRegistry',
        ]);
        $registryProvider = $searchRegistry->search(Yii::$app->request->queryParams);

        $searchRequest = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_ACTIVE],
            'cooperateStatus' => Cooperate::STATUS_NEW,
            'cooperatePayerId' => Yii::$app->user->getIdentity()->payer->id,
            'modelName' => 'SearchRequest',
        ]);
        $requestProvider = $searchRequest->search(Yii::$app->request->queryParams);

        $searchReject = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_ACTIVE],
            'cooperateStatus' => [Cooperate::STATUS_REJECTED, Cooperate::STATUS_APPEALED],
            'cooperatePayerId' => Yii::$app->user->getIdentity()->payer->id,
            'modelName' => 'SearchReject',
        ]);
        $rejectProvider = $searchReject->search(Yii::$app->request->queryParams);

        $searchConfirm = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_ACTIVE],
            'cooperateStatus' => Cooperate::STATUS_CONFIRMED,
            'cooperatePayerId' => Yii::$app->user->getIdentity()->payer->id,
            'modelName' => 'SearchConfirm',
        ]);
        $confirmProvider = $searchConfirm->search(Yii::$app->request->queryParams);

        return $this->render('payer-organizations', [
            'searchRegistry' => $searchRegistry,
            'registryProvider' => $registryProvider,
            'searchRequest' => $searchRequest,
            'requestProvider' => $requestProvider,
            'searchReject' => $searchReject,
            'rejectProvider' => $rejectProvider,
            'searchConfirm' => $searchConfirm,
            'confirmProvider' => $confirmProvider,
        ]);
    }

    public function actionPayerSuborderOrganizations()
    {
        $searchModel = new OrganizationSearch(['subordered' => true]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax && Yii::$app->request->post('hasEditable')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = Organization::findOne(Yii::$app->request->post('editableKey'));
            $model->scenario = Organization::SCENARIO_PAYER;
            $post = Yii::$app->request->post();

            $out = ['output' => '', 'message' => ''];
            $data = ['Organization' => current($post['Organization'])];

            $output = '';
            if ($model->load($data) && $model->validate()) {
                $model->save(false);
            } else {
                $out = ['output' => $output, 'message' => 'Ошибка при сохранении.'];
            }

            return $out;
        }

        return $this->render('payer-suborder-organizations', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPayerAllOrganizations()
    {
        $searchModel = new OrganizationSearch(['possibleForSuborder' => true]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('payer-all-organizations', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerPrograms()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $searchPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_DONE],
            'payerId' => $user->payer->id,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => '',
        ]);
        $programsProvider = $searchPrograms->search(Yii::$app->request->queryParams);

        return $this->render('payer-programs', [
            'programsProvider' => $programsProvider,
            'searchPrograms' => $searchPrograms,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerMunicipalTask()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $searchWaitTasks = new ProgramsSearch([
            'taskPayerId' => $user->payer->id,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchTasks',
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
            'isMunicipalTask' => true,
        ]);
        $waitTasksProvider = $searchWaitTasks->search(Yii::$app->request->queryParams);

        $searchRefusedTasks = new ProgramsSearch([
            'taskPayerId' => $user->payer->id,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchTasks',
            'verification' => Programs::VERIFICATION_DENIED,
            'isMunicipalTask' => true,
        ]);
        $refusedTasksProvider = $searchRefusedTasks->search(Yii::$app->request->queryParams);

        $matrix = MunicipalTaskMatrix::find()->all();
        $tabs = [];
        foreach ($matrix as $item) {
            $searchTasks = new ProgramsSearch([
                'taskPayerId' => $user->payer->id,
                'hours' => '0,2000',
                'limit' => '0,10000',
                'rating' => '0,100',
                'modelName' => 'SearchTasks',
                'verification' => Programs::VERIFICATION_DONE,
                'isMunicipalTask' => true,
                'municipal_task_matrix_id' => $item->id,
            ]);
            $tasksProvider = $searchTasks->search(Yii::$app->request->queryParams);
            array_push($tabs, ['item' => $item, 'searchModel' => $searchTasks, 'provider' => $tasksProvider]);
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->post('hasEditable')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = Programs::findOne(Yii::$app->request->post('editableKey'));
            $post = Yii::$app->request->post();

            $out = ['output' => '', 'message' => ''];
            $data = ['Programs' => current($post['Programs'])];

            $output = '';
            if ($model->load($data) && $model->validate()) {
                $model->save(false);
            } else {
                $out = ['output' => $output, 'message' => 'Ошибка при сохранении.'];
            }

            return $out;
        }

        return $this->render('payer-municipal-task', [
            'searchWaitTasks' => $searchWaitTasks,
            'waitTasksProvider' => $waitTasksProvider,
            'searchRefusedTasks' => $searchRefusedTasks,
            'refusedTasksProvider' => $refusedTasksProvider,
            'tabs' => $tabs,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerInvoices()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $exposedSearchInvoices = new InvoicesSearch([
            'status' => [Invoices::STATUS_NOT_VIEWED, Invoices::STATUS_IN_THE_WORK],
            'payers_id' => $user->payer->id,
            'organization_id' => ArrayHelper::getColumn($user->payer->cooperates, 'organization_id'),
            'sum' => '0,10000000',
        ]);
        $exposedInvoicesProvider = $exposedSearchInvoices->search(Yii::$app->request->queryParams);

        $paidSearchInvoices = new InvoicesSearch([
            'status' => [Invoices::STATUS_PAID],
            'payers_id' => $user->payer->id,
            'organization_id' => ArrayHelper::getColumn($user->payer->cooperates, 'organization_id'),
            'sum' => '0,10000000',
        ]);
        $paidInvoicesProvider = $paidSearchInvoices->search(Yii::$app->request->queryParams);

        $removedSearchInvoices = new InvoicesSearch([
            'status' => [Invoices::STATUS_REMOVED],
            'payers_id' => $user->payer->id,
            'organization_id' => ArrayHelper::getColumn($user->payer->cooperates, 'organization_id'),
            'sum' => '0,10000000',
        ]);
        $removedInvoicesProvider = $removedSearchInvoices->search(Yii::$app->request->queryParams);

        return $this->render('payer-invoices', [
            'exposedSearchInvoices' => $exposedSearchInvoices,
            'exposedInvoicesProvider' => $exposedInvoicesProvider,
            'paidSearchInvoices' => $paidSearchInvoices,
            'paidInvoicesProvider' => $paidInvoicesProvider,
            'removedSearchInvoices' => $removedSearchInvoices,
            'removedInvoicesProvider' => $removedInvoicesProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationStatistic()
    {
        return $this->render('organization-statistic', [
            'organization' => Yii::$app->user->identity->organization,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationInfo()
    {
        $organization = Yii::$app->user->identity->organization;

        $organizationSettingsForm = new OrganizationSettingsForm();

        $model = $organization->contractSettings ??
            new OrganizationContractSettings(['organization_id' => $organization->id]);
        $organizationSettingsForm->setModel($model);

        if ($organization->load(Yii::$app->request->post()) && $organization->save()) {
            Yii::$app->session->setFlash('success', 'Информация успешно сохранена.');
        }

        if ($organizationSettingsForm->load(Yii::$app->request->post()) && $organizationSettingsForm->save()) {
            Yii::$app->session->setFlash('success', 'Настройки договора успешно сохранены.');

            return $this->refresh();
        }

        return $this->render('organization-info', [
            'organization' => $organization,
            'organizationSettings' => $organizationSettingsForm,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationFavorites()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $searchFavorites = new PreviusSearch();
        $searchFavorites->organization_id = $organization['id'];
        if (isset($_GET['year'])) {
            $searchFavorites->year_id = $_GET['year'];
        }
        if (isset($_GET['program'])) {
            $searchFavorites->program_id = $_GET['program'];
        }
        $FavoritesProvider = $searchFavorites->search(Yii::$app->request->queryParams);

        return $this->render('organization-favorites', [
            'searchFavorites' => $searchFavorites,
            'FavoritesProvider' => $FavoritesProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationPrograms()
    {
        $searchOpenPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'verification' => [Programs::VERIFICATION_DONE],
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchOpenPrograms',
        ]);
        $openProgramsProvider = $searchOpenPrograms->search(Yii::$app->request->queryParams);

        $searchWaitPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
            'open' => 0,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchWaitPrograms',
        ]);
        $waitProgramsProvider = $searchWaitPrograms->search(Yii::$app->request->queryParams);

        $searchClosedPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'verification' => [Programs::VERIFICATION_DENIED],
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchClosedPrograms',
        ]);
        $closedProgramsProvider = $searchClosedPrograms->search(Yii::$app->request->queryParams);

        $searchDraftPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'verification' => [Programs::VERIFICATION_DRAFT],
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchDraftPrograms',
        ]);
        $draftProgramsProvider = $searchDraftPrograms->search(Yii::$app->request->queryParams);

        return $this->render('organization-programs', [
            'searchOpenPrograms' => $searchOpenPrograms,
            'openProgramsProvider' => $openProgramsProvider,
            'searchWaitPrograms' => $searchWaitPrograms,
            'waitProgramsProvider' => $waitProgramsProvider,
            'searchClosedPrograms' => $searchClosedPrograms,
            'closedProgramsProvider' => $closedProgramsProvider,
            'searchDraftPrograms' => $searchDraftPrograms,
            'draftProgramsProvider' => $draftProgramsProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationMunicipalTask()
    {
        $matrix = MunicipalTaskMatrix::find()->all();
        $tabs = [];
        foreach ($matrix as $item) {
            $searchTasks = new ProgramsSearch([
                'organization_id' => Yii::$app->user->identity->organization->id,
                'hours' => '0,2000',
                'limit' => '0,10000',
                'rating' => '0,100',
                'modelName' => 'SearchTasks',
                'verification' => Programs::VERIFICATION_DONE,
                'isMunicipalTask' => true,
                'municipal_task_matrix_id' => $item->id,
            ]);
            $tasksProvider = $searchTasks->search(Yii::$app->request->queryParams);
            array_push($tabs, ['item' => $item, 'searchModel' => $searchTasks, 'provider' => $tasksProvider]);
        }

        $searchWaitPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchTasks',
            'isMunicipalTask' => true,
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
        ]);
        $waitProgramsProvider = $searchWaitPrograms->search(Yii::$app->request->queryParams);

        $searchDeniedPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'hours' => '0,2000',
            'limit' => '0,10000',
            'rating' => '0,100',
            'modelName' => 'SearchTasks',
            'isMunicipalTask' => true,
            'verification' => Programs::VERIFICATION_DENIED,
        ]);
        $deniedProgramsProvider = $searchDeniedPrograms->search(Yii::$app->request->queryParams);

        return $this->render('organization-municipal-task', [
            'tabs' => $tabs,
            'searchWaitPrograms' => $searchWaitPrograms,
            'waitProgramsProvider' => $waitProgramsProvider,
            'searchDeniedPrograms' => $searchDeniedPrograms,
            'deniedProgramsProvider' => $deniedProgramsProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationContracts()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->identity;

        $searchActiveContracts = new ContractsSearch([
            'status' => Contracts::STATUS_ACTIVE,
            'paid' => '0,150000',
            'rezerv' => '0,150000',
            'all_parents_funds' => '0,10000',
            'modelName' => 'SearchActiveContracts',
            'organization_id' => $user->organization->id,
        ]);
        $activeContractsProvider = $searchActiveContracts->search(Yii::$app->request->queryParams);

        $searchConfirmedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_ACCEPTED,
            'modelName' => 'SearchConfirmedContracts',
            'organization_id' => $user->organization->id,
        ]);
        $confirmedContractsProvider = $searchConfirmedContracts->search(Yii::$app->request->queryParams);

        $searchPendingContracts = new ContractsSearch([
            'status' => Contracts::STATUS_REQUESTED,
            'modelName' => 'SearchPendingContracts',
            'organization_id' => $user->organization->id,
        ]);
        $pendingContractsProvider = $searchPendingContracts->search(Yii::$app->request->queryParams);

        $searchDissolvedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_CLOSED,
            'paid' => '0,150000',
            'modelName' => 'SearchDissolvedContracts',
            'organization_id' => $user->organization->id,
        ]);
        $dissolvedContractsProvider = $searchDissolvedContracts->search(Yii::$app->request->queryParams);

        $searchEndsContracts = new ContractsSearch([
            'status' => Contracts::STATUS_ACTIVE,
            'paid' => '0,150000',
            'rezerv' => '0,150000',
            'wait_termnate' => 1,
            'modelName' => 'SearchEndsContracts',
            'organization_id' => $user->organization->id,
        ]);
        $endsContractsProvider = $searchEndsContracts->search(Yii::$app->request->queryParams);

        $searchContractsall = new ContractsOrgclearSearch();
        $ContractsallProvider = $searchContractsall->search(Yii::$app->request->queryParams);

        return $this->render('organization-contracts', [
            'searchActiveContracts' => $searchActiveContracts,
            'activeContractsProvider' => $activeContractsProvider,
            'searchConfirmedContracts' => $searchConfirmedContracts,
            'confirmedContractsProvider' => $confirmedContractsProvider,
            'searchPendingContracts' => $searchPendingContracts,
            'pendingContractsProvider' => $pendingContractsProvider,
            'searchDissolvedContracts' => $searchDissolvedContracts,
            'dissolvedContractsProvider' => $dissolvedContractsProvider,
            'searchEndsContracts' => $searchEndsContracts,
            'endsContractsProvider' => $endsContractsProvider,

            'ContractsallProvider' => $ContractsallProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationMunicipalTaskContracts()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->identity;

        $searchActiveContracts = new MunicipalTaskContractSearch([
            'status' => MunicipalTaskContract::STATUS_ACTIVE,
            'organization_id' => $user->organization->id,
        ]);
        $activeContractsProvider = $searchActiveContracts->search(Yii::$app->request->queryParams);

        $searchPendingContracts = new MunicipalTaskContractSearch([
            'status' => MunicipalTaskContract::STATUS_NEW,
            'organization_id' => $user->organization->id,
        ]);
        $pendingContractsProvider = $searchPendingContracts->search(Yii::$app->request->queryParams);


        return $this->render('organization-municipal-task-contracts', [
            'searchActiveContracts' => $searchActiveContracts,
            'activeContractsProvider' => $activeContractsProvider,
            'searchPendingContracts' => $searchPendingContracts,
            'pendingContractsProvider' => $pendingContractsProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationInvoices()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $exposedSearchInvoices = new InvoicesSearch([
            'status' => [Invoices::STATUS_NOT_VIEWED, Invoices::STATUS_IN_THE_WORK],
            'payers_id' => $user->payer->id,
            'organization_id' => $user->organization->id,
            'sum' => '0,10000000',
        ]);
        $exposedInvoicesProvider = $exposedSearchInvoices->search(Yii::$app->request->queryParams);

        $paidSearchInvoices = new InvoicesSearch([
            'status' => [Invoices::STATUS_PAID],
            'payers_id' => $user->payer->id,
            'organization_id' => $user->organization->id,
            'sum' => '0,10000000',
        ]);
        $paidInvoicesProvider = $paidSearchInvoices->search(Yii::$app->request->queryParams);

        $removedSearchInvoices = new InvoicesSearch([
            'status' => [Invoices::STATUS_REMOVED],
            'payers_id' => $user->payer->id,
            'organization_id' => $user->organization->id,
            'sum' => '0,10000000',
        ]);
        $removedInvoicesProvider = $removedSearchInvoices->search(Yii::$app->request->queryParams);

        return $this->render('organization-invoices', [
            'exposedSearchInvoices' => $exposedSearchInvoices,
            'exposedInvoicesProvider' => $exposedInvoicesProvider,
            'paidSearchInvoices' => $paidSearchInvoices,
            'paidInvoicesProvider' => $paidInvoicesProvider,
            'removedSearchInvoices' => $removedSearchInvoices,
            'removedInvoicesProvider' => $removedInvoicesProvider,
        ]);
    }

    public function actionOrganizationSuborder()
    {
        $model = Yii::$app->user->identity->organization;

        return $this->render('organization-suborder', [
            'model' => $model,
        ]);
    }

    public function actionOrganizationSetSuborderStatus($refuse = 0)
    {
        $model = Yii::$app->user->identity->organization->organizationPayerAssignment;
        if ($model->status != OrganizationPayerAssignment::STATUS_PENDING) {
            throw new BadRequestHttpException('Невозможно выполнить действие.');
        }

        if (!$refuse) {
            $model->status = OrganizationPayerAssignment::STATUS_ACTIVE;
        } else {
            $model->status = OrganizationPayerAssignment::STATUS_REFUSED;
        }
        $model->save();

        $this->redirect(['organization-suborder']);
    }

    /**
     * @return string
     */
    public function actionOrganizationPayers()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $searchOpenPayers = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates' => '0,100',
            'cooperateStatus' => Cooperate::STATUS_ACTIVE,
            'cooperateOrganization' => $user->organization->id,
            'onlyPayers' => ArrayHelper::getColumn($user->organization->cooperates, 'payer_id'),
            'modelName' => 'SearchOpenPayers',
        ]);
        $openPayersProvider = $searchOpenPayers->search(Yii::$app->request->queryParams);

        $searchWaitPayers = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates' => '0,100',
            'cooperateStatus' => Cooperate::STATUS_NEW,
            'cooperateOrganization' => $user->organization->id,
            'onlyPayers' => ArrayHelper::getColumn($user->organization->cooperates, 'payer_id'),
            'modelName' => 'SearchWaitPayers',
        ]);
        $waitPayersProvider = $searchWaitPayers->search(Yii::$app->request->queryParams);

        $searchRejectPayers = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates' => '0,100',
            'cooperateStatus' => [Cooperate::STATUS_REJECTED, Cooperate::STATUS_APPEALED],
            'cooperateOrganization' => $user->organization->id,
            'onlyPayers' => ArrayHelper::getColumn($user->organization->cooperates, 'payer_id'),
            'modelName' => 'SearchRejectPayers',
        ]);
        $rejectPayersProvider = $searchRejectPayers->search(Yii::$app->request->queryParams);

        $searchConfirmPayers = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates' => '0,100',
            'cooperateStatus' => Cooperate::STATUS_CONFIRMED,
            'cooperateOrganization' => $user->organization->id,
            'onlyPayers' => ArrayHelper::getColumn($user->organization->cooperates, 'payer_id'),
            'modelName' => 'SearchConfirmPayers',
        ]);
        $confirmPayersProvider = $searchConfirmPayers->search(Yii::$app->request->queryParams);

        return $this->render('organization-payers', [
            'searchOpenPayers' => $searchOpenPayers,
            'openPayersProvider' => $openPayersProvider,
            'searchWaitPayers' => $searchWaitPayers,
            'waitPayersProvider' => $waitPayersProvider,
            'searchRejectPayers' => $searchRejectPayers,
            'rejectPayersProvider' => $rejectPayersProvider,
            'searchConfirmPayers' => $searchConfirmPayers,
            'confirmPayersProvider' => $confirmPayersProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizationGroups()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $searchGroups = new \app\models\search\GroupsSearch([
            'organization_id' => $user->organization->id
        ]);
        $groupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('organization-groups', [
            'searchGroups' => $searchGroups,
            'groupsProvider' => $groupsProvider,
        ]);
    }

    public function actionCertificateStatistic()
    {
        $certificate = Yii::$app->user->identity->certificate;

        $model = new Programs();

        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(['/programs/search', 'name' => $model->search]);
        }

        $searchContracts1 = new ContractsoSearch();
        $searchContracts1->certificate_id = $certificate['id'];
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);
        $contracts_count = $Contracts1Provider->getTotalCount() + MunicipalTaskContract::getCountContracts($certificate, null, MunicipalTaskContract::STATUS_ACTIVE);

        $Contracts3Search = new Contracts3Search();
        $Contracts3Search->certificate_id = $certificate['id'];
        $Contracts3Provider = $Contracts3Search->search(Yii::$app->request->queryParams);
        $contracts_wait_count = $Contracts3Provider->getTotalCount();

        $ContractsnSearch = new ContractsnSearch();
        $ContractsnSearch->certificate_id = $certificate['id'];
        $ContractsnProvider = $ContractsnSearch->search(Yii::$app->request->queryParams);
        $contracts_wait_request = $ContractsnProvider->getTotalCount() + MunicipalTaskContract::getCountContracts($certificate, null, MunicipalTaskContract::STATUS_NEW);

        $Contracts2Search = new Contracts2Search();
        $Contracts2Search->certificate_id = $certificate['id'];
        $Contracts2Provider = $Contracts2Search->search(Yii::$app->request->queryParams);
        $contracts_arh1 = $Contracts2Provider->getTotalCount();

        $Contracts4Search = new Contracts5Search();
        $Contracts4Search->certificate_id = $certificate['id'];
        $Contracts4Provider = $Contracts4Search->search(Yii::$app->request->queryParams);
        $contracts_arh2 = $Contracts4Provider->getTotalCount();

        $contracts_arhive = $contracts_arh2 + $contracts_arh1;

        $searchPrev = new PreviusSearch();
        $searchPrev->certificate_id = $certificate['id'];
        $searchPrev->actual = 1;
        $PrevProvider = $searchPrev->search(Yii::$app->request->queryParams);
        $contracts_previus = $PrevProvider->getTotalCount();

        $searchFavorites = new FavoritesSearch();
        $searchFavorites->certificate_id = $certificate['id'];
        $FavoritesProvider = $searchFavorites->search(Yii::$app->request->queryParams);
        $contracts_favorites = $FavoritesProvider->getTotalCount();


        return $this->render('certificate-statistic', [
            'model' => $model,
            'contracts_count' => $contracts_count,
            'contracts_wait_count' => $contracts_wait_count,
            'contracts_wait_request' => $contracts_wait_request,
            'contracts_arhive' => $contracts_arhive,
            'contracts_previus' => $contracts_previus,
            'contracts_favorites' => $contracts_favorites,
        ]);
    }

    public function actionCertificateInfo()
    {
        /** @var $model Certificates */
        $model = Yii::$app->user->identity->certificate;
        $model->scenario = Certificates::SCENARIO_CERTIFICATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $flagGroupHasBeenChanged = false;
            if ($model->canChangeGroup && $model->oldAttributes['cert_group'] != $model->cert_group) {
                if ($model->changeCertGroup() == Certificates::FLAG_GROUP_HAS_BEEN_CHANGED) {
                    $flagGroupHasBeenChanged = true;
                }
            }

            if ($model->save(false)) {
                if ($flagGroupHasBeenChanged === true) {
                    Yii::$app->session->setFlash('success', 'Вам успешно поменяли вид сертификата на сертификат персонифицированного финансирования.');
                }

                return $this->refresh();
            }
        }

        return $this->render('certificate-info', [
            'certificate' => $model,
        ]);
    }

    public function actionCertificateWaitContract()
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $Contracts3Search = new Contracts3Search();
        $Contracts3Search->certificate_id = $certificate['id'];
        $Contracts3Provider = $Contracts3Search->search(Yii::$app->request->queryParams);

        return $this->render('certificate-wait-contract', [
            'Contracts3Search' => $Contracts3Search,
            'Contracts3Provider' => $Contracts3Provider,
        ]);
    }

    public function actionCertificateWaitRequest()
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $ContractsnSearch = new ContractsnSearch();
        $ContractsnSearch->certificate_id = $certificate['id'];
        $ContractsnProvider = $ContractsnSearch->search(Yii::$app->request->queryParams);

        return $this->render('certificate-wait-request', [
            'ContractsnSearch' => $ContractsnSearch,
            'ContractsnProvider' => $ContractsnProvider,
        ]);
    }

    public function actionCertificatePrevius()
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $searchPrev = new PreviusSearch();
        $searchPrev->certificate_id = $certificate['id'];
        $searchPrev->actual = 1;
        $PrevProvider = $searchPrev->search(Yii::$app->request->queryParams);

        return $this->render('certificate-previus', [
            'searchPrev' => $searchPrev,
            'PrevProvider' => $PrevProvider,
        ]);
    }

    public function actionCertificateContracts()
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $searchContracts1 = new ContractsoSearch();
        $searchContracts1->certificate_id = $certificate['id'];
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);

        return $this->render('certificate-contracts', [
            'searchContracts1' => $searchContracts1,
            'Contracts1Provider' => $Contracts1Provider,
        ]);
    }

    public function actionCertificateArchive()
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $Contracts2Search = new Contracts2Search();
        $Contracts2Search->certificate_id = $certificate['id'];
        $Contracts2Provider = $Contracts2Search->search(Yii::$app->request->queryParams);

        $Contracts4Search = new Contracts5Search();
        $Contracts4Search->certificate_id = $certificate['id'];
        $Contracts4Provider = $Contracts4Search->search(Yii::$app->request->queryParams);


        return $this->render('certificate-archive', [
            'Contracts2Search' => $Contracts2Search,
            'Contracts2Provider' => $Contracts2Provider,
            'Contracts4Search' => $Contracts4Search,
            'Contracts4Provider' => $Contracts4Provider,
        ]);
    }

    public function actionCertificateFavorites()
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $searchFavorites = new FavoritesSearch();
        $searchFavorites->certificate_id = $certificate['id'];
        $FavoritesProvider = $searchFavorites->search(Yii::$app->request->queryParams);

        return $this->render('certificate-favorites', [
            'searchFavorites' => $searchFavorites,
            'FavoritesProvider' => $FavoritesProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionCertificatePrograms(): string
    {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        $matrix = MunicipalTaskPayerMatrixAssignment::findByPayerId($certificate->payer_id, $certificate->certGroup->is_special > 0 ? MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_AC : MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_PF);
        $tabs = [];
        foreach ($matrix as $item) {
            $searchTasks = new ProgramsSearch([
                'verification' => Programs::VERIFICATION_DONE,
                'taskPayerId' => $certificate->payer_id,
                'hours' => '0,2000',
                'rating' => '0,100',
                'modelName' => '',
                'isMunicipalTask' => true,
                'municipal_task_matrix_id' => $item->matrix->id,
            ]);
            $tasksProvider = $searchTasks->search(Yii::$app->request->queryParams);
            array_push($tabs, ['item' => $item, 'searchModel' => $searchTasks, 'provider' => $tasksProvider]);
        }

        $searchModel = new ProgramsSearch([
            'verification' => Programs::VERIFICATION_DONE,
            'hours' => '0,2000',
            'rating' => '0,100',
            'mun' => Yii::$app->user->identity->mun_id,
            'modelName' => '',
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($searchModel->organization_id) {
            Yii::$app->session->setFlash('success', 'Поиск по организации ' . $searchModel->getOrganization()->one()->name);
        }
        ProgramsAsset::register($this->view);

        if (!$certificate->payer->certificateCanCreateContract()) {
            /** @var OperatorSettings $operatorSettings */
            $operatorSettings = Yii::$app->operator->identity->settings;

            $nextPeriodBeginDate = \Yii::$app->formatter->asDate(strtotime($operatorSettings->future_program_date_from));
            $additionMessage = $certificate->payer->certificate_can_use_future_balance ? ' Вы можете подать заявки на обучение на период с ' . $nextPeriodBeginDate . '.' : 'Дождитесь появления возможности подачи заявки на обучение на период с ' . $nextPeriodBeginDate . '.';

            \Yii::$app->session->setFlash('warning', 'Заявки на обучение на период до ' . \Yii::$app->formatter->asDate(strtotime($operatorSettings->current_program_date_to)) . ' не принимаются.' . $additionMessage);
        }

        return $this->render('certificate/list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tabs' => $tabs,
        ]);
    }

    public function actionCertificateOrganizations()
    {
        $searchOrganization = new OrganizationSearch([
            'programs' => '0,1000',
            'children' => '0,10000',
            'raiting' => '0,100',
            'max_child' => '0,10000',
            'modelName' => '',
            'statusArray' => Organization::STATUS_ACTIVE,
        ]);
        $organizationProvider = $searchOrganization->search(Yii::$app->request->queryParams);

        return $this->render('certificate-organizations', [
            'searchOrganization' => $searchOrganization,
            'organizationProvider' => $organizationProvider,
        ]);
    }

    /**
     * assign user personal
     *
     * @param null $munId
     *
     * @return string
     */
    public function actionUserPersonalAssign($munId = null)
    {
        $mun = Mun::findOne($munId);

        $loginForm = new LoginForm;
        if (Yii::$app->request->isAjax && $loginForm->load(Yii::$app->request->post())) {
            $loginForm->username = $mun->payer->user->username;

            if ($loginForm->validate()) {
                PersonalAssignment::assignUserPersonalByMunId($munId);
            }

            return $this->asJson(ActiveForm::validate($loginForm));
        }

        $dataProvider = PersonalAssignment::getDataProviderForMunList();

        $assignedMunList = PersonalAssignment::getAssignedMunList();

        return $this->render('assignment', ['dataProvider' => $dataProvider, 'loginForm' => $loginForm, 'assignedMunList' => $assignedMunList]);
    }

    /**
     * remove user personal assign
     *
     * @param $munId
     *
     * @return Response
     */
    public function actionRemoveUserPersonalAssign($munId)
    {
        PersonalAssignment::removeAssignUserPersonalByMunId($munId);

        return $this->redirect('/personal/user-personal-assign');
    }

    /**
     * login under assigned user
     *
     * @param $userId
     *
     * @return Response
     */
    public function actionAssignedUserLogin($userId)
    {
        if (in_array($userId, PersonalAssignment::getAssignedUserIdList())) {
            Yii::$app->user->login(UserIdentity::findIdentity($userId));

            return $this->goHome();
        } else {
            Yii::$app->session->addFlash('danger', 'Указанный личный кабинет не объединен');

            return $this->goHome();
        }
    }
}
