<?php

namespace app\models;

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
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'actual', 'type', 'license_number', 'bank_bik', 'korr_invoice', 'max_child', 'amount_child', 'inn', 'KPP', 'OGRN', 'okopo', 'certprogram'], 'integer'],
            [['name', 'license_date', 'license_issued', 'bank_name', 'bank_sity', 'rass_invoice', 'fio', 'position', 'address_legal', 'address_actual', 'geocode', 'raiting', 'ground', 'orgtype'], 'safe'],
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
                'pagesize' => 50,
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'actual' => $this->actual,
            'type' => $this->type,
            'license_date' => $this->license_date,
            'license_number' => $this->license_number,
            'bank_bik' => $this->bank_bik,
            'korr_invoice' => $this->korr_invoice,
            'max_child' => $this->max_child,
            'amount_child' => $this->amount_child,
            'inn' => $this->inn,
            'KPP' => $this->KPP,
            'OGRN' => $this->OGRN,
            'okopo' => $this->okopo,
            'certprogram' => $this->certprogram,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'license_issued', $this->license_issued])
            ->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'bank_sity', $this->bank_sity])
            ->andFilterWhere(['like', 'rass_invoice', $this->rass_invoice])
            ->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'geocode', $this->geocode])
            ->andFilterWhere(['like', 'raiting', $this->raiting])
            ->andFilterWhere(['like', 'type', $this->orgtype])
            ->andFilterWhere(['like', 'ground', $this->ground]);

        return $dataProvider;
    }
}
