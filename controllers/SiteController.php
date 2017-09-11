<?php

namespace app\controllers;

use yii\base\Model;
use app\models\CertificateInformation;
use app\models\Help;
use app\models\Mun;
use app\models\search\HelpSearch;
use app\models\SettingsSearchFilters;
use app\models\UserSearchFiltersAssignment;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\helpers\PermissionHelper;
use yii\web\NotFoundHttpException;

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

    /**
     * @param $municipalityId
     * @return string
     */
    public function actionInformation($municipalityId)
    {
        $result = CertificateInformation::findOneByMunicipality($municipalityId);

        return $this->render('information', [
            'result' => $result,
        ]);
    }

    public function actionTestMail()
    {
        /*$message = "Line 1\r\nLine 2\r\nLine 3";
        $message = wordwrap($message, 70, "\r\n");
        var_dump(mail('nauly@mail.ru', 'My Subject', $message));
        exit;*/


        var_dump(Yii::$app->mailer->compose()
            ->setTo('nauly@mail.ru')
            ->setFrom(['support@pfdo.ru' => 'PFDO'])
            ->setSubject('Сообщение с pfdo')
            ->setTextBody('TEST mail from pfdo')
            ->setHtmlBody('<b>HTML content</b>')
            ->send());
        exit;
    }

    public function actionManualsRequired()
    {
        $userRoles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        $searchModel = new HelpSearch(['role' => array_shift($userRoles)]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $models = $dataProvider->models;
        foreach ($models as $model) {
            $model->scenario = Help::SCENARIO_CHECK;
            $model->getCheckes();
        }

        if (Model::loadMultiple($models, Yii::$app->request->post()) && Model::validateMultiple($models)) {
            foreach ($models as $model) {
                $model->saveCheckes();
            }

            return $this->redirect('index');
        }

        return $this->render('manuals-required', [
            'models' => $models,
        ]);
    }

    public function actionManual($id)
    {
        if (!$model = Help::findOne($id)) {
            throw new NotFoundHttpException('Такой страницы не существует.');
        }

        return $this->render('manual', [
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(PermissionHelper::redirectUrlByRole());
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Переключаем identity, если пользователь создан как монитор
            if (!empty(Yii::$app->user->identity->donor) && Yii::$app->user->can('monitor')) {
                Yii::$app->session->set('user.monitor', Yii::$app->user->identity);
                Yii::$app->user->switchIdentity(Yii::$app->user->identity->donor);
            }

            return $this->refresh();
        }

        $municipalities = Mun::find()
            ->andWhere([
                'operator_id' => Yii::$app->operator->identity->id
            ])
            ->all();

        return $this->render('index', [
            'model' => $model,
            'municipalities' => $municipalities
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
