<?php

namespace app\controllers;

use Yii;
use app\models\Contracts;
use app\models\User;
use app\models\ContractsSearch;
use app\models\ContractsoSearch;
use app\models\ContractsInvoiceSearch;
use app\models\ContractsDecInvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Informs;
use app\models\Programs;
use app\models\Certificates;
use app\models\Organization;
use app\models\Favorites;
use app\models\Years;
use app\models\Groups;
use app\models\GroupsSearch;
use app\models\Payers;
use mPDF;
use yii\helpers\Json;
use app\models\ContractspreInvoiceSearch;
use app\models\Completeness;

/**
 * ContractsController implements the CRUD actions for Contracts model.
 */
class ContractsController extends Controller
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
     * Lists all Contracts models.
     * @return mixed
     */
   /* public function actionIndex()
    {
        $searchModel = new ContractsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    } */

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

    /**
     * Creates a new Contracts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Contracts();
        $certificate = Certificates::findOne($id);
        
        $model->certificate_id = $certificate->id;
        $model->payer_id = $certificate->payer_id;
         

        if ($model->load(Yii::$app->request->post())) {
            
            if (empty($model->group_id) or !isset($model->group_id)) {
                Yii::$app->session->setFlash('error', 'Не выбрана группа');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
            if (empty($model->year_id) or !isset($model->year_id)) {
                Yii::$app->session->setFlash('error', 'Не выбран год');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
            $groups = new Groups();
            $group = $groups->getGroup($model->group_id);

            $model->start_edu_contract = $group['datestart'];
         
            $year = Years::findOne($group['year_id']);
         
            $date_elements_start  = explode("-", $group['datestart']);
        $date_elements_stop  = explode("-", $group['datestop']);
        $date_elements_user  = explode("-", $model->start_edu_contract); 
        
        $prodolj_d = (intval(abs(strtotime($group['datestart']) - strtotime($group['datestop']))) / (3600 * 24)) + 1;  // поменять на кол-во дней
        
        $prodolj_d_user = (intval(abs(strtotime($model->start_edu_contract) - strtotime($group['datestop']))) / (3600 * 24)) + 1;  // поменять на кол-во дней
        
        //return $prodolj_d_user;
        
         if ($date_elements_stop[0] > $date_elements_start[0]) {
            $prodolj_m = ($date_elements_stop[1] + 13) - $date_elements_start[1];
        }
        else {
            $prodolj_m = ($date_elements_stop[1] + 1) - $date_elements_start[1];
        }
        
        if ($date_elements_stop[0] > $date_elements_user[0]) {
            $prodolj_m_user = ($date_elements_stop[1] + 13) - $date_elements_user[1];
        }
        else {
            $prodolj_m_user = ($date_elements_stop[1] + 1) - $date_elements_user[1];
        }
        
        //$first_m_day = cal_days_in_month(CAL_GREGORIAN, $date_elements_user[1], $date_elements_user[0]);
        
        $first_m_day = cal_days_in_month(CAL_GREGORIAN, $date_elements_start[1], $date_elements_start[0]);
        
        //$teach_day = $first_m_day - $date_elements_user[2] + 1;
        
        $teach_day = $first_m_day - $date_elements_start[2] + 1;
        
            $year = Years::findOne($group['year_id']);
        
            $first_m_price = $teach_day / $prodolj_d * $year['price'];
            $other_m_price = ($year['price'] - $first_m_price) / ($prodolj_m - 1);

            $first_m_nprice = $teach_day / $prodolj_d * $year['normative_price'];
            $other_m_nprice = ($year['normative_price'] - $first_m_nprice) / ($prodolj_m - 1);

            if ($prodolj_m == $prodolj_m_user) {
                $userprice = $year['price'];
                $nuserprice = $year['normative_price'];
            }
            else {
                $userprice = $prodolj_m_user * $other_m_price;
                $nuserprice = $prodolj_m_user * $other_m_nprice;
            }
        
            if ($userprice <= $nuserprice) {
                if ($userprice <= $certificate->balance) {
                    $pay = "Полная стоимость";
                    $dop = "отсутствует";
                } else {
                    $pay = $certificate->balance;
                    $dop = $userprice - $certificate->balance;
                }
            } else {
                if ($nuserprice <= $certificate->balance) {
                    $pay = $nuserprice;
                    $dop = $userprice - $nuserprice;
                } else {
                    $pay = $certificate->balance;
                    $dop = $userprice - $certificate->balance;
                }
            }

            $ost = $certificate->balance - $pay;

            $display['balance'] = round($certificate->balance, 2);
            $display['userprice'] = round($userprice, 2);
            $display['pay'] = round($pay, 2);
            $display['dop'] = round($dop, 2);
            $display['ost'] = round($ost, 2);

             $model->prodolj_d = $prodolj_d;
             $model->prodolj_m = $prodolj_m;
             $model->prodolj_m_user = $prodolj_m_user;


             $cert_dol = $dop / $year['price'];
            $payer_dol = $pay / $year['price']; 
            $model->cert_dol = $cert_dol;
            $model->payer_dol = $payer_dol;


             $model->first_m_nprice = $first_m_nprice;
             $model->other_m_nprice = $other_m_nprice; 

             $model->all_funds = round($userprice, 2);
             $model->funds_cert = round($pay, 2);
             $model->all_parents_funds  = round($dop, 2);
         
            
            $model->organization_id = $group['organization_id'];
            $model->year_id = $group['year_id'];
            $model->program_id = $group['program_id'];
            
            if ($date_elements_stop[1] == date('m')) {
               $model->wait_termnate = 1;
            }

            

            //$model->date = date("Y-m-d");

            if ($model->save()) {
                Yii::$app->session->setFlash('param1', $model->id);
                return $this->redirect(['/contracts/complete']);
            }

            
            /*
            $organizations = new Organization();
            $organization = $organizations->getOrganization();
            $model->organization_id = $organization['id'];
            $model->status = 0;

            if ($model->validate() && $model->save()) {
                $informs = new Informs();
                $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $model->certificate_id;
                $informs->text = 'Вас записали на программу';
                $informs->from = 4;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {

                    $programs = new Programs();
                    $program = $programs->getPrograms($model->program_id);

                    $inform = new Informs();
                    $inform->program_id = $model->program_id;
                    $inform->contract_id = $model->id;
                    $inform->prof_id = $program['payer_id'];
                    $inform->text = 'Поступила заявка на обучение';
                    $inform->from = 2;
                    $inform->date = date("Y-m-d");
                    $inform->read = 0;

                    if ($inform->save()) {
                        return $this->redirect(['/personal/organization#panel4']);
                    }
                }
            }
            */
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionGroup($id)
    {
        $model = new Contracts();
        
        $searchGroups = new GroupsSearch();
        $searchGroups->year_id = $id;
        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);
        
        $rows = (new \yii\db\Query())
            ->select(['program_id'])
            ->from('years')
            ->where(['id' => $id])
            ->one();
        
        $program = Programs::findOne($rows['program_id']);
        $year = Years::findOne($id);
            
         return $this->render('/contracts/groups', [
            'GroupsProvider' => $GroupsProvider,
             'program' => $program,
             'year' => $year,
        ]);
    }


    public function actionNewgroup($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())) {
            $model->save();
            return $this->redirect(['/groups/contracts', 'id' => $model->group_id]);
        }

        return $this->render('newgroup', [
            'model' => $model,
        ]);

    }
    
     public function actionNew($id)
    {
        $model = new Contracts();
         
         
         //$certificate = Certificates::findOne($cert);
         
        $groups = new Groups();
        $group = $groups->getGroup($id);
         
        //$model->start_edu_contract = $group['datestart'];
         $start_edu_contract = explode('-', $group['datestart']);
         
        $model->month_start_edu_contract = $start_edu_contract[1].'-'.$start_edu_contract[0];
         
        $model->program_id = $group['program_id'];
        
         
        if ($model->load(Yii::$app->request->post())) {
            
            $month_start_edu_contract = explode('-', $model->month_start_edu_contract);
            
            if ($start_edu_contract[1] == $month_start_edu_contract[0]) {
                $model->start_edu_contract = $group['datestart'];
            } else {
            $model->start_edu_contract = $month_start_edu_contract[1].'-'.$month_start_edu_contract[0].'-01';
            }
            //return $model->start_edu_contract;

            if ($model->start_edu_contract < $group['datestart'] or $model->start_edu_contract > $group['datestop']) {
                return $this->render('/contracts/new', [
                'model' => $model,
                'error' => 'Неправильная дата. Дата начала - '. $group['datestart'].', дата окончания - '.$group['datestop'],
                ]);
            }

            
            $model->group_id = $id;
            
            $model->organization_id = $group['organization_id'];
            $model->year_id = $group['year_id'];
            
            $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            if (isset($roles['certificate'])) {
            
                $certificates = new Certificates();
                $certificate = $certificates->getCertificates();
            
                $model->certificate_id = $certificate->id;
                $model->payer_id = $certificate->payer_id;
            } else {
                
                $certificate = Certificates::findOne(Yii::$app->session->getFlash('param2'));
                
                $model->certificate_id = $certificate->id;
                $model->payer_id = $certificate->payer_id;
            }

            //$model->date = date("Y-m-d");

            if ($model->save()) {
                Yii::$app->session->setFlash('param1', $model->id);
                return $this->redirect(['/contracts/complete']);
            } 
        }
         return $this->render('/contracts/new', [
            'model' => $model,
        ]);
    }
    
    
    
    public function actionComplete()
    {
        
        $model = Contracts::findOne(Yii::$app->session->getFlash('param1'));
        
        $duble = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['certificate_id' => $model->certificate_id])
                ->andWhere(['payer_id' => $model->payer_id])
                ->andWhere(['program_id' => $model->program_id])
                ->andWhere(['year_id' => $model->year_id])
                ->andWhere(['organization_id' => $model->organization_id])
                ->andWhere(['group_id' => $model->group_id])
                ->andWhere(['status' => [0,1,3]])
                ->count();
        
         if ($duble > 0) {
           Yii::$app->session->setFlash('error', 'Вы уже подали заявку на программу/заключили договор на обучение.');
                 return $this->redirect(['/programs/view', 'id' => $model->program_id]);
        }
        
        //$certificates = new Certificates();
        //$certificate = $certificates->getCertificates();
        
        $certificate = Certificates::findOne($model->certificate_id);
           
        $group = Groups::findOne($model->group_id);

        $date_elements_start  = explode("-", $group['datestart']);
        $date_elements_stop  = explode("-", $group['datestop']);
        $date_elements_user  = explode("-", $model->start_edu_contract); 
        
        $prodolj_d = (intval(abs(strtotime($group['datestart']) - strtotime($group['datestop']))) / (3600 * 24)) + 1;  // поменять на кол-во дней
        
        $prodolj_d_user = (intval(abs(strtotime($model->start_edu_contract) - strtotime($group['datestop']))) / (3600 * 24)) + 1;  // поменять на кол-во дней
        
        //return $prodolj_d_user;
        
        if ($date_elements_stop[0] > $date_elements_start[0]) {
            $prodolj_m = ($date_elements_stop[1] + 13) - $date_elements_start[1];
        }
        else {
            $prodolj_m = ($date_elements_stop[1] + 1) - $date_elements_start[1];
        }
        
        if ($date_elements_stop[0] > $date_elements_user[0]) {
            $prodolj_m_user = ($date_elements_stop[1] + 13) - $date_elements_user[1];
        }
        else {
            $prodolj_m_user = ($date_elements_stop[1] + 1) - $date_elements_user[1];
        }
        
        //$first_m_day = cal_days_in_month(CAL_GREGORIAN, $date_elements_user[1], $date_elements_user[0]);
        
        $first_m_day = cal_days_in_month(CAL_GREGORIAN, $date_elements_start[1], $date_elements_start[0]);
        
        //$teach_day = $first_m_day - $date_elements_user[2] + 1;
        
        $teach_day = $first_m_day - $date_elements_start[2] + 1;
        
        //return $teach_day;
        
       // return $first_m_day;
        $year = Years::findOne($group['year_id']);
        
        $first_m_price = $teach_day / $prodolj_d * $year['price'];
       
        
        if ($prodolj_m > 1) {
             $other_m_price = ($year['price'] - $first_m_price) / ($prodolj_m - 1);
        }
        else {
             $other_m_price = 0;
         }
         
        $first_m_nprice = $teach_day / $prodolj_d * $year['normative_price'];
        
        
        if ($prodolj_m > 1) {
            $other_m_nprice = ($year['normative_price'] - $first_m_nprice) / ($prodolj_m - 1);
        }
         else {
             $other_m_nprice = 0;
         }
        
        if ($prodolj_m == $prodolj_m_user) {
            $userprice = $year['price'];
            $nuserprice = $year['normative_price'];
        }
        else {
            //$userprice = $prodolj_m_user * $other_m_price;
            
            $userprice = ($prodolj_d_user /  $prodolj_d) * $year['price'];
            
            $nuserprice = ($prodolj_d_user /  $prodolj_d) * $year['normative_price'];
        }
        
        if ($userprice <= $nuserprice) {
            if ($userprice <= $certificate->balance) {
                $pay = $userprice;
                $dop = 0;
            } else {
                $pay = $certificate->balance;
                $dop = $userprice - $certificate->balance;
            }
        } else {
            if ($nuserprice <= $certificate->balance) {
                $pay = $nuserprice;
                $dop = $userprice - $nuserprice;
            } else {
                $pay = $certificate->balance;
                $dop = $userprice - $certificate->balance;
            }
        }
        
        $ost = $certificate->balance - $pay;
        
        $display['balance'] = round($certificate->balance, 2);
        $display['userprice'] = round($userprice, 2);
        $display['pay'] = round($pay, 2);
        $display['dop'] = round($dop, 2);
        $display['ost'] = round($ost, 2);  
        
        if ($prodolj_m == $prodolj_m_user) {
            $model->prodolj_d = $prodolj_d;
             $model->prodolj_m = $prodolj_m;
             $model->prodolj_m_user = $prodolj_m_user;
        }
        else {
            $model->prodolj_d = $prodolj_d_user;
             $model->prodolj_m = $prodolj_m;
             $model->prodolj_m_user = $prodolj_m_user;
        }
        
        //return var_dump($year['price']);
        
       // $cert_dol = $dop / $year['price'];
       // $payer_dol = $pay / $year['price']; 
        
        $cert_dol = $dop / $userprice;
        $payer_dol = $pay / $userprice; 
        
        $model->cert_dol = $cert_dol;
        $model->payer_dol = $payer_dol;
        
        
        if ($prodolj_m == $prodolj_m_user) {
            if ($date_elements_start[0] == $date_elements_stop[0]) {
                 $model->first_m_price = round($userprice, 2);
            }
            else {
                 //$model->first_m_price = $first_m_price;
                $model->first_m_price = round($userprice, 2) - ($prodolj_m_user - 1) * round($other_m_price, 2);
            }
            
            $model->first_m_nprice = round($first_m_nprice, 2);
            
            $model->other_m_price = round($other_m_price, 2);
         $model->other_m_nprice = round($other_m_nprice, 2);
        }
        else {
           // $model->first_m_price = $other_m_price;
           // $model->first_m_nprice = $other_m_price;
            
            //$model->first_m_price = $userprice / $prodolj_m_user;
             
            $model->other_m_price = round($userprice / $prodolj_m_user, 2);
            $model->first_m_price = round($userprice, 2) - ($prodolj_m_user - 1) * round($other_m_price, 2);
            
            $model->first_m_nprice = round($nuserprice / $prodolj_m_user, 2);
            
           
         $model->other_m_nprice = round($nuserprice / $prodolj_m_user, 2);
            
        }
        
         
        
        $model->all_funds = round($userprice, 2);
        $model->funds_cert = round($pay, 2);
        $model->all_parents_funds  = round($dop, 2);
        
        $display['cert_dol'] = $cert_dol;
        $display['payer_dol'] = $payer_dol;
        $display['first_m_price'] = $model->first_m_price;
        $display['other_m_price'] = $model->other_m_price; 
        
        if ($model->load(Yii::$app->request->post())) {            
            
            $duble = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['certificate_id' => $model->certificate_id])
                ->andWhere(['payer_id' => $model->payer_id])
                ->andWhere(['program_id' => $model->program_id])
                ->andWhere(['year_id' => $model->year_id])
                ->andWhere(['organization_id' => $model->organization_id])
                ->andWhere(['group_id' => $model->group_id])
                ->andWhere(['status' => [0,1,3]])
                ->count();
        
             if ($duble > 0) {
                Yii::$app->session->setFlash('error', 'Вы уже подали заявку на программу/заключили договор на обучение.');
                 return $this->redirect(['/programs/view', 'id' => $model->program_id]);
            }

            if ($model->validate() && $model->save()) {
                return $this->redirect(['/contracts/complete', 'id' => $model->id]);
            }
        }
        return $this->render('/contracts/new', [
            'model' => $model,
             'display' => $display,
        ]);
    }
    
    public function actionBack($id)
    {
        $model = $this->findModel($id);
        $group = $model->group_id;
        $cert = $model->certificate_id;
        $model->delete();
        Yii::$app->session->setFlash('param2', $cert);
        return $this->redirect(['/contracts/new', 'id' => $group]);
        
    }
    
    public function actionCancel($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['/programs/index']);
        
    }
    
    public function actionGood($id)
    {
        $model = Contracts::findOne($id);
        
        $duble = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['certificate_id' => $model->certificate_id])
                ->andWhere(['payer_id' => $model->payer_id])
                ->andWhere(['program_id' => $model->program_id])
                ->andWhere(['year_id' => $model->year_id])
                ->andWhere(['organization_id' => $model->organization_id])
                ->andWhere(['group_id' => $model->group_id])
                ->andWhere(['status' => [0,1,3]])
                ->count();
        
         if ($duble > 0) {
           Yii::$app->session->setFlash('error', 'Вы уже подали заявку на программу/заключили договор на обучение.');
                 return $this->redirect(['/programs/view', 'id' => $model->program_id]);
        }
        
        //$certificates = new Certificates();
        //$certificate = $certificates->getCertificates();
        
         $certificate = Certificates::findOne($model->certificate_id);
           
        $group = Groups::findOne($model->group_id);

        $date_elements_start  = explode("-", $group['datestart']);
        $date_elements_stop  = explode("-", $group['datestop']);
        $date_elements_user  = explode("-", $model->start_edu_contract); 
        
        $prodolj_d = (intval(abs(strtotime($group['datestart']) - strtotime($group['datestop']))) / (3600 * 24)) + 1;  // поменять на кол-во дней
        
        $prodolj_d_user = (intval(abs(strtotime($model->start_edu_contract) - strtotime($group['datestop']))) / (3600 * 24)) + 1;  // поменять на кол-во дней
        
        //return $prodolj_d_user;
        
         if ($date_elements_stop[0] > $date_elements_start[0]) {
            $prodolj_m = ($date_elements_stop[1] + 13) - $date_elements_start[1];
        }
        else {
            $prodolj_m = ($date_elements_stop[1] + 1) - $date_elements_start[1];
        }
        
        if ($date_elements_stop[0] > $date_elements_user[0]) {
            $prodolj_m_user = ($date_elements_stop[1] + 13) - $date_elements_user[1];
        }
        else {
            $prodolj_m_user = ($date_elements_stop[1] + 1) - $date_elements_user[1];
        }
        
        //$first_m_day = cal_days_in_month(CAL_GREGORIAN, $date_elements_user[1], $date_elements_user[0]);
        
        $first_m_day = cal_days_in_month(CAL_GREGORIAN, $date_elements_start[1], $date_elements_start[0]);
        
        //$teach_day = $first_m_day - $date_elements_user[2] + 1;
        
        $teach_day = $first_m_day - $date_elements_start[2] + 1;
        
        //return $teach_day;
        
       // return $first_m_day;
        $year = Years::findOne($group['year_id']);
        
        $first_m_price = $teach_day / $prodolj_d * $year['price'];
       
        
        if ($prodolj_m > 1) {
             $other_m_price = ($year['price'] - $first_m_price) / ($prodolj_m - 1);
        }
        else {
             $other_m_price = 0;
         }
         
        $first_m_nprice = $teach_day / $prodolj_d * $year['normative_price'];
        
        
        if ($prodolj_m > 1) {
            $other_m_nprice = ($year['normative_price'] - $first_m_nprice) / ($prodolj_m - 1);
        }
         else {
             $other_m_nprice = 0;
         }
        
        if ($prodolj_m == $prodolj_m_user) {
            $userprice = $year['price'];
            $nuserprice = $year['normative_price'];
        }
        else {
            //$userprice = $prodolj_m_user * $other_m_price;
            
            $userprice = ($prodolj_d_user /  $prodolj_d) * $year['price'];
            
            $nuserprice = ($prodolj_d_user /  $prodolj_d) * $year['normative_price'];
        }
        
        if ($userprice <= $nuserprice) {
            if ($userprice <= $certificate->balance) {
                $pay = $userprice;
                $dop = 0;
            } else {
                $pay = $certificate->balance;
                $dop = $userprice - $certificate->balance;
            }
        } else {
            if ($nuserprice <= $certificate->balance) {
                $pay = $nuserprice;
                $dop = $userprice - $nuserprice;
            } else {
                $pay = $certificate->balance;
                $dop = $userprice - $certificate->balance;
            }
        }
        
        $ost = $certificate->balance - $pay;
        
        $display['balance'] = round($certificate->balance, 2);
        $display['userprice'] = round($userprice, 2);
        $display['pay'] = round($pay, 2);
        $display['dop'] = round($dop, 2);
        $display['ost'] = round($ost, 2);  
        
       if ($prodolj_m == $prodolj_m_user) {
            $model->prodolj_d = $prodolj_d;
             $model->prodolj_m = $prodolj_m;
             $model->prodolj_m_user = $prodolj_m_user;
        }
        else {
            $model->prodolj_d = $prodolj_d_user;
             $model->prodolj_m = $prodolj_m;
             $model->prodolj_m_user = $prodolj_m_user;
        }
        
        //return var_dump($year['price']);
        
       // $cert_dol = $dop / $year['price'];
       // $payer_dol = $pay / $year['price']; 
        
        $cert_dol = $dop / $userprice;
        $payer_dol = $pay / $userprice; 
        
        $model->cert_dol = $cert_dol;
        $model->payer_dol = $payer_dol;
        
        
        if ($prodolj_m == $prodolj_m_user) {
            if ($date_elements_start[0] == $date_elements_stop[0]) {
                 $model->first_m_price = round($userprice, 2);
            }
            else {
                 $model->first_m_price = $first_m_price;
            }
            $model->first_m_nprice = $first_m_nprice;
            
            $model->other_m_price = $other_m_price;
         $model->other_m_nprice = $other_m_nprice;
        }
        else {
           // $model->first_m_price = $other_m_price;
           // $model->first_m_nprice = $other_m_price;
            $model->first_m_price = $userprice / $prodolj_m_user;
            $model->first_m_nprice = $nuserprice / $prodolj_m_user;
            
            $model->other_m_price = $userprice / $prodolj_m_user;
         $model->other_m_nprice = $nuserprice / $prodolj_m_user;
            
        }
        
        $model->stop_edu_contract = $group['datestop'];
        
         //return var_dump($model->cert_dol);
        
         
        $model->all_funds = round($userprice, 2);
        $model->funds_cert = round($pay, 2);
        $model->all_parents_funds  = round($dop, 2);
        $model->rezerv = round($pay, 2);
        
       // $org = Organization::findOne($model->organization_id);
       // return var_dump($org);
        //$org->amount_child = $org->amount_child + 1;
        //$org ->save();
        
         $model->status = 0;

            if ($model->validate() && $model->save()) {
                
                $cert = Certificates::findOne($certificate->id);
            
                $cert->balance = round($ost, 2);

                $cert->rezerv = $cert->rezerv + round($pay, 2);

                $cert->save();
                
                $informs = new Informs();
                $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $model->organization_id;
                $informs->text = 'Поступила заявка на обучение';
                $informs->from = 3;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {
                    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
                    if (isset($roles['organizations'])) {
                        return $this->redirect('/personal/organization-contracts');
                    }
                    if (isset($roles['certificate'])) {
                        return $this->redirect('/personal/certificate-wait-request');
                    }
                }
            }
    }
    
    public function actionVerificate($id)
    {
        $model = $this->findModel($id);
        
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
    
    
    
    public function actionSave($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
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
                
                $date  = explode("-", $model->date);
                $cdate  = explode("-", $cont->date);
                
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
            
            $cert->rezerv = $cert->rezerv - ($model->first_m_price * $model->payer_dol);
            $cert->save();
            
            $model->paid = $model->first_m_price * $model->payer_dol;
            $model->rezerv = $model->rezerv - ($model->first_m_price * $model->payer_dol);
            
            $model->status = 1;
            
            if ($model->save()) {
                $completeness = new Completeness();
                $completeness->group_id = $model->group_id;
                $completeness->contract_id = $model->id;
                
                $start_edu_contract  = explode("-", $model->start_edu_contract);
                
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
                            $price = $model->first_m_price * $model->payer_dol;
                        } else {
                            $price = $model->other_m_price * $model->payer_dol;
                        }
                    } else { 
                        if ($month == date('m')-1) {
                            $price = $model->first_m_price * $model->payer_dol;
                        } else {
                            $price = $model->other_m_price * $model->payer_dol;
                        }
                    }
                
                $completeness->sum = ($price * $completeness->completeness) / 100;  
                
                
                 if (date('m') != 1) {
                    $completeness->save();
                } 
                
                $preinvoice = new Completeness();
                $preinvoice->group_id = $model->group_id;
                $preinvoice->contract_id = $model->id;
                $preinvoice->month = date('m');
                $preinvoice->year =  $start_edu_contract[0];
                $preinvoice->preinvoice = 1;
                $preinvoice->completeness = 80;

                $start_edu_contract  = explode("-", $model->start_edu_contract);
                $month = $start_edu_contract[1];

                        if ($month == date('m')) {
                            $price = $model->first_m_price * $model->payer_dol;
                        } else {
                            $price = $model->other_m_price * $model->payer_dol;
                        }

                $preinvoice->sum = ($price * $preinvoice->completeness) / 100;


                if ($preinvoice->save()) {
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
        return $this->render('/contracts/save', [
            'model' => $model,
        ]);
    }
    
    public function actionGenerate($id)
    {
        $model = $this->findModel($id);
        
        $organizations = new Organization();
        $organization = $organizations->getOrganization();
        
        if ($model->load(Yii::$app->request->post())) { 
            $model->org_position = $organization->position;
            $model->org_position_min = $organization->position_min;
            
            if ($model->save()) {
                return $this->redirect(['/contracts/preview', 'id' => $model->id]);
            }
            
        }
        
        return $this->render('/contracts/generate', [
            'model' => $model,
            'organization' => $organization,
        ]);
    }
    
    public function actionPreview($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('/contracts/preview', [
            'model' => $model,
        ]);
    }

    public function actionOk($id)
    {
        $model = $this->findModel($id);
        
        //if ($model->load(Yii::$app->request->post())) {        
            
            $model->status = 3;
            if ($model->save()) {
                $cert = Certificates::findOne($model->certificate_id);
                
                $informs = new Informs();
                $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $cert->payer_id;
                $informs->text = 'Подтверждено создание договора';
                $informs->from = 2;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {
                    $inform = new Informs();
                    $inform->program_id = $model->program_id;
                    $inform->contract_id = $model->id;
                    $inform->prof_id = $model->certificate_id;
                    $inform->text = 'Подтверждено создание договора';
                    $inform->from = 4;
                    $inform->date = date("Y-m-d");
                    $inform->read = 0;

                    if ($inform->save()) {
                        return $this->redirect('/personal/organization-contracts#panel2');
                    }
                }
            }
       // }
       // return $this->render('/contracts/make', [
       //     'model' => $model,
       // ]);
    }
    
    public function actionNo($id)
    {
        $model = $this->findModel($id);
        $informs = new Informs();
        
        if ($informs->load(Yii::$app->request->post())) {

             $cert = Certificates::findOne($model->certificate_id);
            $cert->balance = $cert->balance + $model->rezerv;
            $cert->rezerv = $cert->rezerv - $model->rezerv;
            $cert->save();
            
            $model->rezerv = 0;
            $model->status = 2;
            
            if ($model->save()) {
                $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $model->organization_id;
                $informs->text = 'Отказано в записи. Причина: '.$informs->dop;
                $informs->from = 3;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {
                    $inform = new Informs();
                    $inform->program_id = $model->program_id;
                    $inform->contract_id = $model->id;
                    $inform->prof_id = $model->certificate_id;
                    $inform->text = 'Отказано в записи. Причина: '.$informs->dop;
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
       // $informs = new Informs();
        
        //if ($informs->load(Yii::$app->request->post())) {

             $cert = Certificates::findOne($model->certificate_id);
            $cert->balance = $cert->balance + $model->rezerv;
            $cert->rezerv = $cert->rezerv - $model->rezerv;
            $cert->save();
            
            $model->rezerv = 0;
            $model->status = 2;
            
            if ($model->save()) {
               /* $informs->program_id = $model->program_id;
                $informs->contract_id = $model->id;
                $informs->prof_id = $model->organization_id;
                $informs->text = 'Отказано в записи. Причина: '.$informs->dop;
                $informs->from = 3;
                $informs->date = date("Y-m-d");
                $informs->read = 0;

                if ($informs->save()) {
                    $inform = new Informs();
                    $inform->program_id = $model->program_id;
                    $inform->contract_id = $model->id;
                    $inform->prof_id = $model->certificate_id;
                    $inform->text = 'Отказано в записи. Причина: '.$informs->dop;
                    $inform->from = 4;
                    $inform->date = date("Y-m-d");
                    $inform->read = 0;

                    if ($inform->save()) { */
                        return $this->redirect('/personal/certificate-archive#panel2');
                    //}
               // }
            }
       // }
       // return $this->render('/informs/comment', [
        //    'informs' => $informs,
       // ]);
    }
    
    public function actionTerminate($id)
    {
        $model = $this->findModel($id);
        $informs = new Informs();
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        
        if ($informs->load(Yii::$app->request->post())) {
            
            if (isset($roles['certificate'])) {
                $model->terminator_user = 1;
            }
            if (isset($roles['organizations'])) {
                $model->terminator_user = 2;
            }
 
            $model->wait_termnate = 1;
            $model->status_comment = $informs->dop;
            
            $cert = Certificates::findOne($model->certificate_id);
            $cert->balance = $cert->balance + $model->rezerv;
            $cert->rezerv = $cert->rezerv - $model->rezerv;
            $cert->save();
            
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
        
           // return '<pre>'.var_dump($contracts).'</pre>';
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
    
    
    
    public function actionMpdf($id) {
        
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        $model = $this->findModel($id);
        
        $cert = Certificates::findOne($model->certificate_id);
        $organization = Organization::findOne($model->organization_id);
        $program = Programs::findOne($model->program_id);
        $group = Groups::findOne($model->group_id);
        $year = Years::findOne($model->year_id);
        $payer = Payers::findOne($model->payer_id);
        if ($year->p21z == 1) { $kv_osn = 'Высшая'; }
        if ($year->p21z == 2) { $kv_osn = 'Первая'; }
        if ($year->p21z == 3) { $kv_osn = 'Иная'; }
        
        $license_date  = explode("-", $organization->license_date);
        $date_proxy  = explode("-", $organization->date_proxy);
        $date_elements_user  = explode("-", $model->start_edu_contract);
        
        //$month_text = \Yii::t('app', 'Today is {0,date}', time());
        $doc_type = $organization->doc_type == 1 ? "доверенности от ".$date_proxy[2].".".$date_proxy[1].".".$date_proxy[0]." № ".$organization->number_proxy : "устава";
        $all_funds = floor($model->all_funds).' рубля(ей) '.round(($model->all_funds - floor($model->all_funds)) * 100, 0).' коп.';
        $funds_cert = floor($model->funds_cert).' рубля(ей) '.round(($model->funds_cert - floor($model->funds_cert)) * 100, 0).' коп.';
        $all_parents_funds = floor($model->all_parents_funds).' рубля(ей) '.round(($model->all_parents_funds - floor($model->all_parents_funds)) * 100, 0).' коп.';
        
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
        
        
        if ($model->number != NULL) {$docnumber = $model->number; } else { $docnumber = '__________'; }
        if ($model->date != NULL) {
            $docdate = explode("-", $model->date); 
            $docdated = $docdate[2].'.'.$docdate[1].'.'.$docdate[0];
        } else { 
            $docdated = '___________'; 
        }
        
        if ($organization->type != 4) {   
        $html = <<<EOD
<div style="font-size: $model->fontsize" > 
<p style="text-align: center;">Договор об образовании №$docnumber от $docdated</p>
<p>_______________________________</p>
<br>

<div align="justify">$organization->full_name, осуществляющ$model->change1 образовательную  деятельность на основании лицензии от $license_date[2].$license_date[1].$license_date[0] г. № $organization->license_number, выданной $organization->license_issued_dat, именуем$model->change2 в дальнейшем "Исполнитель", в лице $model->org_position $model->change_org_fio, действующего на основании $model->change_doctype, и $model->change_fioparent, именуем$model->change6 в   дальнейшем    "Заказчик",    действующ$model->change9  в  интересах несовершеннолетнего, включенного в систему персонифицированного финансирования дополнительного образования на основании сертификата № $cert->number, $model->change_fiochild именуем$model->change8 в дальнейшем "Обучающийся», совместно   именуемые   Стороны,   заключили   настоящий    Договор    о нижеследующем:</div></div>
EOD;
        }
        
        if ($organization->type == 4) {   
        $html = <<<EOD
<div style="font-size: $model->fontsize" > 
<p style="text-align: center;">Договор об образовании №$docnumber от $docdated</p>
<p>_______________________________</p>
<br>

<div align="justify">$organization->name, именуемый в дальнейшем "Исполнитель", и $model->change_fioparent, именуем$model->change6 в   дальнейшем    "Заказчик",    действующий  в  интересах несовершеннолетнего, включенного в систему персонифицированного финансирования дополнительного образования на основании сертификата № $cert->number, $model->change_fiochild именуем$model->change8 в дальнейшем "Обучающийся», совместно   именуемые   Стороны,   заключили   настоящий    Договор    о нижеследующем:</div></div>
EOD;
        }
        
        
        if ($program->year > 1) {$chast = ' части';} else {$chast = '';}
        if ($program->year > 1) {$text5 = 'частью Программы';} else {$text5 = 'Программой';}
        
        if ($program->year >= 2 and $program->year <= 4) {$text144 = 'Полный срок реализации Программы - '.$program->year.' года.';}
        if ($program->year >= 5) {$text144 = 'Полный срок реализации Программы - '.$program->year.' лет.';}
        if ($program->year == 1) {

            $month = (new \yii\db\Query())
            ->select(['month'])
            ->from('years')
            ->where(['id' => $model->year_id])
            ->one();
            
            if ($month['month'] == 1) {
              $text144 = 'Полный срок реализации Программы - '.$month['month'].' месяц.';  
            }
            
            if ($month['month'] >= 2 and $month['month'] <= 4) {
              $text144 = 'Полный срок реализации Программы - '.$month['month'].' месяцa.';  
            }
            
            if ($month['month'] >= 5) {
              $text144 = 'Полный срок реализации Программы - '.$month['month'].' месяцев.';  
            }
        }
        
        
        if ($model->sposob == 1) { 
            $text77 = 'за наличный расчет';
        }
        else {
            $text77 = 'в безналичном порядке на счет Исполнителя, реквизиты которого указанны в разделе IX настоящего Договора,';
        }
        
        if ($model->other_m_price == 0) { 
            $text88 = floor($model->first_m_price * $model->payer_dol) .' руб. '. round((($model->first_m_price * $model->payer_dol) - floor($model->first_m_price * $model->payer_dol)) * 100, 0) .' коп.';
            
            $text89 = floor($model->first_m_price * $model->cert_dol) .' руб. '. round((($model->first_m_price * $model->cert_dol) - floor($model->first_m_price * $model->cert_dol)) * 100, 0) .' коп.';
        }
        else {
            $text88 = floor($model->first_m_price * $model->payer_dol) .' руб. '. round((($model->first_m_price * $model->payer_dol) - floor($model->first_m_price * $model->payer_dol)) * 100, 0) .' коп. - за первый месяц периода обучения по Договору, '.floor($model->other_m_price * $model->payer_dol) .' руб. '. round((($model->other_m_price * $model->payer_dol) - floor($model->other_m_price * $model->payer_dol)) * 100, 0) .' коп. - за каждый последующий месяц периода обучения по Договору.';
           
            $text89 = floor($model->first_m_price * $model->cert_dol) .' руб. '. round((($model->first_m_price * $model->cert_dol) - floor($model->first_m_price * $model->cert_dol)) * 100, 0) .' коп. - за первый месяц периода обучения по Договору, '.floor($model->other_m_price * $model->cert_dol) .' руб. '. round((($model->other_m_price * $model->cert_dol) - floor($model->other_m_price * $model->cert_dol)) * 100, 0) .' коп. - за каждый последующий месяц периода обучения по Договору.';
        }
        
        
        
        
        if ($program->directivity == 'Техническая (робототехника)' or $program->directivity == 'Техническая (иная)') {$directivity = 'технической';}
        if ($program->directivity == 'Естественнонаучная') {$directivity = 'естественнонаучной';}
        if ($program->directivity == 'Физкультурно-спортивная') {$directivity = 'физкультурно-спортивной';}
        if ($program->directivity == 'Художественная') {$directivity = 'художественной';}
        if ($program->directivity == 'Туристско-краеведческая') {$directivity = 'туристско-краеведческой';}
        if ($program->directivity == 'Социально-педагогическая') {$directivity = 'социально-педагогической';}
        
        if ($model->cert_dol != 0) {
                $text1 = ', а также оплатить часть образовательной услуги в объеме и на условиях, предусмотренных разделом IV настоящего Договора ';
            
            $text3 = '3.2.1. Своевременно вносить плату за образовательную услугу в размере и порядке, определенных настоящим Договором, а также предоставлять платежные документы, подтверждающие такую оплату.<br>
             3.2.2. Создавать условия для получения Обучающимся образовательной услуги.<br>';
                
            $text4 = '4.1. Полная стоимость образовательной услуги за период обучения по Договору составляет '.floor($model->all_funds) .' руб. 
    '. round(($model->all_funds - floor($model->all_funds)) * 100, 0) .' коп., в том числе:<br>
                4.1.1. Будет оплачено за счет средств сертификата дополнительного образования Обучающегося - '. floor($model->funds_cert) .' руб. '. round(($model->funds_cert - floor($model->funds_cert)) * 100, 0).' коп.<br>
                4.1.2. Будет оплачено за счет средств Заказчика - '. floor($model->all_parents_funds) .' руб. '. round(($model->all_parents_funds - floor($model->all_parents_funds)) * 100, 0) .' коп.<br>
            4.2. Оплата за счет средств сертификата осуществляется в рамках договора '.Yii::$app->params['param42'].' № '.$cooperate['number'] .' от '. $date_cooperate[2] .'.'.$date_cooperate[1].'.'.$date_cooperate[0].', заключенного между Исполнителем и '. $payer->name_dat .' (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере:
            '.$text88.'<br>
            4.3. Заказчик осуществляет оплату ежемесячно '.$text77.' не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: '.$text89.'<br>
            4.4. Оплата за счет средств сертификата и Заказчика за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.';        
                
        }
        
        if ($model->cert_dol == 0) {
            
            $text1 = '';
            $text3 = '3.2.1. Создавать условия для получения Обучающимся образовательной услуги.<br>';
                
            
            $text4 = '
            4.1. Полная стоимость образовательной услуги за период обучения по Договору составляет '.floor($model->all_funds) .' руб. 
    '. round(($model->all_funds - floor($model->all_funds)) * 100, 0) .' коп.. Вся сумма будет оплачена за счет средств сертификата дополнительного образования Обучающегося.<br>
            4.2. Оплата за счет средств сертификата осуществляется в рамках договора '.Yii::$app->params['param42'].' № '.$cooperate['number'] .' от '. $date_cooperate[2] .'.'.$date_cooperate[1].'.'.$date_cooperate[0].', заключенного между Исполнителем и '. $payer->name_dat .' (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере:
            '.$text88.'<br>
    
            4.3. Оплата за счет средств сертификата за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.
            ';    
        }
        
         if ($year->kvdop == 0 and $year->hoursindivid == 0) {
                $text2 = '
                3.1.5.2. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                '.$program->norm_providing.'<br>
                3.1.5.3. Обеспечить проведение занятий в группе с наполняемостью не более '.$year->maxchild.' детей.<br>
                3.1.5.4. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образоательной услуги).<br>
                3.1.5.5. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
                if ($model->cert_dol != 0) {
                    $text2 = $text2.'3.1.5.6. Принимать от Заказчика плату за образовательные услуги.<br>';
                }
            }
        
        
         if ($year->kvdop == 0 and $year->hoursindivid != 0) {
                $text2 = '
                3.1.5.2. Обеспечить индивидуальное консультирование обучающегося в рамках оказания образовательной услуги в объеме не менее '.$year->hoursindivid.' ак. час.<br>
                3.1.5.3. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                '.$program->norm_providing.'<br>
                3.1.5.4. Обеспечить проведение занятий в группе с наполняемостью не более '.$year->maxchild.' детей.<br>
                3.1.5.5. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                3.1.5.6. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
                if ($model->cert_dol != 0) {
                    $text2 = $text2.'3.1.5.7. Принимать от Заказчика плату за образовательные услуги.<br>';
                }
            }
        
        
        if ($year->kvdop != 0 and $year->hoursindivid == 0) {
                $text2 = '
                3.1.5.2. Обеспечить одновременное сопровождение группы детей не менее чем двумя педагогическими работниками, за счет привлечения к оказанию услуги дополнительного(ых) педагогического(их) работника(ов), квалификация которого(ых) соответствует следующим условиям:<br>
                '.$year->kvdop.'<br>
                3.1.5.3. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                '.$program->norm_providing.'<br>
                3.1.5.4. Обеспечить проведение занятий в группе с наполняемостью не более '.$year->maxchild.' детей.<br>
                3.1.5.5. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                3.1.5.6. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
                if ($model->cert_dol != 0) {
                        $text2 = $text2.'3.1.5.7. Принимать от Заказчика плату за образовательные услуги.<br>';
                }
            }
        
        if ($year->kvdop != 0 and $year->hoursindivid != 0) {
                $text2 = '
                3.1.5.2. Обеспечить индивидуальное консультирование обучающегося в рамках оказания образовательной услуги в объеме не менее '.$year->hoursindivid.' ак. час.<br>
                 3.1.5.3. Обеспечить одновременное сопровождение группы детей не менее чем двумя педагогическими работниками, за счет привлечения к оказанию услуги дополнительного(ых) педагогического(их) работника(ов), квалификация которого(ых) соответствует следующим условиям:<br>
                '.$year->kvdop.'<br>
                3.1.5.4. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br> 
                «'.$program->norm_providing.'»<br>
                3.1.5.5. Обеспечить проведение занятий в группе с наполняемостью не более '.$year->maxchild.' детей.<br>
                3.1.5.6. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                3.1.5.7. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
                if ($model->cert_dol != 0) {
                    $text2 = $text2.'3.1.5.8. Принимать от Заказчика плату за образовательные услуги.<br>';
                }
            }       
        
    $start_edu_contract  = explode("-", $model->start_edu_contract);
    $datestop  = explode("-", $group->datestop);
        
        $text = '
        <div style="font-size: '.$model->fontsize.'" >
        <p style="text-align:center">I. Предмет Договора</p>

<div align="justify">
	1.1. Исполнитель обязуется оказать Обучающемуся образовательную услугу по реализации'.$chast.' дополнительной общеобразовательной программы '.$directivity.' направленности «'.$program->name.'» (далее – Образовательная услуга, Программа), в пределах учебного плана программы, предусмотренного на период обучения по Договору.<br>
    1.2. Форма обучения и используемые образовательные технологии: '.$programform.'<br>
	1.3. Заказчик обязуется содействовать получению Обучающимся образовательной услуги'.$text1.'.<br>
	1.4. '.$text144.' Период обучения по Договору: с '.$start_edu_contract[2].'.'.$start_edu_contract[1].'.'.$start_edu_contract[0].' по '.$datestop[2].'.'.$datestop[1].'.'.$datestop[0].'.
</div>

<p style="text-align:center">II. Права Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
    2.1.  Исполнитель вправе:<br>
    2.1.1. Самостоятельно осуществлять образовательный процесс, устанавливать системы оценок, формы, порядок и периодичность проведения промежуточной аттестации Обучающегося.<br>
    2.1.2. Применять к Обучающемуся меры поощрения и меры дисциплинарного взыскания в соответствии с законодательством Российской Федерации, учредительными документами Исполнителя, настоящим Договором и локальными нормативными актами Исполнителя.<br>
    2.1.3. В случае невозможности проведения необходимого числа занятий, предусмотренных учебным планом, на определенный месяц оказания образовательной услуги, обеспечить оказание образовательной услуги в полном объеме за счет проведения дополнительных занятий в последующие месяцы действия настоящего Договора.<br>
    2.2. Заказчик вправе:<br>
    2.2.1. Получать информацию от Исполнителя по вопросам организации и обеспечения надлежащего оказания образовательной услуги.<br>
    2.2.2. Обращаться к Исполнителю по вопросам, касающимся образовательного процесса.<br>
    2.2.3. Участвовать в оценке качества образовательной услуги, проводимой в рамках системы персонифицированного финансирования.<br>
    2.3. Обучающемуся предоставляются академические права в соответствии с частью 1 статьи 34 Федерального закона от 29 декабря 2012 г. №273-ФЗ "Об образовании в Российской Федерации". Обучающийся также вправе:<br>
    2.3.1. Получать информацию от Исполнителя по вопросам организации и обеспечения надлежащего оказания образовательной услуги.<br>
    2.3.2. Обращаться к Исполнителю по вопросам, касающимся образовательного процесса.<br>
    2.3.3. Пользоваться в порядке, установленном локальными нормативными актами, имуществом Исполнителя, необходимым для освоения Программы.<br>
    2.3.4. Принимать в порядке, установленном локальными нормативными актами, участие в социально-культурных, оздоровительных и иных мероприятиях, организованных Исполнителем.<br>
    2.3.5. Получать полную и достоверную информацию об оценке своих знаний, умений, навыков и компетенций, а также о критериях этой оценки.
</div>

<p style="text-align:center">III. Обязанности Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
	3.1. Исполнитель обязан:<br>
    3.1.1. Зачислить Обучающегося в качестве учащегося на обучение по Программе.<br>
    3.1.2. Довести до Заказчика информацию, содержащую сведения о предоставлении платных образовательных услуг в порядке и объеме, которые предусмотрены Законом Российской Федерации "О защите прав потребителей" и Федеральным законом "Об образовании в Российской Федерации"<br>
    3.1.3. Организовать и обеспечить надлежащее предоставление образовательных услуг, предусмотренных разделом I настоящего Договора. Образовательные услуги оказываются в соответствии с учебным планом Программы и расписанием занятий Исполнителя.<br>
    3.1.4. Обеспечить полное выполнение учебного плана Программы, предусмотренного на период обучения по Договору.<br>
    3.1.5. Обеспечить Обучающемуся предусмотренные Программой условия ее освоения, в том числе:<br>
        3.1.5.1. Обеспечить сопровождение оказания услуги педагогическим работником, квалификация которого соответствует следующим условиям:<br> «'.$year->kvfirst.'»<br>
        '.$text2.'
        
    3.2. Заказчик обязан:<br>
        '.$text3.'
        
    3.3. Обучающийся обязан:<br>
        3.3.1. Выполнять задания для подготовки к занятиям, предусмотренным учебным планом Программы<br>
        3.3.2. Извещать Исполнителя о причинах отсутствия на занятиях.<br>
        3.3.3. Обучаться по образовательной программе с соблюдением требований, установленных учебным планом Программы<br>
        3.3.4. Соблюдать требования учредительных документов, правила внутреннего распорядка и иные локальные нормативные акты Исполнителя.<br>
        3.3.5. Соблюдать иные требования, установленные в статье 43 Федерального закона от 29 декабря 2012 г. №273-ФЗ "Об образовании в Российской Федерации"<br>
</div>

<p style="text-align:center">IV. Стоимость услуги, сроки и порядок их оплаты</p>
</div>
';
        
        $mpdf = new mPDF();
        $mpdf->WriteHtml($html); // call mpdf write html
        $mpdf->WriteHtml($text); // call mpdf write html
        
        $mpdf->WriteHtml('<div align="justify"  style="font-size: '.$model->fontsize.'">'.$text4.'</div>');
        
        
        $mpdf->WriteHtml('
<div style="font-size: '.$model->fontsize.'" >
<p style="text-align:center">V. Основания изменения и порядок расторжения договора</p>

<div align="justify">
    5.1. Условия, на которых заключен настоящий Договор, могут быть изменены по соглашению Сторон или в соответствии с законодательством Российской Федерации.<br>
    5.2. Настоящий Договор может быть расторгнут по соглашению Сторон.<br>
    5.3. Настоящий Договор может быть расторгнут по инициативе Исполнителя в одностороннем порядке в случаях:<br>
    установления нарушения порядка приема Обучающегося на обучение по Программе, повлекшего по вине Обучающегося его незаконное зачисление на обучение по Программе;<br>
    просрочки оплаты стоимости образовательной услуг со стороны Уполномоченной организации и/или Заказчика.
    невозможности надлежащего исполнения обязательства по оказанию образовательной услуги вследствие действий (бездействия) Обучающегося;<br>
    приостановления действия сертификата дополнительного образования Обучающегося;<br>
    получения предписания о расторжении договора от Уполномоченной организации, направляемой Уполномоченной организацией Исполнителю в соответствии с Соглашением;<br>
    в иных случаях, предусмотренных законодательством Российской Федерации.<br>
    5.4. Настоящий Договор может быть расторгнут по инициативе Заказчика.<br>
    5.5. Исполнитель вправе отказаться от исполнения обязательств по Договору при условии полного возмещения Заказчику убытков.<br>
    5.6. Заказчик вправе отказаться от исполнения настоящего Договора при условии оплаты Исполнителю фактически понесенных им расходов, связанных с исполнением обязательств по Договору.<br>
    5.7. Для расторжения договора Заказчик направляет Исполнителю уведомление о расторжении настоящего Договора. Датой расторжения договора является последний день месяца, в котором было направлено указанное уведомление о расторжении настоящего Договора.<br>
    5.8. Для расторжения договора Исполнитель направляет Заказчику уведомление о расторжении настоящего Договора, в котором указывает причину расторжения договора. Датой расторжения договора является последний день месяца, в котором было направлено указанное уведомление о расторжении настоящего Договора.<br>
</div>

<p style="text-align:center">VI. Ответственность Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
    6.1. За неисполнение или ненадлежащее исполнение своих обязательств по Договору Стороны несут ответственность, предусмотренную законодательством Российской Федерации и Договором.<br>
    6.2. При обнаружении недостатка образовательной услуги, в том числе оказания ее не в полном объеме, предусмотренном '.$text5.', Заказчик вправе по своему выбору потребовать:<br>
    6.2.1. Безвозмездного оказания образовательной услуги.<br>
    6.2.2. Возмещения понесенных им расходов по устранению недостатков оказанной образовательной услуги своими силами или третьими лицами.<br>
    6.3. Заказчик вправе отказаться от исполнения Договора и потребовать полного возмещения убытков, если в срок недостатки образовательной услуги не устранены Исполнителем. Заказчик также вправе отказаться от исполнения Договора, если им обнаружен существенный недостаток оказанной образовательной услуги или иные существенные отступления от условий Договора.<br>
    6.4. Если Исполнитель нарушил сроки оказания образовательной услуги (сроки начала и (или) окончания оказания образовательной услуги и (или) промежуточные сроки оказания образовательной услуги) либо если во время оказания образовательной услуги стало очевидным, что она не будет осуществлена в срок, Заказчик вправе по своему выбору:<br>
    6.4.1. Назначить Исполнителю новый срок, в течение которого Исполнитель должен приступить к оказанию образовательной услуги и (или) закончить оказание образовательной услуги.<br>
    6.4.2. Поручить оказать образовательную услугу третьим лицам за разумную цену и потребовать от Исполнителя возмещения понесенных расходов.<br>
    6.4.3. Расторгнуть Договор.<br>
    6.5. Заказчик вправе потребовать полного возмещения убытков, причиненных ему в связи с нарушением сроков начала и (или) окончания оказания образовательной услуги, а также в связи с недостатками образовательной услуги.<br>
</div>

<p style="text-align:center">VII. Срок действия Договора</p>

<div align="justify">
    7.1. Настоящий Договор вступает в силу с '.$date_elements_user[2].'.'.$date_elements_user[1].'.'.$date_elements_user[0].' и действует до полного исполнения Сторонами своих обязательств.<br>
</div>

<p style="text-align:center">VIII. Заключительные положения</p>

<div align="justify">
    8.1. Сведения,  указанные  в  настоящем  Договоре,    соответствуют информации,  размещенной  на  официальном  сайте  Исполнителя    в   сети "Интернет" на дату заключения настоящего Договора.<br>
    8.2. Под периодом обучения по Договору  понимается  промежуток  времени  с  даты проведения первого занятия по дату проведения последнего занятия в рамках оказания образовательной услуги.<br>
    8.3. Настоящий Договор составлен в 2-х экземплярах,  по  одному   для каждой из Сторон. Все  экземпляры  имеют  одинаковую  юридическую   силу. Изменения и дополнения настоящего Договора могут производиться только в письменной форме и подписываться уполномоченными представителями Сторон.<br>
    8.4. Изменения Договора оформляются дополнительными соглашениями к Договору.<br>
    8.5. Изменения раздела IV настоящего договора допускаются лишь при условии согласования указанных изменений с Уполномоченной организацией.<br>


<p style="text-align:center">IX. Адреса и реквизиты сторон</p>
</div>
<table align="center" <div style="font-size: '.$model->fontsize.'" > border="0" cellpadding="10" cellspacing="10">
	<tbody>
		<tr>
			<td width="300" style="vertical-align: top;">
            <p>Исполнитель</p>
            <br>
			<p>'.$organization->name.'</p>

			<p>Юридический адрес: '.$organization->address_legal.'</p>

			<p>Адрес местонахождения: '.$organization->address_actual.'</p>

			<p>Наименование банка: '.$organization->bank_name.'</p>
            
            <p>Город банка: '.$organization->bank_sity.'</p>

			<p>БИК: '.$organization->bank_bik.'</p>

			<p>к/с: '.$organization->korr_invoice.'</p>

			<p>р/с: '.$organization->rass_invoice.'</p>
            
            <p>ИНН: '.$organization->inn.'</p>
            
            <p>КПП: '.$organization->KPP.'</p>
            
            <p>ОРГН/ОРГНИП: '.$organization->OGRN.'</p>
            
			</td>
			<td width="300"  style="vertical-align: top;">
            <p>Заказчик</p>
            <br>
            ФИО:<br>
            Дата рождения:<br>
            Адрес места жительства:<br>
            <br>
            <br>
            Паспорт: серия ____ номер ________<br>
            Выдан:<br>
            <br>
            <br>
            Телефон:<br>
            
                <!--<form enctype="multipart/form-data">
                
                <textarea style="background: #fff; border-style: none; border-color: Transparent; overflow: auto;" rows="12" cols="40" name="text">
&#10;ФИО: '.$cert->fio_parent.'&#10;Дата рождения:&#10;Адрес места жительства:&#10;Паспорт: серия ____ номер____&#10;Выдан ____&#10;Телефон:
                </textarea>
                </form> -->
			</td>
		</tr>
		<tr>
			<td>
			'.$model->org_position_min.'

			<p>&nbsp;</p>

			<p>&nbsp;&nbsp;&nbsp;___________________</p>

			<p>&nbsp;м/п</p>
			</td>
			<td>
			<p>&nbsp;</p>

			<p>&nbsp;</p>

			<p>&nbsp;&nbsp;&nbsp;___________________</p>

			<p>&nbsp;</p>
			</td>
		</tr>
	</tbody>
</table>
</div>');
        
        echo $mpdf->Output('contract-'.$model->number.'.pdf', 'D'); // call the mpdf api output as needed
    
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
        
        if ($model->ocen_fact == null) { $model->ocen_fact = 0; }
        if ($model->ocen_kadr == null) { $model->ocen_kadr = 0; }
        if ($model->ocen_mat == null) { $model->ocen_mat = 0; }
        if ($model->ocen_obch == null) { $model->ocen_obch = 0; }

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

        if($user->load(Yii::$app->request->post())) {

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
                                ->andWhere(['month' => date('m')-1])
                                ->andWhere(['preinvoice' => 0])
                                ->one();

                     if (empty($com) && empty($com_pre)) {

                        $completeness = new Completeness();
                        $completeness->group_id = $model->group_id;
                        $completeness->contract_id = $model->id;

                        $start_edu_contract  = explode("-", $model->start_edu_contract);

                       
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

                                if ($month == date('m')-1) {
                                    $price = $model->first_m_price * $model->payer_dol;
                                } else {
                                    $price = $model->other_m_price * $model->payer_dol;
                                }

                        $completeness->sum = ($price * $completeness->completeness) / 100;  
                         
                         if (date('m') != 1) {
                            $completeness->save();
                        } 

                        $preinvoice = new Completeness();
                        $preinvoice->group_id = $model->group_id;
                        $preinvoice->contract_id = $model->id;
                        $preinvoice->month = date('m');
                        $preinvoice->year =  date('Y');
                        $preinvoice->preinvoice = 1;
                        $preinvoice->completeness = 80;

                        $start_edu_contract  = explode("-", $model->start_edu_contract);
                        $month = $start_edu_contract[1];

                                if ($month == date('m')) {
                                    $price = $model->first_m_price * $model->payer_dol;
                                } else {
                                    $price = $model->other_m_price * $model->payer_dol;
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
                        $program->last_s_contracts_rod = $program->last_s_contracts_rod+1;
                        $program->last_s_contracts = $program->last_s_contracts+1;
                    }
                    if ($cont->terminator_user == 2) {
                        $program->last_s_contracts = $program->last_s_contracts+1;
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
                        $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, 12, date('Y')-1);
                        $cont->date_termnate = (date("Y")-1).'-12-'.$cal_days_in_month;
                    } else {
                         $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m')-1, date('Y'));
                        $cont->date_termnate = date("Y").'-'.(date('m')-1).'-'.$cal_days_in_month;
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

                $datestart = date("Y-m").'-01';

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
                        $model->rezerv = $model->rezerv - ($model->other_m_price * $model->payer_dol);
                        $model->paid = $model->paid + ($model->other_m_price * $model->payer_dol);
                        $cert->rezerv = $cert->rezerv - ($model->other_m_price * $model->payer_dol);

                         $model->save();
                        $cert->save();
                    }

                }

                    $contracts4 = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['status' => [1, 4]])
                            ->column();

                if (date('m') == 1) { $twomonth = 11; }
                if (date('m') == 2) { $twomonth = 12; }
                if (date('m') > 2) { $twomonth = date('m')-2; }

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
                                 $certificate->balance = $certificate->balance + (($contract->first_m_price * $contract->payer_dol) / 100) * (100 - $completeness['completeness']);
                             }
                             else {
                                 $certificate->balance = $certificate->balance + (($contract->other_m_price * $contract->payer_dol) / 100) * (100 - $completeness['completeness']);
                             }

                             $certificate->save();
                         }                 
                    }



                 

                return $this->redirect(['/personal/operator-contracts']);
            
            }
            else {
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

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                
                $cont = $this->findModel($id);
               // return var_dump($id);

                $certificates = new Certificates();
                 $certificate = $certificates->getCertificates();


                $cert = Certificates::findOne($certificate->id);
                        $cert->balance = $cert->balance + $cont->rezerv;
                        $cert->save();

                $cont->delete();

                return $this->redirect(['/personal/certificate-programs']);
            }
            else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
                 return $this->redirect(['/personal/operator-organizations']);
            }
        }
        return $this->render('/user/delete', [
            'user' => $user,
        ]);
    }
    
    public function actionYear() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $prog_id = $parents[0];
                
                //$out = Years::find()->where(['program_id' => $prog_id])->asArray()->all();
                
                $rows = (new \yii\db\Query())
                    ->select(['id', 'year'])
                    ->from('years')
                    ->where(['program_id' => $prog_id])
                    ->andWhere(['open' => 1])
                    ->all();
                
                $out = array();
                foreach ($rows as $value) {
                    
                    /*$contract = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status' => [0,1,3]])
                        ->andWhere(['year_id' => $value['id']])
                        ->count();
                    $sum = $value['maxchild'] - $contract; */

                    //if ($sum > 0) {
                        array_push($out, array('id'=> $value['id'], 'name'=> $value['year']));
                   // }
                } 
                

                //$out = ArrayHelper::map(Years::find()->where(['program_id' => $prog_id])->all(), 'id', 'year');
                //$out = self::getSubCatList($cat_id); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                //$out = [['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']];
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }
    
    public function actionYeargroup() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $ids = $_POST['depdrop_parents'];
            $prog_id = empty($ids[0]) ? null : $ids[0];
            $year_id = empty($ids[1]) ? null : $ids[1];
            if ($prog_id != null) {
               //$data = self::getProdList($prog_id, $year_id);
                
                $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('groups')
                    ->where(['program_id' => $prog_id])
                    ->andWhere(['year_id' => $year_id])
                    ->all();
                
                $maxchild = (new \yii\db\Query())
                    ->select(['maxchild'])
                    ->from('years')
                    ->where(['id' => $year_id])
                    ->one();
                
                $out = array();
                foreach ($rows as $value) {
                    
                    $contract = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status' => [0,1,3]])
                        ->andWhere(['group_id' => $value['id']])
                        ->count();
                    
                    $sum = $maxchild['maxchild'] - $contract; 
                        
                    if ($sum > 0) {  
                        array_push($out, array('id'=> $value['id'], 'name'=> $value['name']));
                    }
                } 
                
                /**
                 * the getProdList function will query the database based on the
                 * cat_id and sub_cat_id and return an array like below:
                 *  [
                 *      'out'=>[
                 *          ['id'=>'<prod-id-1>', 'name'=>'<prod-name1>'],
                 *          ['id'=>'<prod_id_2>', 'name'=>'<prod-name2>']
                 *       ],
                 *       'selected'=>'<prod-id-1>'
                 *  ]
                 */

                echo Json::encode(['output'=>$out, 'selected'=>'']);
               return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    
    public function actionNewprice()
     {
         
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
         $contracts5 = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['organization_id' => 48])
                            ->andWhere(['all_funds' => 968])
                            ->column();

                 foreach ($contracts5 as $contract5) {

                    $model = $this->findModel($contract5);
                    
                     $model->first_m_price = 968;
                     $model->save();
                     
                }

                echo "OK";
     }
    
    /*
    
    
    public function actionDecper3()
     {
         
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
         $contracts5 = (new \yii\db\Query())
                            ->select(['id', 'certificate_id'])
                            ->from('contracts')
                            ->where(['status' => 1])
                            ->andWhere(['start_edu_contract' => '2016-12-01'])
                            ->all();

                 foreach ($contracts5 as $contract5) {

                    $model = $this->findModel($contract5['id']);

                     $com_dec = (new \yii\db\Query())
                                ->select(['completeness', 'id'])
                                ->from('completeness')
                                ->where(['contract_id' => $model->id])
                                ->andWhere(['month' => 12])
                                ->andWhere(['preinvoice' => 0])
                                ->one();

                     if (empty($com_dec)) {

                        $completeness = new Completeness();
                        $completeness->group_id = $model->group_id;
                        $completeness->contract_id = $model->id;

                        $start_edu_contract  = explode("-", $model->start_edu_contract);

                            $completeness->month = 12;
                            $completeness->year = $start_edu_contract[0];
                        
                        
                        $completeness->preinvoice = 0;
                        $completeness->completeness = 100;

                        $month = $start_edu_contract[1];

                                if ($month == 12) {
                                    $price = $model->first_m_price * $model->payer_dol;
                                } else {
                                    $price = $model->other_m_price * $model->payer_dol;
                                }

                        $completeness->sum = ($price * $completeness->completeness) / 100;  
                         
    
                            $completeness->save();
                    }
                }

                echo "OK";
     }
    
    
    public function actionDecper2()
     {
         
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
         $contracts5 = (new \yii\db\Query())
                            ->select(['id', 'certificate_id'])
                            ->from('contracts')
                            ->where(['status' => 1])
                            ->where(['start_edu_contract' => '2016-11-01'])
                            ->all();

                 foreach ($contracts5 as $contract5) {

                    $model = $this->findModel($contract5['id']);

                     $com_dec = (new \yii\db\Query())
                                ->select(['completeness', 'id'])
                                ->from('completeness')
                                ->where(['contract_id' => $model->id])
                                ->andWhere(['month' => 11])
                                ->andWhere(['preinvoice' => 0])
                                ->one();

                     if (empty($com_dec)) {

                        $completeness = new Completeness();
                        $completeness->group_id = $model->group_id;
                        $completeness->contract_id = $model->id;

                        $start_edu_contract  = explode("-", $model->start_edu_contract);

                            $completeness->month = 11;
                            $completeness->year = $start_edu_contract[0];
                        
                        
                        $completeness->preinvoice = 0;
                        $completeness->completeness = 100;

                        $month = $start_edu_contract[1];

                                if ($month == 12) {
                                    $price = $model->first_m_price * $model->payer_dol;
                                } else {
                                    $price = $model->other_m_price * $model->payer_dol;
                                }

                        $completeness->sum = ($price * $completeness->completeness) / 100;  
                         
    
                            $completeness->save();
                    }
                }

                echo "OK";
     }
     
    
     
    public function actionSumupd() {
    
        $certificates = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('certificates')
                    ->column();     
        foreach ($certificates as $certificate) {
            
            $cert = Certificates::findOne($certificate);
            
            $contracts = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['organization_id' => 38])
                    ->andWhere(['certificate_id' => $certificate])
                    ->count(); 
            
            if ($contracts > 0) {
                $cert->nominal = 18311.5;
                $cert->balance = 0.29;
                $cert->rezerv = 15695.3;
                
                $cert->save();
            }
        }
        echo "ok";
    } 
    
    
    
    public function actionImport()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        $inputFile = "uploads/contracts-3.xlsx";
        
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);

        
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();
        
        
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowDada = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row,NULL,TRUE,FALSE);
            
            if($row == 1) {
                continue;
            }
            
            $model = new Contracts();
            $model->certificate_id = $rowDada[0][0];
            $model->payer_id = $rowDada[0][1];
            $model->program_id = $rowDada[0][2];
            $model->year_id = $rowDada[0][3];
            $model->organization_id = $rowDada[0][4];
            $model->group_id = $rowDada[0][5];
            $model->status = $rowDada[0][6];
            $model->all_funds = $rowDada[0][7];
            $model->funds_cert = $rowDada[0][8];
            $model->all_parents_funds = $rowDada[0][9];
            //$model->stop_edu_contract = $rowDada[0][10];
            $model->start_edu_contract = '2016-12-01';
            $model->stop_edu_contract = '2017-05-31';
            //$model->start_edu_contract = $rowDada[0][11];
            $model->sposob = $rowDada[0][12];
            $model->prodolj_d = $rowDada[0][13];
            $model->prodolj_m = $rowDada[0][14];
            $model->prodolj_m_user = $rowDada[0][15];
            $model->first_m_price = $rowDada[0][16];
            $model->other_m_price = $rowDada[0][17];
            $model->first_m_nprice = $rowDada[0][18];
            $model->other_m_nprice = $rowDada[0][19];
            $model->change1 = $rowDada[0][20];
            $model->change2 = $rowDada[0][21];
            $model->change_org_fio = $rowDada[0][22];
            $model->change_doctype = $rowDada[0][23];
            $model->change_fioparent = $rowDada[0][24];
            $model->change6 = $rowDada[0][25];
            $model->change_fiochild = $rowDada[0][26];
            $model->change8 = $rowDada[0][27];
            $model->change9 = $rowDada[0][28];
            $model->change10 = $rowDada[0][29];
            $model->cert_dol = $rowDada[0][30];
             $model->payer_dol = $rowDada[0][31];
             $model->rezerv = $rowDada[0][32];
             $model->paid = $rowDada[0][33];
             $model->terminator_user = $rowDada[0][34];
             $model->fontsize = $rowDada[0][35];
            $model->org_position = $rowDada[0][36];
            $model->org_position_min = $rowDada[0][37];
            $model->number = $rowDada[0][38];
            $model->date = '2016-12-01';
            
            $model->save();
            
            print_r($model->getErrors());
            
            
            $certificates = Certificates::findOne($rowDada[0][0]);
            $certificates->balance = $certificates->balance - $rowDada[0][32];
            $certificates->rezerv = $certificates->rezerv + $rowDada[0][32];
            $certificates->save();
            
            print_r($certificates->getErrors());
            
        }
        echo "OK!";
        
    }
    
    */
    /*
    public function actionAnimport()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        
            $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['date' => '0000-00-00'])
            ->column();
        
            foreach ($contracts as $contract) {
                $cont = $this->findModel($contract);
                 
            
                    
                    
                    $certificates = Certificates::findOne($cont->certificate_id);
                    $certificates->balance = $certificates->balance + $cont->rezerv;
                    $certificates->rezerv = $certificates->rezerv - $cont->rezerv;
                    $certificates->save();

                        $cont->delete();

                    print_r($certificates->getErrors());

                    echo "ок";
                
            }
           
        echo "ОК!";
        
    }
    
    
    public function actionCertdel()
    {
        $users = (new \yii\db\Query())
            ->select(['id'])
            ->from('user')
            ->where(['>=', 'id', 1999])
            ->column();
        
        
        
        foreach ($users as $user) {
              
            //User::findOne($user)->delete();
            
        }
        
        echo "ok";
    }
    
    public function actionPositions()
    {
        
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->column();
        
        foreach ($contracts as $contract) {
            
            $cont = $this->findModel($contract);
             
            $org = Organization::findOne($cont->organization_id);
            
           // $org->position = 'директора';
           // $org->position_min = 'Директор';
            
          //  $org->save();
                
            $cont->org_position = $org->position;
            $cont->org_position_min = $org->position_min;
                
            if ($cont->save()) {
                echo $cont->org_position.' ';
                echo $cont->org_position_min.' ';
                echo '-- ok --<br>';
            }
            else {
                echo $cont->id.'-- error --<br><br><br>';
            }
        }
        
        echo "OK!";
    }

    
    
    public function actionContorg()
    {
        $users = [
 27,31,36,39,40,45,55,60,63,70,71,72,75,78,84,88,93,94,101,102,105,106,109,115,118,126,131,133,134,136,142,144,145,146,150,151,153,155,162,167,169,170,174,175,176,177,187,190,192,194,196,198,203,205,207,210,212,213,215,224,225,228,232,240,247,248,249,251,259,260,262,266,275,291,298,299,302,303,304,305,308,312,314,315,317,328,329,342,345,347,349,354,362,372,382,387,393,397,416,418,425,437,438,453
        ];
        
        
        foreach ($users as $user) {
            
            $certificates = Certificates::findOne($user);
            $certificates->rezerv = 4267.51;
             $certificates->balance = 2485.49;
            if ($certificates->save()) {
                echo "ok ";
            }
            else {
                echo "err ";
            }    
                
            $contract = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['certificate_id' => $user])
            ->one();
            
            $cont = $this->findModel($contract['id']);
            $cont->all_funds = 4267.51;
            $cont->funds_cert = 4267.51;
            $cont->first_m_price = 711.25;
            $cont->other_m_price = 711.25;
            $cont->first_m_nprice = 711.25;
            $cont->other_m_nprice = 711.25;
            $cont->rezerv = 4267.51;
                
            if ($cont->save()) {
                echo "ok<br>";
            }
            else {
                echo "err<br>";
            }
            
        }
        
        echo "OK!";
    }
    }
    public function actionDubles()
    {
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->column();
        
        
        foreach ($contracts as $contract) {
            
            $contrac = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['id' => $contract])
            ->one();
            
            if (isset($contrac) and !empty($contrac)) {
                $cont = $this->findModel($contract);
                    
                $duble = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['certificate_id' => $cont->certificate_id])
                ->andWhere(['payer_id' => $cont->payer_id])
                ->andWhere(['program_id' => $cont->program_id])
                ->andWhere(['year_id' => $cont->year_id])
                ->andWhere(['organization_id' => $cont->organization_id])
                ->andWhere(['group_id' => $cont->group_id])
                ->andWhere(['status' => $cont->status])
                ->column();

                if (count($duble) > 2) {
                    echo " --- #3 ----<br>";
                }
                else {
                    if (count($duble) == 2) {

                        array_shift($duble);

                        //array_unique($duble);

                        //unset($duble[0]);

                        //var_dump($duble);
                        foreach ($duble as $dub) {
                            echo " ---- $dub ----";

                            $contr = $this->findModel($dub);

                            $certificates = Certificates::findOne($contr->certificate_id);
                            $certificates->rezerv = $certificates->rezerv - $contr->rezerv;
                            $certificates->balance = $certificates->balance + $contr->rezerv;

                            if ($certificates->save()) {
                                if ($contr->delete()) {
                                    echo "ok";
                                }
                                else {
                                    echo "err";
                                }
                            }
                            else {
                                echo "ERR";
                            }

                            //return $this->redirect(['dubles']);
                        }
                    }
                }
            }
            
        }
        
        echo "OK!";
    }
    */
    /*
    public function actionDubles()
    {
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->column();
        
        
        foreach ($contracts as $contract) {
            
            $cont = $this->findModel($contract);
            
            if ($cont->start_edu_contract == '2016-12-01') {
                
                $certificates = Certificates::findOne($cont->certificate_id); 
                
                $certificates->rezerv = $certificates->rezerv - $cont->rezerv;
                $certificates->balance = $certificates->balance + $cont->rezerv;

                if ($certificates->save()) {
                    if ($cont->delete()) {
                        echo "ok";
                    }
                    else {
                        echo "err";
                    }
                }
                else {
                    echo "ERR";
                } 
            }        
        }
        
        echo "OK!";
    } 
    */
    /*
    public function actionDubles()
    {
        $certificates = (new \yii\db\Query())
            ->select(['id'])
            ->from('certificates')
            ->column();
        
        
        foreach ($certificates as $cert) {
            
            $certificates = Certificates::findOne($cert); 
            
            if ($certificates->rezerv < 0) {
                
                $certificates->rezerv = 0;

                if ($certificates->save()) {
                        echo "ok";

                }
                else {
                    echo "ERR";
                } 
            }        
        }
        
        echo "OK!";
    }
  

    public function actionDubles2()
    {
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['organization_id' => 49])
            ->column();
        
        
        foreach ($contracts as $contract) {
            
            $cont = $this->findModel($contract);
            
            //if ($cont->start_edu_contract != '2016-12-01') {
                
                $certificates = Certificates::findOne($cont->certificate_id); 
                
                $certificates->rezerv = $certificates->rezerv - $cont->rezerv;
                $certificates->balance = $certificates->balance + $cont->rezerv + $cont->paid;

                if ($certificates->save()) {
                    echo "ok1 ";
                    if ($cont->delete()) {
                        echo "ok2 <br>";
                    }
                    else {
                        echo " --- err --- <br>";
                    }
                }
                else {
                    echo "ERR <br> ---- <br>";
                } 
            //}        
        }
        
        echo "OK!";
    } 
      
   public function actionWaitterm()
    {
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['wait_termnate' => 1])
            ->column();
        
        
        foreach ($contracts as $contract) {
            
            $cont = $this->findModel($contract);
            
            //if ($cont->start_edu_contract != '2016-12-01') {
                
                $certificates = Certificates::findOne($cont->certificate_id); 
                
                $certificates->rezerv = $certificates->rezerv - $cont->rezerv;
                $certificates->balance = $certificates->balance + $cont->rezerv + $cont->paid;

                if ($certificates->save()) {
                    echo "ok1 ";
                    if ($cont->delete()) {
                        echo "ok2 <br>";
                    }
                    else {
                        echo " --- err --- <br>";
                    }
                }
                else {
                    echo "ERR <br> ---- <br>";
                } 
            //}        
        }
        
        echo "OK!";
    }     
   */  
    
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
            $rowDada = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row,NULL,TRUE,FALSE);
            
            if($row == 1) {
                continue;
            }
            if(empty($rowDada[0][0]) {
                break;
            }
            
            $certificates = Certificates::findOne($rowDada[0][0]);
            $certificates->soname = $rowDada[0][1];
            $certificates->name = $rowDada[0][2];
            $certificates->phname = $rowDada[0][3];
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
            $rowDada = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row,NULL,TRUE,FALSE);
            
            if($row == 1) {
                continue;
            }
            
            if(empty($rowDada[0][0]) {
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
