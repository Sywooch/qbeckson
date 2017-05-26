<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;

/**
 * Class FileStorageController
 * @package backend\controllers
 */
class FileStorageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['delete']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'upload' => [
                'class' => UploadAction::class,
                'deleteRoute' => 'delete'
            ],
            'delete' => [
                'class' => DeleteAction::class
            ],
        ];
    }
}
