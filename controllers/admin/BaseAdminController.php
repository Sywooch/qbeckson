<?php

namespace app\controllers\admin;

use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Class BaseAdminController
 * @package app\controllers\admin
 */
class BaseAdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
}
