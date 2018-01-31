<?php

namespace app\components;

use app\models\siteRestriction\SiteRestrictionPolicy;
use yii\base\Component;
use yii\web\ForbiddenHttpException;

/**
 * компонент для проверки запрета доступа к сайту
 */
class SiteRestrictionComponent extends Component
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $siteRestrictionPolicy = new SiteRestrictionPolicy;

        if ($siteRestrictionPolicy->isRestricted()) {
            throw new ForbiddenHttpException($siteRestrictionPolicy->getMessage());
        }
    }
}