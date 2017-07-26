<?php

namespace app\controllers;

use app\models\Mun;
use app\models\search\ContractsSearch;
use app\models\search\InvoicesSearch;
use app\models\UserIdentity;
use Yii;
use app\models\Programs;
use app\models\search\ProgramsSearch;
use app\models\ProgramsfromcertSearch;
use app\models\Organization;
use app\models\search\OrganizationSearch;
use app\models\Informs;
use app\models\Contracts;
use app\models\ContractsoSearch;
use app\models\ContractsnSearch;
use app\models\Contracts2Search;
use app\models\Contracts3Search;
use app\models\Contracts4Search;
use app\models\Contracts5Search;
use app\models\InvoicesOrgSearch;
use app\models\PayersSearch;
use app\models\PayersmySearch;
use app\models\PayersWaitSearch;
use app\models\ProgramsclearSearch;
use app\models\ContractsclearSearch;
use app\models\ContractsOrgclearSearch;
use app\models\ContractsPayerclearSearch;
use app\models\Certificates;
use app\models\search\CertificatesSearch;
use app\models\GroupsSearch;
use app\models\FavoritesSearch;
use yii\data\ActiveDataProvider;
use app\models\ProgrammeModuleSearch;
use app\models\PreviusSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        ];
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
            if (!$user->save()) {
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
    public function actionOperatorPayers()
    {
        $searchPayers = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates' => '0,150000'
        ]);
        $payersProvider = $searchPayers->search(Yii::$app->request->queryParams);

        return $this->render('operator-payers', [
            'searchPayers' => $searchPayers,
            'payersProvider' => $payersProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionOperatorOrganizations()
    {
        $searchRegistry = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_ACTIVE, Organization::STATUS_BANNED],
            'programs' => '0,150000',
            'children' => '0,150000',
            'amount_child' => '0,150000',
            'raiting' => '0,150000',
            'max_child' => '0,150000',
            'modelName' => 'SearchRegistry',
        ]);
        $registryProvider = $searchRegistry->search(Yii::$app->request->queryParams);

        $searchRequest = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_NEW],
            'modelName' => 'SearchRequest',
        ]);
        $requestProvider = $searchRequest->search(Yii::$app->request->queryParams);

        return $this->render('operator-organizations', [
            'searchRegistry' => $searchRegistry,
            'registryProvider' => $registryProvider,
            'searchRequest' => $searchRequest,
            'requestProvider' => $requestProvider,
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
            'rezerv' => '-1,150000',
            'balance' => '0,150000',
        ]);
        $certificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams);

        return $this->render('operator-certificates', [
            'searchCertificates' => $searchCertificates,
            'certificatesProvider' => $certificatesProvider,
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

        $searchConfirmedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_ACCEPTED,
            'modelName' => 'SearchConfirmedContracts'
        ]);
        $confirmedContractsProvider = $searchConfirmedContracts->search(Yii::$app->request->queryParams);

        $searchPendingContracts = new ContractsSearch([
            'status' => Contracts::STATUS_CREATED,
            'modelName' => 'SearchPendingContracts'
        ]);
        $pendingContractsProvider = $searchPendingContracts->search(Yii::$app->request->queryParams);

        $searchDissolvedContracts = new ContractsSearch([
            'status' => Contracts::STATUS_CLOSED,
            'paid' => '0,150000',
            'modelName' => 'SearchDissolvedContracts'
        ]);
        $dissolvedContractsProvider = $searchDissolvedContracts->search(Yii::$app->request->queryParams);

        $searchContractsall = new ContractsclearSearch();
        $ContractsallProvider = $searchContractsall->search(Yii::$app->request->queryParams);

        return $this->render('operator-contracts', [
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
    public function actionOperatorPrograms()
    {
        $searchOpenPrograms = new ProgramsSearch([
            'verification' => [2],
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
            'modelName' => 'SearchOpenPrograms',
        ]);
        $openProgramsProvider = $searchOpenPrograms->search(Yii::$app->request->queryParams);

        $searchWaitPrograms = new ProgramsSearch([
            'verification' => [0, 1],
            'open' => 0,
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
            'modelName' => 'SearchWaitPrograms',
        ]);
        $waitProgramsProvider = $searchWaitPrograms->search(Yii::$app->request->queryParams);

        $searchClosedPrograms = new ProgramsSearch([
            'verification' => [3],
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
            'modelName' => 'SearchClosedPrograms',
        ]);
        $closedProgramsProvider = $searchClosedPrograms->search(Yii::$app->request->queryParams);

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
        $payer = Yii::$app->user->identity->payer;

        $searchCertificates = new CertificatesSearch([
            'enableContractsCount' => true,
            'onlyPayerIds' => $payer->id,
            'nominal' => '0,150000',
            'rezerv' => '-1,150000',
            'balance' => '0,150000',
        ]);
        $certificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams);

        return $this->render('payer-certificates', [
            'payer_id' => $payer->id,
            'certificatesProvider' => $certificatesProvider,
            'searchCertificates' => $searchCertificates,
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
            'status' => Contracts::STATUS_CREATED,
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
            'statusArray' => [Organization::STATUS_ACTIVE, Organization::STATUS_BANNED],
            'programs' => '0,150000',
            'children' => '0,150000',
            'amount_child' => '0,150000',
            'raiting' => '0,150000',
            'max_child' => '0,150000',
            'modelName' => 'SearchRegistry',
        ]);
        $registryProvider = $searchRegistry->search(Yii::$app->request->queryParams);

        $searchRequest = new OrganizationSearch([
            'statusArray' => [Organization::STATUS_NEW],
            'modelName' => 'SearchRequest',
        ]);
        $requestProvider = $searchRequest->search(Yii::$app->request->queryParams);

        return $this->render('payer-organizations', [
            'searchRegistry' => $searchRegistry,
            'registryProvider' => $registryProvider,
            'searchRequest' => $searchRequest,
            'requestProvider' => $requestProvider,
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
            'verification' => [2],
            'organization_id' => ArrayHelper::getColumn($user->payer->cooperates, 'organization_id'),
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
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

        if ($organization->load(Yii::$app->request->post()) && $organization->save()) {
            Yii::$app->session->setFlash('success', 'Информация успешно сохранена.');

            return $this->refresh();
        }

        return $this->render('organization-info', [
            'organization' => $organization,
        ]);
    }

    /**
     * @return string
     */
    public function actionPayerInvoices()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $searchInvoices = new InvoicesSearch([
            //'status' => [0, 1, 2],
            'payers_id' => $user->payer->id,
            'organization_id' => ArrayHelper::getColumn($user->payer->cooperates, 'organization_id'),
            'sum' => '0,150000',
        ]);
        $invoicesProvider = $searchInvoices->search(Yii::$app->request->queryParams);

        return $this->render('payer-invoices', [
            'searchInvoices' => $searchInvoices,
            'invoicesProvider' => $invoicesProvider,
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
            'verification' => [2],
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
            'modelName' => 'SearchOpenPrograms',
        ]);
        $openProgramsProvider = $searchOpenPrograms->search(Yii::$app->request->queryParams);

        $searchWaitPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'verification' => [0, 1],
            'open' => 0,
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
            'modelName' => 'SearchWaitPrograms',
        ]);
        $waitProgramsProvider = $searchWaitPrograms->search(Yii::$app->request->queryParams);

        $searchClosedPrograms = new ProgramsSearch([
            'organization_id' => Yii::$app->user->identity->organization->id,
            'verification' => [3],
            'hours' => '-1,150000',
            'limit' => '-1,150000',
            'rating' => '-1,150000',
            'modelName' => 'SearchClosedPrograms',
        ]);
        $closedProgramsProvider = $searchClosedPrograms->search(Yii::$app->request->queryParams);

        return $this->render('organization-programs', [
            'searchOpenPrograms' => $searchOpenPrograms,
            'openProgramsProvider' => $openProgramsProvider,
            'searchWaitPrograms' => $searchWaitPrograms,
            'waitProgramsProvider' => $waitProgramsProvider,
            'searchClosedPrograms' => $searchClosedPrograms,
            'closedProgramsProvider' => $closedProgramsProvider,
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
            'status' => Contracts::STATUS_CREATED,
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
    public function actionOrganizationInvoices()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();

        $searchInvoices = new InvoicesSearch([
            //'status' => [0, 1, 2],
            'payers_id' => $user->payer->id,
            'organization_id' => $user->organization->id,
            'sum' => '0,150000',
        ]);
        $invoicesProvider = $searchInvoices->search(Yii::$app->request->queryParams);

        return $this->render('organization-invoices', [
            'searchInvoices' => $searchInvoices,
            'invoicesProvider' => $invoicesProvider,
        ]);
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
            'cooperates' => '0,150000',
            'cooperateStatus' => 1,
            'id' => ArrayHelper::getColumn($user->organization->cooperates, 'payer_id'),
        ]);
        $openPayersProvider = $searchOpenPayers->search(Yii::$app->request->queryParams);

        $searchWaitPayers = new PayersSearch([
            'certificates' => '0,150000',
            'cooperates' => '0,150000',
            'cooperateStatus' => 0,
            'id' => ArrayHelper::getColumn($user->organization->cooperates, 'payer_id'),
        ]);
        $waitPayersProvider = $searchWaitPayers->search(Yii::$app->request->queryParams);

        return $this->render('organization-payers', [
            'searchOpenPayers' => $searchOpenPayers,
            'openPayersProvider' => $openPayersProvider,
            'searchWaitPayers' => $searchWaitPayers,
            'waitPayersProvider' => $waitPayersProvider,
        ]);
    }




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
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        $model = new Programs();

        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(['/programs/search', 'name' => $model->search]);
        }

        $searchContracts1 = new ContractsoSearch();
        $searchContracts1->certificate_id = $certificate['id'];
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);
        $contracts_count = $Contracts1Provider->getTotalCount();

        $Contracts3Search = new Contracts3Search();
        $Contracts3Search->certificate_id = $certificate['id'];
        $Contracts3Provider = $Contracts3Search->search(Yii::$app->request->queryParams);
        $contracts_wait_count = $Contracts3Provider->getTotalCount();

        $ContractsnSearch = new ContractsnSearch();
        $ContractsnSearch->certificate_id = $certificate['id'];
        $ContractsnProvider = $ContractsnSearch->search(Yii::$app->request->queryParams);
        $contracts_wait_request = $ContractsnProvider->getTotalCount();

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
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read' => 0])->andwhere(['from' => 4])->andwhere(['prof_id' => $certificate['id']]),
        ]);

        $programcertModel = new ProgramsfromcertSearch();
        $programcertProvider = $programcertModel->search(Yii::$app->request->queryParams);
        $count_programs = $programcertProvider->getTotalCount();

        $searchContracts1 = new ContractsoSearch();
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);
        $count_organizations = $Contracts1Provider->getTotalCount();

        return $this->render('certificate-info', [
            'informsProvider' => $informsProvider,
            'count_organizations' => $count_organizations,
            'count_programs' => $count_programs,
            'certificate' => $certificate,
        ]);
    }

    public function actionCertificateWaitContract()
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

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
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();
        $ContractsnSearch = new ContractsnSearch();
        $ContractsnSearch->certificate_id = $certificate['id'];
        $ContractsnProvider = $ContractsnSearch->search(Yii::$app->request->queryParams);

        return $this->render('certificate-wait-request', [
            'ContractsnSearch' => $ContractsnSearch,
            'ContractsnProvider' => $ContractsnProvider,
        ]);
    }

    public function actionCertificatePrograms()
    {
        return $this->redirect(['programs/search']);
    }

    public function actionCertificatePrevius()
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

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
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

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
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

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
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();


        $searchFavorites = new FavoritesSearch();
        $searchFavorites->certificate_id = $certificate['id'];
        $FavoritesProvider = $searchFavorites->search(Yii::$app->request->queryParams);

        return $this->render('certificate-favorites', [
            'searchFavorites' => $searchFavorites,
            'FavoritesProvider' => $FavoritesProvider,
        ]);
    }

    public function actionCertificateOrganizations()
    {
        $searchOrganization = new OrganizationSearch();
        $OrganizationProvider = $searchOrganization->search(Yii::$app->request->queryParams);

        return $this->render('certificate-organizations', [
            'searchOrganization' => $searchOrganization,
            'OrganizationProvider' => $OrganizationProvider,
        ]);
    }
}
