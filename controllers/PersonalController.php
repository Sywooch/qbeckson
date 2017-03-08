<?php

namespace app\controllers;

use Yii;
use app\models\Programs;
use app\models\ProgramsSearch;
use app\models\ProgramsotkSearch;
use app\models\ProgramsallSearch;
use app\models\ProgramsPreviusSearch;
use app\models\ProgramscertSearch;
use app\models\ProgramsfromcertSearch;
use app\models\ProgramsfromnocertSearch;
use app\models\ProgramsPayerSearch;
use app\models\Organization;
use app\models\OrganizationSearch;
use app\models\OrganizationmySearch;
use app\models\OrganizationwaitSearch;
use app\models\Informs;
use app\models\Contracts;
use app\models\ContractsSearch;
use app\models\ContractsoSearch;
use app\models\ContractsnSearch;
use app\models\Contracts2Search;
use app\models\Contracts3Search;
use app\models\Contracts4Search;
use app\models\Contracts5Search;
use app\models\Invoices;
use app\models\InvoicesSearch;
use app\models\InvoicesOrgSearch;
use app\models\InvoicesPayerSearch;
use app\models\Payers;
use app\models\PayersSearch;
use app\models\PayersmySearch;
use app\models\PayersWaitSearch;
use app\models\ProgramsclearSearch;
use app\models\ContractsclearSearch;
use app\models\ContractsOrgclearSearch;
use app\models\ContractsPayerclearSearch;
use app\models\Certificates;
use app\models\search\CertificatesSearch;
use app\models\CertificatesExportSearch;
use app\models\CertificatesPayersSearch;
use app\models\Operators;
use app\models\GroupsSearch;
use app\models\FavoritesSearch;
use app\models\ProgramsfavoritesSearch;
use app\models\Cooperate;
use yii\data\ActiveDataProvider;
use app\models\Years;
use app\models\YearsWaitSearch;
use app\models\YearsSearch;
use app\models\YearsNoSearch;
use app\models\YearsCertSearch;
use app\models\PreviusSearch;
use app\models\Payer1ContractsSearch;
use app\models\Payer0ContractsSearch;
use app\models\Payer4ContractsSearch;
use app\models\Payer3ContractsSearch;



