<?php

namespace app\controllers;

use app\models\SettingsSearchFilters;
use app\models\UserSearchFiltersAssignment;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\helpers\PermissionHelper;

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
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_DEV ? 'test' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(PermissionHelper::redirectUrlByRole());
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionHelp()
    {
        if (!Yii::$app->user->isGuest) {
            $role = array_shift(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
            if (!empty($role) && !empty($role->data)) {
                return $this->render('help', ['helpText' => $role->data]);
            }
        }

        return $this->render('no-video');
    }

    public function actionSaveFilter()
    {
        $post = Yii::$app->request->post('UserSearchFiltersAssignment');
        $filter = SettingsSearchFilters::findOne($post['filter_id']);
        $model = UserSearchFiltersAssignment::findByFilter($filter);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->columns = $post['columns'];
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Настройки поиска; успешно сохранены.');
        } else {
            Yii::$app->session->setFlash('danger', 'Ошибка при сохранении настроек поиска.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
