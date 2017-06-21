<?php

namespace app\controllers;

use yii\web\Controller;

/**
 * Class MaintenanceController
 * @package app\controllers
 */
class MaintenanceController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
