<?php

namespace app\models\forms;

use app\models\ContractDeleteApplication;
use app\models\Contracts;
use yii\base\Model;

/**
 * Class ContractDeleteApplicationForm
 * @package app\models\forms
 */
class ContractDeleteApplicationForm extends Model
{
    public $contractId;
    public $appId;
    public $status;
    public $captcha;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['contractId', 'status', 'captcha', 'appId'], 'required'],
            [['contractId', 'status', 'appId'], 'integer'],
            ['captcha', 'captcha'],
            [
                ['contractId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Contracts::className(),
                'targetAttribute' => 'id'
            ],
            [
                ['appId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ContractDeleteApplication::className(),
                'targetAttribute' => 'id'
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'contractId' => 'ID договора',
            'status' => 'Статус',
            'captcha' => 'Код с картинки',
        ];
    }
}
