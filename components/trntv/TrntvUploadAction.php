<?php

namespace app\components\trntv;

use trntv\filekit\actions\UploadAction;
use League\Flysystem\FilesystemInterface;
use trntv\filekit\events\UploadEvent;
use League\Flysystem\File as FlysystemFile;
use yii\base\DynamicModel;
use yii\di\Instance;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Overrides class of trntv\filekit\actions\UploadAction to save file with name you wish.
 */
class TrntvUploadAction extends UploadAction
{
    /**
     * @return array
     * @throws \HttpException
     */
    public function run()
    {
        $result = [];
        $uploadedFiles = UploadedFile::getInstancesByName($this->fileparam);

        foreach ($uploadedFiles as $uploadedFile) {
            /* @var \yii\web\UploadedFile $uploadedFile */
            $output = [
                $this->responseNameParam => Html::encode($uploadedFile->name),
                $this->responseMimeTypeParam => $uploadedFile->type,
                $this->responseSizeParam => $uploadedFile->size,
                $this->responseBaseUrlParam =>  $this->getFileStorage()->baseUrl
            ];

            if ($uploadedFile->error === UPLOAD_ERR_OK) {
                $validationModel = DynamicModel::validateData(['file' => $uploadedFile], $this->validationRules);

                if (!$validationModel->hasErrors()) {
                    $fileNamePostfix = $this->fileparam == '_fileinput_w1' ? 'dogovor' : 'dogsumma';

                    if (\Yii::$app->user->can('operators')) {
                        $tempPath = preg_replace('/^(.*?)([^\\/]*$)/', '\1' . \Yii::$app->user->identity->username . $fileNamePostfix . '.' . $uploadedFile->getExtension(), $uploadedFile->tempName);
                        $uploadedFile->saveAs($tempPath);
                        $uploadedFile->tempName = $tempPath;
                        $uploadedFile->name = \Yii::$app->user->identity->username . $fileNamePostfix . '.' . $uploadedFile->getExtension();
                    }

                    $path = $this->getFileStorage()->save($uploadedFile, true, true);

                    if ($path) {
                        $output[$this->responsePathParam] = $path;
                        $output[$this->responseUrlParam] = $this->getFileStorage()->baseUrl . '/' . $path;
                        $output[$this->responseDeleteUrlParam] = Url::to([$this->deleteRoute, 'path' => $path]);
                        $paths = \Yii::$app->session->get($this->sessionKey, []);
                        $paths[] = $path;
                        \Yii::$app->session->set($this->sessionKey, $paths);
                        $this->afterSave($path);

                    } else {
                        $output['error'] = true;
                        $output['errors'] = [];
                    }

                } else {
                    $output['error'] = true;
                    $output['errors'] = $validationModel->errors;
                }
            } else {
                $output['error'] = true;
                $output['errors'] = $this->resolveErrorMessage($uploadedFile->error);
            }

            $result['files'][] = $output;
        }

        return $this->multiple ? $result : array_shift($result);
    }

    /**
     * @param $path
     */
    public function afterSave($path)
    {
        $file = null;
        $fs = $this->getFileStorage()->getFilesystem();
        if ($fs instanceof FilesystemInterface) {
            $file = new FlysystemFile($fs, $path);
        }
        $this->trigger(self::EVENT_AFTER_SAVE, new UploadEvent([
            'path' => $path,
            'filesystem' => $fs,
            'file' => $file
        ]));
    }

    /**
     * @return TrntvStorage
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFileStorage()
    {
        $fileStorage = 'contractFileStorage';
        $fileStorage = Instance::ensure($fileStorage, TrntvStorage::className());

        return $fileStorage;
    }
}