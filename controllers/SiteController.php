<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Organization;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        // $this->layout = '@app/views/layouts/index.php';

        if (!Yii::$app->user->isGuest) {
            $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            if (isset($roles['admins'])) {
                return $this->redirect('/personal/index');
            }
            if (isset($roles['operators'])) {
                return $this->redirect('/personal/operator-statistic');
            }
            if (isset($roles['payer'])) {
                return $this->redirect('/personal/payer-statistic');
            }
            if (isset($roles['organizations'])) {
                
                $organizations = new Organization();
                $organization = $organizations->getOrganization();
                
                if ($organization->type != 4) {
                    if (empty($organization['license_issued_dat']) or empty($organization['fio']) or empty($organization['position']) or empty($organization['doc_type'])) {
                        Yii::$app->session->setFlash('warning', 'Заполните информацию "Для договора"');
                        return $this->redirect('/personal/organization-info');
                    }
                    if ($organization['doc_type'] == 1) {
                        if (empty($organization['date_proxy']) or empty($organization['number_proxy'])) {
                            Yii::$app->session->setFlash('warning', 'Заполните информацию "Для договора"');
                            return $this->redirect('/personal/organization-info');
                        }
                    }
                } /*else {
                   
                    if ($organization['doc_type'] == 1) {
                        if (empty($organization['date_proxy']) or empty($organization['number_proxy'])) {
                            Yii::$app->session->setFlash('warning', 'Заполните информацию "Для договора"');
                            return $this->redirect('/personal/organization-info');
                        }
                    }
                } */
                
                return $this->redirect('/personal/organization-statistic');
            }
            if (isset($roles['certificate'])) {
                return $this->redirect('/personal/certificate-statistic');
            }
        }
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            if ($roles['admins']) {
                return $this->redirect('/personal/index');
            }
            if ($roles['operators']) {
                return $this->redirect('/personal/operator-statistic');
            }
            if ($roles['payer']) {
                return $this->redirect('/personal/payer-statistic');
            }
            if ($roles['organizations']) {
                return $this->redirect('/personal/organization-statistic');
            }
            if ($roles['certificate']) {
                return $this->redirect('/personal/certificate-statistic');
            }

            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
