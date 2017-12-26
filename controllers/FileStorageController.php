<?php

namespace app\controllers;

use app\components\trntv\TrntvUploadAction;
use Intervention\Image\ImageManager;
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
            'contract-delete-application' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'delete',
                'validationRules' => [
                    [['file'], 'file', 'extensions' => 'jpg, jpeg, png, gif, pdf', 'maxSize' => 3145728/* 3mb */]
                ]
            ],
            'contract-upload' => [
                'class' => TrntvUploadAction::class,
                'deleteRoute' => 'delete'
            ],
            'upload' => [
                'class' => UploadAction::class,
                'deleteRoute' => 'delete'
            ],
            'delete' => [
                'class' => DeleteAction::class
            ],
            'program-upload' => [
                'class' => UploadAction::class,
                'deleteRoute' => 'program-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $imageManager = new ImageManager();
                    $img = $imageManager->make($file->read())->fit(700);
                    $file->put($img->encode());
                }
            ],
            'program-delete' => [
                'class' => DeleteAction::class
            ]
        ];
    }
}