class PersonalController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOperatorStatistic()
    {
        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
        ]);

        $certificates = new Certificates();
        $count_certificates = $certificates->getCountCert('all');

        $organizations = new Organization();
        $count_organizations = $organizations->getCountOrganization();

        $programs = new Programs();
        $count_programs = $programs->getCountProgram();
        
        $operators = new Operators();
        $operator = $operators->getOperators();


        return $this->render('operator-statistic', [
            'InformsProvider' => $InformsProvider,
            'count_certificates' => $count_certificates,
            'count_organizations' => $count_organizations,
            'count_programs' => $count_programs,
            'operator' => $operator,
        ]);
    }

    /*public function actionOperatorInfo()
    {
        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
        ]);

 
        return $this->render('operator-info', [
            'InformsProvider' => $InformsProvider,
            
        ]);
    } */

    public function actionOperatorPayers()
    {
       // $InformsProvider = new ActiveDataProvider([
       //     'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
       // ]);

        $searchPayers = new PayersSearch();
        $PayersProvider = $searchPayers->search(Yii::$app->request->queryParams);

        return $this->render('operator-payers', [
           // 'InformsProvider' => $InformsProvider,
            'searchPayers' => $searchPayers,
            'PayersProvider' => $PayersProvider,
        ]);
    }


    public function actionOperatorOrganizations()
    {
        //$InformsProvider = new ActiveDataProvider([
        //    'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
        //]);

        $searchOrganization = new OrganizationSearch();
        $OrganizationProvider = $searchOrganization->search(Yii::$app->request->queryParams);

        return $this->render('operator-organizations', [
           // 'InformsProvider' => $InformsProvider,
            'searchOrganization' => $searchOrganization,
            'OrganizationProvider' => $OrganizationProvider,
        ]);
    }


    public function actionOperatorCertificates()
    {
        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
        ]);

        $searchCertificates = new CertificatesSearch();
        if (isset($_GET['payer'])) { $searchCertificates->payers = $_GET['payer']; }
        $CertificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams);
            
        
        $searchCertificatesExport = new CertificatesExportSearch();
        if (isset($_GET['payer'])) { $searchCertificatesExport->payers = $_GET['payer']; }
        $CertificatesExportProvider = $searchCertificatesExport->search(Yii::$app->request->queryParams);

        return $this->render('operator-certificates', [
            'InformsProvider' => $InformsProvider,
            'searchCertificates' => $searchCertificates,
            'CertificatesProvider' => $CertificatesProvider,
            'CertificatesExportProvider' => $CertificatesExportProvider,
        ]);
    }


        public function actionOperatorContracts()
    {
        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
        ]);

        $searchContracts1 = new ContractsoSearch();
        if (isset($_GET['payer'])) { $searchContracts1->payers = $_GET['payer']; }
        if (isset($_GET['org'])) { $searchContracts1->organization = $_GET['org']; }
        if (isset($_GET['cert'])) { $searchContracts1->certificate = $_GET['cert']; }
        if (isset($_GET['prog'])) { $searchContracts1->program = $_GET['prog']; }
            
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);

        $searchContracts0 = new ContractsnSearch();
        $Contracts0Provider = $searchContracts0->search(Yii::$app->request->queryParams);
            
        $search3Contracts = new Contracts3Search();
        $Contracts3Provider = $search3Contracts->search(Yii::$app->request->queryParams); // Подтвержденые
            
        $searchContracts5 = new Contracts5Search();
        $Contracts5Provider = $searchContracts5->search(Yii::$app->request->queryParams);
            
        $searchContractsall = new ContractsclearSearch();
        $ContractsallProvider = $searchContractsall->search(Yii::$app->request->queryParams);

        return $this->render('operator-contracts', [
            'InformsProvider' => $InformsProvider,
            'searchContracts0' => $searchContracts0,
            'Contracts0Provider' => $Contracts0Provider,
            'searchContracts1' => $searchContracts1,
            'Contracts1Provider' => $Contracts1Provider,
            'searchContracts5' => $searchContracts5,
            'Contracts5Provider' => $Contracts5Provider,
            'Contracts3Provider' => $Contracts3Provider,
            'search3Contracts' => $search3Contracts,
            'ContractsallProvider' => $ContractsallProvider,
        ]);
    }


        public function actionOperatorPrograms()
    {
        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 1]),
        ]);

        $searchPrograms0 = new ProgramsSearch();
        $Programs0Provider = $searchPrograms0->search(Yii::$app->request->queryParams);
            
                
           // return var_dump($Programs0Provider);

        $searchPrograms1 = new ProgramscertSearch();
        if (isset($_GET['org'])) { $searchPrograms1->organization = $_GET['org']; }
        $Programs1Provider = $searchPrograms1->search(Yii::$app->request->queryParams);
            
        $searchPrograms2 = new ProgramsotkSearch();
        $Programs2Provider = $searchPrograms2->search(Yii::$app->request->queryParams);
            
        $searchProgramsall = new ProgramsclearSearch();
        $ProgramsallProvider = $searchProgramsall->search(Yii::$app->request->queryParams);
            
        $searchYearsall = new YearsSearch();
        $YearsallProvider = $searchYearsall->search(Yii::$app->request->queryParams);
            
        $searchGroupsall = new GroupsSearch();
        $GroupsallProvider = $searchGroupsall->search(Yii::$app->request->queryParams);

        return $this->render('operator-programs', [
            'InformsProvider' => $InformsProvider,
            'searchPrograms0' => $searchPrograms0,
            'Programs0Provider' => $Programs0Provider,
            'searchPrograms1' => $searchPrograms1,
            'Programs1Provider' => $Programs1Provider,
            'searchPrograms2' => $searchPrograms2,
            'Programs2Provider' => $Programs2Provider,
            'ProgramsallProvider' => $ProgramsallProvider,
            'YearsallProvider' => $YearsallProvider,
            'GroupsallProvider' => $GroupsallProvider,
        ]);
    }

    public function actionPayerStatistic()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
        ]);

        $CooperateProvider = new ActiveDataProvider([
            'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
        ]);

        $certificates = new Certificates();
        $count_certificates = $certificates->getCountCertificates($payer['id'], false);
        $sum_certificates = $certificates->getSumCertificates($payer['id']) ? $certificates->getSumCertificates($payer['id']) : 0;
       
        
        $contracts = new Contracts();
        $count_certificates_contracts = $contracts->getCountUsePayerCert($payer['id']);
        $count_certificates_contracts_one = $contracts->getCountUseOnePayerCert($payer['id']);
        $count_certificates_contracts_two = $contracts->getCountUseTwoPayerCert($payer['id']);
        $count_certificates_contracts_more = $contracts->getCountUseMorePayerCert($payer['id']);
         $sum_contracts = $contracts->getCountContractsPayer($payer['id']) ? $contracts->getCountContractsPayer($payer['id']) : 0;
        


        return $this->render('payer-statistic', [
            'InformsProvider' => $InformsProvider,
            'CooperateProvider' => $CooperateProvider,
            'count_certificates' => $count_certificates,
            'count_certificates_contracts' => $count_certificates_contracts,
            'count_certificates_contracts_one' => $count_certificates_contracts_one,
            'count_certificates_contracts_two' => $count_certificates_contracts_two,
            'count_certificates_contracts_more' => $count_certificates_contracts_more,
            'sum_certificates' => $sum_certificates,
            'sum_contracts' => $sum_contracts,
            'payer' => $payer,
        ]);
    }

  /*  public function actionPayerInfo()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
        ]);

        $CooperateProvider = new ActiveDataProvider([
            'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
        ]);

        return $this->render('payer-info', [
            'InformsProvider' => $InformsProvider,
            'CooperateProvider' => $CooperateProvider,
            'payer' => $payer,
        ]);
    } */

    public function actionPayerCertificates()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

       // $InformsProvider = new ActiveDataProvider([
       //     'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
       // ]);

       // $CooperateProvider = new ActiveDataProvider([
       //     'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
       // ]);

        $certificates = new Certificates();
        $count_certificates = $certificates->getCountCertificates($payer['id']);
        $count_certificates_contracts = $certificates->getCountCertificates($payer['id'], 'true');
        $count_certificates_contracts_one = $certificates->getCountCertificates($payer['id'], 1);
        $count_certificates_contracts_two = $certificates->getCountCertificates($payer['id'], 2);
        $count_certificates_contracts_more = $certificates->getCountCertificates($payer['id'], 'more');
        $sum_certificates = $certificates->getSumCertificates($payer['id']) ? $certificates->getSumCertificates($payer['id']) : 0;
        $sum_contracts = $certificates->getSumContractes($payer['id']) ? $certificates->getSumContractes($payer['id']) : 0;

        $searchCertificates = new CertificatesPayersSearch();
        $CertificatesProvider = $searchCertificates->search(Yii::$app->request->queryParams);

        return $this->render('payer-certificates', [
            //'InformsProvider' => $InformsProvider,
            //'CooperateProvider' => $CooperateProvider,
            'payer_id' => $payer['id'],
            'CertificatesProvider' => $CertificatesProvider,
            'searchCertificates' => $searchCertificates,
        ]);
    }

    public function actionPayerContracts()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

       // $InformsProvider = new ActiveDataProvider([
       //     'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
       // ]);

        // $CooperateProvider = new ActiveDataProvider([
        //    'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
        // ]);
        
        /* $cooperate = (new \yii\db\Query())
            ->select(['organization_id'])
            ->from('cooperate')
            ->where(['status' => 1])
            ->andwhere(['payer_id'=> $payer['id']])
            ->all();
        
        if (empty($cooperate)) {
            $cooperate = 0;
        } */
        
        $searchContracts1 = new Payer1ContractsSearch();
        //$searchContracts1->organizations_id = $cooperate;
    //    $searchContracts1->contracts->payer_id = $payer['id'];
        //$searchContracts1->status = 1;
        if (isset($_GET['cert'])) { $searchContracts1->certificate_id = $_GET['cert']; }
        if (isset($_GET['org'])) { $searchContracts1->organization_id = $_GET['org']; }
        if (isset($_GET['prog'])) { $searchContracts1->program = $_GET['prog']; }
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);

        $searchContracts0 = new Payer0ContractsSearch();
        //$searchContracts0->organization_id = $cooperate;
    //    $searchContracts0->payer_id = $payer['id'];
        $Contracts0Provider = $searchContracts0->search(Yii::$app->request->queryParams);
        
         $searchContracts5 = new Payer4ContractsSearch();
       //  $searchContracts5->organization_id = $cooperate;
    //    $searchContracts5->payer_id = $payer['id'];
        $Contracts5Provider = $searchContracts5->search(Yii::$app->request->queryParams);
        
        $search3Contracts = new Payer3ContractsSearch();
     //   $search3Contracts->payer_id = $payer['id'];
        $Contracts3Provider = $search3Contracts->search(Yii::$app->request->queryParams); // Подтвержденые
        
        $searchContractsall = new ContractsPayerclearSearch();
        $ContractsallProvider = $searchContractsall->search(Yii::$app->request->queryParams);

        return $this->render('payer-contracts', [
           // 'InformsProvider' => $InformsProvider,
            //'CooperateProvider' => $CooperateProvider,
            'searchContracts1' => $searchContracts1,
            'Contracts1Provider' => $Contracts1Provider,
            'searchContracts0' => $searchContracts0,
            'Contracts0Provider' => $Contracts0Provider,
            'searchContracts5' => $searchContracts5,
            'Contracts5Provider' => $Contracts5Provider,
            'Contracts3Provider' => $Contracts3Provider,
            'search3Contracts' => $search3Contracts,
            'ContractsallProvider' => $ContractsallProvider,
        ]);
    }

    public function actionPayerInvoices()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

        //$InformsProvider = new ActiveDataProvider([
       //     'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
       /// ]);

      //  $CooperateProvider = new ActiveDataProvider([
      //      'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
      //  ]);

        
        $cooperate = (new \yii\db\Query())
            ->select(['organization_id'])
            ->from('cooperate')
            ->where(['status' => 1])
            ->andwhere(['payer_id'=> $payer['id']])
            ->column();
        
        if (empty($cooperate)) {
            $cooperate = 0;
        }
        
        $searchInvoices = new InvoicesPayerSearch();
        //$searchInvoices->organization_id = $cooperate;
        //$searchInvoices->payers_id = $payer['id'];
       // if (isset($_GET['org'])) { $searchInvoices->organization = $_GET['org']; }
        $InvoicesProvider = $searchInvoices->search(Yii::$app->request->queryParams);

        return $this->render('payer-invoices', [
         //   'InformsProvider' => $InformsProvider,
         //   'CooperateProvider' => $CooperateProvider,
            'searchInvoices' => $searchInvoices,
            'InvoicesProvider' => $InvoicesProvider,
        ]);
    }

    public function actionPayerOrganizations()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $InformsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
        ]);

        $CooperateProvider = new ActiveDataProvider([
            'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
        ]);

        $searchOrganization1 = new OrganizationmySearch();
        $Organization1Provider = $searchOrganization1->search(Yii::$app->request->queryParams);

        $searchOrganization0 = new OrganizationwaitSearch();
        $Organization0Provider = $searchOrganization0->search(Yii::$app->request->queryParams);

        return $this->render('payer-organizations', [
            'InformsProvider' => $InformsProvider,
            'CooperateProvider' => $CooperateProvider,
            'searchOrganization1' => $searchOrganization1,
            'Organization1Provider' => $Organization1Provider,
            'searchOrganization0' => $searchOrganization0,
            'Organization0Provider' => $Organization0Provider,
        ]);
    }

    public function actionPayerPrograms()
    {
        $payers = new Payers();
        $payer = $payers->getPayer();

        //$InformsProvider = new ActiveDataProvider([
        //    'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 2]),
        //]);

        //$CooperateProvider = new ActiveDataProvider([
        //    'query' => Cooperate::find()->where(['status'=> 0])->andWhere(['reade'=> 0])->andwhere(['payer_id' => $payer['id']]),
        //]);

        $cooperate = (new \yii\db\Query())
            ->select(['organization_id'])
            ->from('cooperate')
            ->where(['status' => 1])
            ->andwhere(['payer_id'=> $payer['id']])
            ->column();
        
        if (empty($cooperate)) {
            $cooperate = 0;
        }
        
        $searchPrograms = new ProgramsPayerSearch();
        //$searchPrograms->organization_id = $cooperate;
       // if (isset($_GET['org'])) { $searchPrograms->organization_id = $_GET['org']; }
        $ProgramsProvider = $searchPrograms->search(Yii::$app->request->queryParams);

        return $this->render('payer-programs', [
            //'InformsProvider' => $InformsProvider,
            //'CooperateProvider' => $CooperateProvider,
            'ProgramsProvider' => $ProgramsProvider,
            'searchPrograms' => $searchPrograms,
        ]);
    }
    
    public function actionOrganizationStatistic()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
        ]);

        $programs = new Programs();
        $count_programs = $programs->getCountPrograms($organization['id'], 2);
        $count_wait_programs = $programs->getCountPrograms($organization['id'], 0) + $programs->getCountPrograms($organization['id'], 1);

        $contracts = new Contracts();
        $contract_wait = $contracts->getCountWaitContracts($organization['id']);

        return $this->render('organization-statistic', [
            'informsProvider' => $informsProvider,
            'organization' => $organization,
            'count_programs' => $count_programs,
            'count_wait_programs' => $count_wait_programs,
            'contract_wait' => $contract_wait,
        ]);
    }

    public function actionOrganizationInfo()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
        ]);

        $model = Organization::findOne($organization['id']);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['/personal/organization-info']);
        }

        return $this->render('organization-info', [
            'informsProvider' => $informsProvider,
            'organization' => $organization,
            'model' => $model,
        ]);
    }

    public function actionOrganizationFavorites()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
        ]);

        $searchFavorites = new PreviusSearch();
        $searchFavorites->organization_id = $organization['id'];
        if (isset($_GET['year'])) { $searchFavorites->year_id = $_GET['year']; }
        if (isset($_GET['program'])) { $searchFavorites->program_id = $_GET['program']; }
        $FavoritesProvider = $searchFavorites->search(Yii::$app->request->queryParams);

        return $this->render('organization-favorites', [
            'informsProvider' => $informsProvider,
            'searchFavorites' => $searchFavorites,
            'FavoritesProvider' => $FavoritesProvider,
        ]);
    }

    public function actionOrganizationPrograms()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
        ]);
        
        //$searchYears0 = new YearsWaitSearch();
        //$Years0Provider = $searchYears0->search(Yii::$app->request->queryParams);
        
        $searchYears1 = new YearsCertSearch();
        $Years1Provider = $searchYears1->search(Yii::$app->request->queryParams);
        
       // $searchYears2 = new YearsNoSearch();
       // $Years2Provider = $searchYears2->search(Yii::$app->request->queryParams);
        

        $searchPrograms0 = new ProgramsSearch();
        $Programs0Provider = $searchPrograms0->search(Yii::$app->request->queryParams);

        $searchPrograms1 = new ProgramscertSearch();
        $Programs1Provider = $searchPrograms1->search(Yii::$app->request->queryParams);
        
        $searchPrograms2 = new ProgramsotkSearch();
        $Programs2Provider = $searchPrograms2->search(Yii::$app->request->queryParams); 

        return $this->render('organization-programs', [
            'informsProvider' => $informsProvider,
            'searchPrograms0' => $searchPrograms0,
            'Programs0Provider' => $Programs0Provider,
            //'searchYears0' => $searchYears0,
            //'Years0Provider' => $Years0Provider,
            'searchYears1' => $searchYears1,
            'Years1Provider' => $Years1Provider,
            //'searchYears2' => $searchYears2,
            //'Years2Provider' => $Years2Provider,
            'searchPrograms1' => $searchPrograms1,
            'Programs1Provider' => $Programs1Provider,
            'searchPrograms2' => $searchPrograms2,
            'Programs2Provider' => $Programs2Provider,
        ]);
    }

    public function actionOrganizationContracts()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
        ]);

        $searchContracts1 = new ContractsoSearch();
        $searchContracts1->organization = $organization['name'];
        if (isset($_GET['prog'])) { $searchContracts1->program = $_GET['prog']; }
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams); // Действующий

        $searchContracts0 = new ContractsnSearch();
        $searchContracts0->organization_id = $organization['id'];
        $Contracts0Provider = $searchContracts0->search(Yii::$app->request->queryParams); // Ожидающие подтверждения
        
        $search3Contracts = new Contracts3Search();
        $search3Contracts->organization_id = $organization['id'];
        $Contracts3Provider = $search3Contracts->search(Yii::$app->request->queryParams); // Подтвержденые
        
        $searchContracts4 = new Contracts4Search();
        $searchContracts4->organization_id = $organization['id'];
        $Contracts4Provider = $searchContracts4->search(Yii::$app->request->queryParams); // Заканчивающий действие
        
        $searchContracts5 = new Contracts5Search();
        $searchContracts5->organization_id = $organization['id'];
        $Contracts5Provider = $searchContracts5->search(Yii::$app->request->queryParams); // Расторгнутый
        
        $searchContractsall = new ContractsOrgclearSearch();
        $ContractsallProvider = $searchContractsall->search(Yii::$app->request->queryParams);

        return $this->render('organization-contracts', [
            'informsProvider' => $informsProvider,
            'searchContracts1' => $searchContracts1,
            'Contracts1Provider' => $Contracts1Provider,
            'searchContracts0' => $searchContracts0,
            'Contracts0Provider' => $Contracts0Provider,
            'search3Contracts' => $search3Contracts,
            'Contracts3Provider' => $Contracts3Provider,
            'searchContracts4' => $searchContracts4,
            'Contracts4Provider' => $Contracts4Provider,
            'searchContracts5' => $searchContracts5,
            'Contracts5Provider' => $Contracts5Provider,
            'ContractsallProvider' => $ContractsallProvider,
        ]);
    }

    public function actionOrganizationInvoices()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

      //  $informsProvider = new ActiveDataProvider([
     //       'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
      //  ]);

        $searchInvoices = new InvoicesOrgSearch();
      //  $searchInvoices->organization_id = $organization['id'];
        $InvoicesProvider = $searchInvoices->search(Yii::$app->request->queryParams);

        return $this->render('organization-invoices', [
            //'informsProvider' => $informsProvider,
            'searchInvoices' => $searchInvoices,
            'InvoicesProvider' => $InvoicesProvider,
        ]);
    }

    public function actionOrganizationPayers()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

