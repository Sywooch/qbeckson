<?php

namespace app\models;

use app\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "payers".
 *
 * @property integer                $id
 * @property integer                $user_id
 * @property string                 $name
 * @property integer                $OGRN
 * @property integer                $INN
 * @property integer                $KPP
 * @property integer                $OKPO
 * @property string                 $address_legal
 * @property string                 $address_actual
 * @property string                 $phone
 * @property string                 $email
 * @property string                 $position
 * @property string                 $fio
 * @property integer                $mun
 * @property string                 $directionality
 * @property integer                $directionality_1_count
 * @property integer                $directionality_2_count
 * @property integer                $directionality_3_count
 * @property integer                $directionality_4_count
 * @property integer                $directionality_5_count
 * @property integer                $directionality_6_count
 * @property integer                $certificate_can_use_future_balance
 * @property bool                   $certificate_can_create_contract        разрешено ли сертификату создавать контракт
 * @property string                 $certificate_cant_create_contract_at    сертификату запрещается создавать контракт с указанной даты и времени
 * @property int                    $operator_id
 * @property string                 $code
 * @property string                 $name_dat
 * @property int                    $directionality_1rob_count
 * @property int                    $days_to_first_contract_request         кол-во дней для создания первой заявки после создания сертификата или перевод в тип ПФ
 * @property int                    $days_to_contract_request_after_refused кол-во дней для создания новой заявки после отклонения предыдущей
 *
 * @property Certificates[]         $certificates
 * @property Invoices[]             $invoices
 * @property mixed                  $payer
 * @property mixed                  $noCooperatePayer
 * @property Mun                    $municipality
 * @property Cooperate[]            $cooperates
 * @property User                   $user
 * @property CertificateInformation $certificateInformation
 * @property CertGroup              $firstCertGroup
 * @property Contracts              $contracts
 */
