<?php

namespace app\models\search;

use app\models\UserIdentity;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Organization;

/**
 * OrganizationSearch represents the model behind the search form about `app\models\Organization`.
 */
class OrganizationSearch extends Organization
{
    public $orgtype;

    public $certprogram;

    public $statusArray = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'actual', 'type', 'license_number', 'bank_bik', 'korr_invoice', 'max_child', 'amount_child', 'inn', 'KPP', 'OGRN', 'okopo', 'certprogram'], 'integer'],
            [['name', 'license_date', 'license_issued', 'bank_name', 'bank_sity', 'rass_invoice', 'fio', 'position', 'address_legal', 'address_actual', 'geocode', 'raiting', 'ground', 'orgtype', 'statusArray'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Organization::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'orgtype' => [
                    'asc' => ['type' => SORT_ASC],
                    'desc' => ['type' => SORT_DESC],
                    'label' => 'Тип организации',
                    'default' => SORT_ASC
                ],
                'certprogram' => [
                    'asc' => ['certprogram' => SORT_ASC],
                    'desc' => ['certprogram' => SORT_DESC],
                    'label' => 'Число программ',
                    'default' => SORT_ASC
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'organization.id' => $this->id,
            'organization.user_id' => $this->user_id,
            'organization.actual' => $this->actual,
            'organization.type' => $this->type,
            'organization.license_date' => $this->license_date,
            'organization.license_number' => $this->license_number,
            'organization.bank_bik' => $this->bank_bik,
            'organization.korr_invoice' => $this->korr_invoice,
            'organization.max_child' => $this->max_child,
            'organization.amount_child' => $this->amount_child,
            'organization.inn' => $this->inn,
            'organization.KPP' => $this->KPP,
            'organization.OGRN' => $this->OGRN,
            'organization.okopo' => $this->okopo,
            'organization.certprogram' => $this->certprogram,
            'organization.status' => $this->statusArray,
        ]);

        $query
            ->andFilterWhere(['like', 'organization.name', $this->name])
            ->andFilterWhere(['like', 'organization.license_issued', $this->license_issued])
            ->andFilterWhere(['like', 'organization.bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'organization.bank_sity', $this->bank_sity])
            ->andFilterWhere(['like', 'organization.rass_invoice', $this->rass_invoice])
            ->andFilterWhere(['like', 'organization.fio', $this->fio])
            ->andFilterWhere(['like', 'organization.position', $this->position])
            ->andFilterWhere(['like', 'organization.address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'organization.address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'organization.geocode', $this->geocode])
            ->andFilterWhere(['like', 'organization.raiting', $this->raiting])
            ->andFilterWhere(['like', 'organization.type', $this->orgtype])
            ->andFilterWhere(['like', 'organization.ground', $this->ground]);

        if (Yii::$app->user->can('certificate')) {
            /** @var UserIdentity $identity */
            $identity = Yii::$app->user->getIdentity();
            if (null !== $identity->mun_id) {
                $query->joinWith(['programs'])->groupBy(['organization.id']);
                $query->andFilterWhere([
                    'OR',
                    ['organization.mun' => $identity->mun_id],
                    [
                        'AND',
                        ['programs.mun' => $identity->mun_id],
                        ['programs.verification' => 2],
                    ]
                ]);
            }
        }

        return $dataProvider;
    }
}
