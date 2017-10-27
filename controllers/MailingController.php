<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 27.10.2017
 * Time: 17:04
 */

namespace app\controllers;


use app\models\mailing\services\MailingBuilder;
use yii\web\Controller;

class MailingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

        ];
    }

    public function actionIndex()
    {


    }

    public function actionView($id)
    {

    }

    public function actionCreate()
    {
        $builder = MailingBuilder::getBuilderWithOperator(\Yii::$app->user->identity->operator);
        if (\Yii::$app->request->isPost
            && $builder->load(\Yii::$app->request->post())
            && $builder->validate()
        ) {

        }

        return $this->render('create', ['model' => $builder]);
    }

}
