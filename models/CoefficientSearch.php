<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Coefficient;

/**
 * CoefficientSearch represents the model behind the search form about `app\models\Coefficient`.
 */
class CoefficientSearch extends Coefficient
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'p21v', 'p21s', 'p21o', 'p22v', 'p22s', 'p22o', 'p3v', 'p3s', 'p3n', 'weekyear', 'weekmonth', 'pk', 'norm', 'potenc', 'ngr', 'sgr', 'vgr', 'chr1', 'zmr1', 'chr2', 'zmr2', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc', 'ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppzm1', 'ppchr2', 'ppzm2', 'ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd'], 'integer'],
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
        $query = Coefficient::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'p21v' => $this->p21v,
            'p21s' => $this->p21s,
            'p21o' => $this->p21o,
            'p22v' => $this->p22v,
            'p22s' => $this->p22s,
            'p22o' => $this->p22o,
            'p3v' => $this->p3v,
            'p3s' => $this->p3s,
            'p3n' => $this->p3n,
            'weekyear' => $this->weekyear,
            'weekmonth' => $this->weekmonth,
            'pk' => $this->pk,
            'norm' => $this->norm,
            'potenc' => $this->potenc,
            'ngr' => $this->ngr,
            'sgr' => $this->sgr,
            'vgr' => $this->vgr,
            'chr1' => $this->chr1,
            'zmr1' => $this->zmr1,
            'chr2' => $this->chr2,
            'zmr2' => $this->zmr2,
            'blimrob' => $this->blimrob,
            'blimtex' => $this->blimtex,
            'blimest' => $this->blimest,
            'blimfiz' => $this->blimfiz,
            'blimxud' => $this->blimxud,
            'blimtur' => $this->blimtur,
            'blimsoc' => $this->blimsoc,
            'ngrp' => $this->ngrp,
            'sgrp' => $this->sgrp,
            'vgrp' => $this->vgrp,
            'ppchr1' => $this->ppchr1,
            'ppzm1' => $this->ppzm1,
            'ppchr2' => $this->ppchr2,
            'ppzm2' => $this->ppzm2,
            'ocsootv' => $this->ocsootv,
            'ocku' => $this->ocku,
            'ocmt' => $this->ocmt,
            'obsh' => $this->obsh,
            'ktob' => $this->ktob,
            'vgs' => $this->vgs,
            'sgs' => $this->sgs,
            'pchsrd' => $this->pchsrd,
            'pzmsrd' => $this->pzmsrd,
        ]);

        return $dataProvider;
    }
}
