<?php

namespace app\models\siteRestriction;

/**
 * политика запрета доступа к сайту
 */
class SiteRestrictionPolicy
{
    /**
     * запрет доступа к сайту
     *
     * @var SiteRestriction
     */
    private $siteRestriction;

    /**
     * запрещен ли доступ к сайту
     *
     * @return bool
     */
    public function isRestricted()
    {
        if (\Yii::$app->user->isGuest) {
            return false;
        }

        if (\Yii::$app->user->can('admins')) {
            return false;
        }

        if (!$site_restriction = $this->getSiteRestriction()) {
            return false;
        }

        if (SiteRestrictionStatus::ACTIVE != $site_restriction->status) {
            return false;
        }

        if (SiteRestrictionType::ALWAYS == $site_restriction->type) {
            return true;
        }

        if (SiteRestrictionType::CRON == $site_restriction->type && SiteRestrictionCronStatus::isActive()) {
            return true;
        }

        return false;
    }

    /**
     * получить сообщение запрета доступа к сайту
     *
     * @return string|null
     */
    public function getMessage()
    {
        if (!$siteRestriction = $this->getSiteRestriction()) {
            return null;
        }

        return $siteRestriction->message;
    }

    /**
     * получить запрет доступа к сайту
     *
     * @return SiteRestriction|null
     */
    private function getSiteRestriction()
    {
        if (!$this->siteRestriction) {
            $this->siteRestriction = SiteRestriction::find()->one();
        }

        return $this->siteRestriction;
    }
}