class Payers extends \yii\db\ActiveRecord
{
    public $directionality_1rob;
    public $directionality_1;
    public $directionality_2;
    public $directionality_3;
    public $directionality_4;
    public $directionality_5;
    public $directionality_6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payers';
    }

    public function populateDirections()
    {
        $directionsArray = self::directionalityAttributes();
        $directionCountersArray = self::directionalityAttributes(true);
        $this->directionality = '';

        foreach ($directionsArray as $index => $value) {
            if (!empty($this->$index)) {
                if (!$this->{$directionCountersArray[$index]}) {
                    $this->{$directionCountersArray[$index]} = 1000000;
                }
                $this->directionality = $this->directionality ? join(',', [$this->directionality, $value]) : $value;
            } else {
                $this->{$directionCountersArray[$index]} = 0;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->populateDirections();

        return parent::beforeSave($insert);
    }

    /**
     * @return array
     */
    public static function directionalityAttributes($counters = false): array
    {
        return [
            'directionality_1rob' => !$counters ? 'Техническая (робототехника)' : 'directionality_1rob_count',
            'directionality_1'    => !$counters ? 'Техническая (иная)' : 'directionality_1_count',
            'directionality_2'    => !$counters ? 'Естественнонаучная' : 'directionality_2_count',
            'directionality_3'    => !$counters ? 'Физкультурно-спортивная' : 'directionality_3_count',
            'directionality_4'    => !$counters ? 'Художественная' : 'directionality_4_count',
            'directionality_5'    => !$counters ? 'Туристско-краеведческая' : 'directionality_5_count',
            'directionality_6'    => !$counters ? 'Социально-педагогическая' : 'directionality_6_count',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'name_dat', 'INN', 'OGRN', 'KPP', 'OKPO', 'address_legal', 'address_actual', 'email', 'phone', 'position', 'fio', 'code'], 'required'],
            [['user_id', 'INN', 'OGRN', 'KPP', 'OKPO', 'directionality_1rob_count', 'directionality_1_count', 'directionality_2_count', 'directionality_3_count', 'directionality_4_count', 'directionality_5_count', 'directionality_6_count', 'mun', 'certificate_can_use_future_balance', 'certificate_can_create_contract'], 'integer'],
            ['operator_id', 'integer'],
            [['code'], 'string', 'length' => [2, 2]],
            [['directionality'], 'safe'],
            [['directionality_1rob', 'directionality_1', 'directionality_2', 'directionality_3', 'directionality_4', 'directionality_5', 'directionality_6'], 'string'],
            [['email'], 'email'],
            [['name', 'name_dat', 'address_legal', 'address_actual', 'phone', 'position', 'fio'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['days_to_first_contract_request', 'integer', 'min' => 5],
            ['days_to_contract_request_after_refused', 'integer', 'min' => 10],
            ['certificate_cant_create_contract_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                                     => 'ID Плательщика',
            'user_id'                                => 'ID Пользователя',
            'name'                                   => 'Наименование',
            'name_dat'                               => 'Наименование в творительном падеже',
            'code'                                   => 'Код плательщика',
            'OGRN'                                   => 'ОГРН',
            'INN'                                    => 'ИНН',
            'KPP'                                    => 'КПП',
            'OKPO'                                   => 'ОКПО',
            'address_legal'                          => 'Адрес юридический',
            'address_actual'                         => 'Адрес фактический',
            'phone'                                  => 'Телефон',
            'email'                                  => 'Email',
            'position'                               => 'Должность ответственного лица',
            'fio'                                    => 'ФИО ответственного лица',
            'mun'                                    => 'Муниципальный район',
            'directionality'                         => 'Оплачивает направленности',
            'directionality_1rob_count'              => 'Максимальное число детей в "Технической (робототехника)" направленности',
            'directionality_1_count'                 => 'Максимальное число детей в "Технической (иная)" направленности',
            'directionality_2_count'                 => 'Максимальное число детей в "Естественнонаучной" направленности',
            'directionality_3_count'                 => 'Максимальное число детей в "Физкультурно-спортивной" направленности',
            'directionality_4_count'                 => 'Максимальное число детей в "Художественной" направленности',
            'directionality_5_count'                 => 'Максимальное число детей в "Туристско-краеведческой" направленности',
            'directionality_6_count'                 => 'Максимальное число детей в "Социально-педагогической" направленности',
            'cooperates'                             => 'Число заключенных соглашений',
            'certificates'                           => 'Число выданных сертификатов',
            'certificate_can_use_future_balance'     => 'Установите возможность заключения договоров за счет средств сертификатов, предусмотренных на период от ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' до ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to),
            'certificate_can_create_contract'        => 'Доступно заключение договоров на текущий период:  от ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) . ' по ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to),
            'certificate_cant_create_contract_at'    => 'Дата и время с которой начинает действовать запрет на заключение договоров',
            'days_to_first_contract_request'         => 'Количество дней, предусмотренное для создания первой заявки после создания сертификата или его перевода в тип "сертификат ПФ"',
            'days_to_contract_request_after_refused' => 'Количество дней, предусмотренное для создания новой заявки после неудачной попытки заключения предыдущего договора',
        ];
    }

    /**
     * Better to use repository.
     * @return null|Cooperate
     */
    public function findUnconfirmedCooperates()
    {
        return $this->hasMany(Cooperate::class, ['payer_id' => 'id'])
            ->orWhere([
                'AND',
                ['cooperate.status' => Cooperate::STATUS_NEW],
                ['<', 'cooperate.created_date', date('Y-m-d H:i:s', strtotime('-3 day'))]
            ])
            ->orWhere([
                'AND',
                ['cooperate.status' => Cooperate::STATUS_CONFIRMED],
                ['<', 'cooperate.created_date', date('Y-m-d H:i:s', strtotime('-11 day'))],
                ['is not', 'cooperate.number', null],
                ['is not', 'cooperate.date', null]
            ])
            ->all();
    }

    /**
     * @param null $status - статус соглашения
     *
     * @return Cooperate
     */
    public function getCooperation($status = null)
    {
        return $this->hasOne(Cooperate::class, ['payer_id' => 'id'])
            ->andWhere(['cooperate.organization_id' => Yii::$app->user->getIdentity()->organization->id])
            ->andFilterWhere(['cooperate.status' => $status])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateInformation()
    {
        return $this->hasOne(CertificateInformation::class, ['payer_id' => 'id']);
    }

    public function munName($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['name'])
            ->from('mun')
            ->where(['id' => $data])
            ->one();

        return $rows['name'];
    }

    public function directionality1rob($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_1rob_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Техническая (робототехника)', $directionality)) {
            if ($rows['directionality_1rob_count'] > 0 && $rows['directionality_1rob_count'] < 1000000 ) {
                $display = $rows['directionality_1rob_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    public function directionality1($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_1_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Техническая (иная)', $directionality)) {
            if ($rows['directionality_1_count'] > 0 && $rows['directionality_1_count'] < 1000000) {
                $display = $rows['directionality_1_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    public function directionality2($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_2_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Естественнонаучная', $directionality)) {
            if ($rows['directionality_2_count'] > 0 && $rows['directionality_2_count'] < 1000000) {
                $display = $rows['directionality_2_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    public function directionality3($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_3_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Физкультурно-спортивная', $directionality)) {
            if ($rows['directionality_3_count'] > 0 && $rows['directionality_3_count'] < 1000000) {
                $display = $rows['directionality_3_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    public function directionality4($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_4_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Художественная', $directionality)) {
            if ($rows['directionality_4_count'] > 0 && $rows['directionality_4_count'] < 1000000) {
                $display = $rows['directionality_4_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    public function directionality5($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_5_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Туристско-краеведческая', $directionality)) {
            if ($rows['directionality_5_count'] > 0 && $rows['directionality_5_count'] < 1000000) {
                $display = $rows['directionality_5_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    public function directionality6($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['directionality', 'directionality_6_count'])
            ->from('payers')
            ->where(['id' => $data])
            ->one();
        $directionality = explode(',', $rows['directionality']);
        if (in_array('Социально-педагогическая', $directionality)) {
            if ($rows['directionality_6_count'] > 0 && $rows['directionality_6_count'] < 1000000) {
                $display = $rows['directionality_6_count'];
            } else {
                $display = 'без ограничений';
            }
        } else {
            $display = 'не оплачивает';
        }

        return $display;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificates()
    {
        return $this->hasMany(Certificates::className(), ['payer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCooperates()
    {
        return $this->hasMany(Cooperate::class, ['payer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoices::className(), ['payers_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizationPayerAssignments($organizationId = null, $status = null)
    {
        $relation = $this->hasMany(OrganizationPayerAssignment::className(), ['payer_id' => 'id'])
            ->andFilterWhere([
                'organization_id' => $organizationId,
                'status'          => $status,
            ]);

        return $relation;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizations($organizationId = null, $status = null)
    {
        $relation = $this->hasMany(Organization::className(), ['id' => 'organization_id'])->viaTable('organization_payer_assignment', ['payer_id' => 'id'], function ($query) use
        (
            $organizationId,
            $status
        ) {
            /* @var $query \yii\db\ActiveQuery */
            $query->andFilterWhere([
                'organization_id' => $organizationId,
                'status'          => $status,
            ]);
        });

        return $relation;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function findCooperateOrganizations()
    {
        $cooperates = $this->cooperates;

        return ArrayHelper::getColumn($cooperates, 'organization_id');
    }

    /**
     * @return $this
     */
    public function getFirstCertGroup()
    {
        return $this->hasOne(CertGroup::className(), ['payer_id' => 'id'])
            ->from('cert_group cg')
            ->orderBy('id');
    }

    public function getAccountingCertGroup()
    {
        return $this->hasOne(CertGroup::className(), ['payer_id' => 'id'])
            ->from('cert_group cg')
            ->where(['>', 'is_special', 0]);
    }

    public function getCertGroups($isSpecial = null)
    {
        $relation = $this->hasMany(CertGroup::className(), ['payer_id' => 'id']);

        $relation->andFilterWhere(['is_special' => $isSpecial]);

        return $relation;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipality()
    {
        return $this->hasOne(Mun::className(), ['id' => 'mun']);
    }


    /**
     * @deprecated
     * Use UserIdentity::payer instead
     */
    public function getPayer()
    {

        $query = Payers::find();

        if (!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }


    /** возвращает лимит направления по названию направления
     * @return null|int
     */
    public function getDirectionalityCountByName($name)
    {
        if ($result = array_search($name, $this::directionalityAttributes())) {
            return $this->{$result . '_count'};
        }

        return null;
    }

    /** @return  \yii\db\ActiveQuery */
    public function getContracts()
    {
        return $this->hasMany(Contracts::className(), ['payer_id' => 'id'])->inverseOf('payers');
    }

    /** @return  \yii\db\ActiveQuery */
    public function getActiveContractsByProgram($programId)
    {
        return $this->hasMany(Contracts::className(), ['payer_id' => 'id'])
            ->where([
                Contracts::tableName() . '.program_id' => $programId,
                Contracts::tableName() . '.status'     => [
                    Contracts::STATUS_REQUESTED,
                    Contracts::STATUS_ACTIVE,
                    Contracts::STATUS_ACCEPTED
                ]
            ]);
    }

    /**
     * разрешено ли менять плательщику разрешение на создание контрактов сертификатами
     *
     * @return boolean
     */
    public function canChangePermission()
    {
        $allowed = is_null($this->certificate_cant_create_contract_at) || date('Y-m-d H:i:s') <= $this->certificate_cant_create_contract_at;
        $allowed &= date('Y-m-d', strtotime('+2 Month')) >= \Yii::$app->operator->identity->settings->current_program_date_to;

        return $allowed;
    }

    /**
     * разрешено ли создавать контракт сертификату
     *
     * @return boolean
     */
    public function certificateCanCreateContract()
    {
        return $this->certificate_can_create_contract || date('Y-m-d H:i:s') <= $this->certificate_cant_create_contract_at;
    }
}
