<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 27.10.2017
 * Time: 17:04
 */

namespace app\controllers;


use app\models\mailing\activeRecord\MailingList;
use app\models\mailing\decorators\MailingListDecorator;
use app\models\mailing\services\MailingBuilder;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        $model = $this->findModelMailingListDecorated($id);

        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate()
    {
        $builder = MailingBuilder::getBuilderWithOperator(\Yii::$app->user->identity->operator);
        if (\Yii::$app->request->isPost
            && $builder->load(\Yii::$app->request->post())
            && $builder->validate()
            && $builder->save()
        ) {

        }

        return $this->render('create', ['model' => $builder]);
    }

    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return MailingListDecorator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelMailingListDecorated($id)
    {
        if (($model = MailingList::findOne($id)) !== null) {
            return MailingListDecorator::decorate($model);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
