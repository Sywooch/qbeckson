<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 27.10.2017
 * Time: 17:04
 */

namespace app\controllers;


use app\models\mailing\repository\MailingListWithTasks;
use app\models\mailing\repository\MailingListWithTasksSearch;
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

        $searchModel = new MailingListWithTasksSearch();
        $searchModel->created_by = \Yii::$app->user->id;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionView($id)
    {
        $model = $this->findModelMailingListWithTask($id);
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
            return $this->redirect(['view', 'id' => $builder->mailingListId]);
        }

        return $this->render('create', ['model' => $builder]);
    }

    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return MailingListWithTasks
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelMailingListWithTask($id)
    {
        if (($model = MailingListWithTasks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