//        $informsProvider = new ActiveDataProvider([
  //          'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
    //    ]);

        $searchPayers = new PayersmySearch();
        $PayersProvider = $searchPayers->search(Yii::$app->request->queryParams);

        $searchPayersWait = new PayersWaitSearch();
        $PayersWaitProvider = $searchPayersWait->search(Yii::$app->request->queryParams);

        return $this->render('organization-payers', [
            //'informsProvider' => $informsProvider,
            'searchPayers' => $searchPayers,
            'PayersProvider' => $PayersProvider,
            'searchPayersWait' => $searchPayersWait,
            'PayersWaitProvider' => $PayersWaitProvider,
        ]);
    }

    public function actionOrganizationGroups()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 3])->andwhere(['prof_id'=> $organization['id']]),
        ]);

        $searchGroups = new GroupsSearch();
        $searchGroups->organization_id = $organization['id'];
        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('organization-groups', [
            'informsProvider' => $informsProvider,
            'searchGroups' => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);
    }

    public function actionCertificateStatistic()
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();
        
        $model = new Programs();

        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(['/programs/search', 'name' => $model->search]);
            //return var_dump($model);
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
        
        $searchPrev= new PreviusSearch();
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
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 4])->andwhere(['prof_id'=> $certificate['id']]),
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

        //return var_dump($certificate);
        //$informsProvider = new ActiveDataProvider([
        //    'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 4])->andwhere(['prof_id'=> $certificate['id']]),
        //]);

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

        //return var_dump($certificate);
        //$informsProvider = new ActiveDataProvider([
        //    'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 4])->andwhere(['prof_id'=> $certificate['id']]),
        //]);

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
        
       /* $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        $informsProvider = new ActiveDataProvider([
            'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 4])->andwhere(['prof_id'=> $certificate['id']]),
        ]);

        $programcertModel = new ProgramsfromcertSearch();
        $programcertProvider = $programcertModel->search(Yii::$app->request->queryParams);

        $programnocertModel = new ProgramsfromnocertSearch();
        $programnocertProvider = $programnocertModel->search(Yii::$app->request->queryParams);

        return $this->render('certificate-programs', [
            'informsProvider' => $informsProvider,
            'programcertModel' => $programcertModel,
            'programcertProvider' => $programcertProvider,
            'programnocertModel' => $programnocertModel,
            'programnocertProvider' => $programnocertProvider,
        ]); */
    }

    
    
    public function actionCertificatePrevius()
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();
        
        $searchPrev= new PreviusSearch();
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

        // $informsProvider = new ActiveDataProvider([
       //     'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 4])->andwhere(['prof_id'=> $certificate['id']]),
       // ]);

       // $searchContracts0 = new ContractsnSearch();
       // $Contracts0Provider = $searchContracts0->search(Yii::$app->request->queryParams);

        $searchContracts1 = new ContractsoSearch();
        $searchContracts1->certificate_id = $certificate['id'];
        $Contracts1Provider = $searchContracts1->search(Yii::$app->request->queryParams);
        
       //$searchNoContracts = new Contracts2Search();
       // $ContractsNoProvider = $searchNoContracts->search(Yii::$app->request->queryParams);
        
       // $search3Contracts = new Contracts3Search();
       // $Contracts3Provider = $search3Contracts->search(Yii::$app->request->queryParams);

        return $this->render('certificate-contracts', [
           // 'informsProvider' => $informsProvider,
            'searchContracts1' => $searchContracts1,
            'Contracts1Provider' => $Contracts1Provider,
           // 'searchContracts0' => $searchContracts0,
            //'Contracts0Provider' => $Contracts0Provider,
           // 'searchNoContracts' => $searchNoContracts,
           // 'ContractsNoProvider' => $ContractsNoProvider,
           // 'search3Contracts' => $search3Contracts,
           // 'Contracts3Provider' => $Contracts3Provider,
        ]);
    }

    public function actionCertificateArchive()
    {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        // $informsProvider = new ActiveDataProvider([
        //    'query' => Informs::find()->where(['read'=> 0])->andwhere(['from'=> 4])->andwhere(['prof_id'=> $certificate['id']]),
        // ]);

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
