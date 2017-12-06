<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

/**
 * Class RbacController
 * @package console\controllers
 */
class RbacController extends Controller
{
    public function actionCreateViewPermissions()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        $rule = new \app\rbac\ViewContractRule();
        $auth->add($rule);
        $viewContract = $auth->createPermission('viewContract');
        $viewContract->description = 'Права на просмотр договора';
        $viewContract->ruleName = $rule->name;
        $auth->add($viewContract);

        $rule = new \app\rbac\ViewCertificateRule();
        $auth->add($rule);
        $viewCertificate = $auth->createPermission('viewCertificate');
        $viewCertificate->description = 'Права на просмотр сертификата';
        $viewCertificate->ruleName = $rule->name;
        $auth->add($viewCertificate);

        $rule = new \app\rbac\ViewProgrammeRule();
        $auth->add($rule);
        $viewProgramme = $auth->createPermission('viewProgramme');
        $viewProgramme->description = 'Права на просмотр программы';
        $viewProgramme->ruleName = $rule->name;
        $auth->add($viewProgramme);

        $roles = $auth->getRoles();
        foreach ($roles as $role) {
            $auth->addChild($role, $viewContract);
            $auth->addChild($role, $viewProgramme);
            $auth->addChild($role, $viewCertificate);
        }
    }
}
