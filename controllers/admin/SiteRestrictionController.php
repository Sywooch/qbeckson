<?php

namespace app\controllers\admin;

use app\models\siteRestriction\SiteRestriction;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * контроллер управления доступом к сайту администратором
 */
class SiteRestrictionController extends Controller
{
    /**
     * отобразить запрет доступа к сайту
     */
    public function actionList()
    {
        $siteRestriction = SiteRestriction::find()->one();

        $siteRestrictionDataProvider = new ActiveDataProvider(['query' => SiteRestriction::find()]);

        return $this->render('list', ['siteRestriction' => $siteRestriction, 'siteRestrictionDataProvider' => $siteRestrictionDataProvider]);
    }

    /**
     * создать запрет доступа к сайту
     */
    public function actionCreate()
    {
        $siteRestriction = new SiteRestriction();

        if ($siteRestriction->load(\Yii::$app->request->post()) && $siteRestriction->validate()) {
            $siteRestriction->save();

            return $this->redirect(Url::to('/admin/site-restriction/list'));
        }

        return $this->render('create', ['siteRestriction' => $siteRestriction]);
    }

    /**
     * изменить запрет доступа к сайту
     *
     * @param $id - id запрета доступа к сайту
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $siteRestriction = SiteRestriction::findOne($id);

        if ($siteRestriction->load(\Yii::$app->request->post()) && $siteRestriction->validate()) {
            $siteRestriction->save();

            return $this->redirect(Url::to('/admin/site-restriction/list'));
        }

        return $this->render('update', ['siteRestriction' => $siteRestriction]);
    }
}