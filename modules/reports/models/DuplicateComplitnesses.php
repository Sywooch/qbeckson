<?php

namespace app\modules\reports\models;

use app\helpers\GridviewHelper;
use app\models\Completeness;
use app\models\Contracts;
use app\models\Mun;
use app\models\search\ContractsSearch;
use app\widgets\SearchFilter;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * DuplicateComplitnesses represents the model behind the search form about `app\models\Contracts`.
 */
class DuplicateComplitnesses extends ContractsSearch
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id', 'certificate_id', 'payer_id',
                    'program_id', 'year_id', 'organization_id',
                    'group_id', 'status', 'status_year', 'funds_gone',
                    'sposob', 'prodolj_d', 'prodolj_m',
                    'prodolj_m_user', 'ocen_fact', 'ocen_kadr',
                    'ocen_mat', 'ocen_obch', 'ocenka', 'wait_termnate',
                    'terminator_user', 'payment_order', 'period',
                    'cooperate_id', 'creation_status'
                ], 'integer'
            ],
            [
                ['number', 'date', 'status_termination',
                    'status_comment', 'link_doc', 'link_ofer',
                    'start_edu_programm', 'stop_edu_contract',
                    'start_edu_contract', 'change1', 'change2',
                    'change_org_fio', 'org_position', 'org_position_min',
                    'change_doctype', 'change_fioparent', 'change6',
                    'change_fiochild', 'change8', 'change9', 'change10',
                    'date_termnate', 'url', 'created_at', 'requested_at',
                    'refused_at', 'accepted_at', 'activated_at', 'termination_initiated_at'
                ], 'safe'
            ],
            [
                [
                    'all_funds', 'funds_cert', 'all_parents_funds',
                    'first_m_price', 'other_m_price', 'first_m_nprice',
                    'other_m_nprice', 'cert_dol', 'payer_dol', 'rezerv',
                    'paid', 'fontsize', 'parents_first_month_payment',
                    'parents_other_month_payment', 'payer_first_month_payment',
                    'payer_other_month_payment', 'balance'
                ], 'number'
            ],
        ];
    }

    public function getColumns($all = false)
    {
        $number = [
            'attribute' => 'number',
        ];
        $date = [
            'attribute' => 'date',
            'format' => 'date',
        ];
        $rezerv = [
            'attribute' => 'rezerv',
            'label' => 'Резерв',
            'type' => SearchFilter::TYPE_RANGE_SLIDER,
        ];
        $programMunicipality = [
            'attribute' => 'programMunicipality',
            'label' => 'Муниципалитет',
            'value' => function ($model) {
                /** @var \app\models\Contracts $model */
                return Html::a(
                    $model->program->municipality->name,
                    ['mun/view', 'id' => $model->program->municipality->id],
                    ['target' => '_blank', 'data-pjax' => '0']
                );
            },
            'format' => 'raw',
            'type' => SearchFilter::TYPE_DROPDOWN,
            'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
        ];
        $childFullName = [
            'attribute' => 'childFullName',
            'value' => 'certificate.fio_child',
            'label' => 'ФИО ребёнка'
        ];
        $moduleName = [
            'attribute' => 'moduleName',
            'value' => 'year.fullname',
            'label' => 'Модуль'
        ];
        $certificateNumber = [
            'attribute' => 'certificateNumber',
            'format' => 'raw',
            'label' => 'Сертификат',
            'value' => function ($data) {
                return Html::a(
                    $data->certificate->number,
                    Url::to(['certificates/view', 'id' => $data->certificate->id]),
                    ['target' => '_blank', 'data-pjax' => '0']
                );
            }
        ];
        $programName = [
            'attribute' => 'programName',
            'label' => 'Программа',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a(
                    $data->program->name,
                    Url::to(['programs/view', 'id' => $data->program->id]),
                    ['target' => '_blank', 'data-pjax' => '0']
                );
            },
        ];
        $organizationName = [
            'attribute' => 'organizationName',
            'label' => 'Организация',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a(
                    $data->organization->name,
                    Url::to(['/organization/view', 'id' => $data->organization->id]),
                    ['target' => '_blank', 'data-pjax' => '0']
                );
            },
        ];
        $payerName = [
            'attribute' => 'payerName',
            'label' => 'Плательщик',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a(
                    $data->payers->name,
                    Url::to(['payers/view', 'id' => $data->payer->id]),
                    ['target' => '_blank', 'data-pjax' => '0']
                );
            }
        ];
        $paid = [
            'attribute' => 'paid',
            'type' => SearchFilter::TYPE_RANGE_SLIDER,
        ];
        $actions = [
            'class' => ActionColumn::class,
            'controller' => '/contracts',
            'template' => '{view}',
            'searchFilter' => false,
        ];
        $columns = [
            $number,
            $date,
            $rezerv,
            $paid,
            $programMunicipality,
            $childFullName,
            $moduleName,
            $certificateNumber,
            $programName,
            $organizationName,
            $payerName,
            $actions,
        ];

        return GridviewHelper::prepareColumns(
            'contracts',
            $columns,
            'r-dupl-compl',
            $all ? null : 'searchFilter'
        );

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
    public function search($params, $pageSize = 50)
    {
        $query = Contracts::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => false,
                'pageSize' => $pageSize,
            ]
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
            'date' => $this->date,
            'certificate_id' => $this->certificate_id,
            'payer_id' => $this->payer_id,
            'program_id' => $this->program_id,
            'year_id' => $this->year_id,
            'organization_id' => $this->organization_id,
            'group_id' => $this->group_id,
            'status' => $this->status,
            'status_termination' => $this->status_termination,
            'status_year' => $this->status_year,
            'all_funds' => $this->all_funds,
            'funds_cert' => $this->funds_cert,
            'all_parents_funds' => $this->all_parents_funds,
            'start_edu_programm' => $this->start_edu_programm,
            'funds_gone' => $this->funds_gone,
            'stop_edu_contract' => $this->stop_edu_contract,
            'start_edu_contract' => $this->start_edu_contract,
            'sposob' => $this->sposob,
            'prodolj_d' => $this->prodolj_d,
            'prodolj_m' => $this->prodolj_m,
            'prodolj_m_user' => $this->prodolj_m_user,
            'first_m_price' => $this->first_m_price,
            'other_m_price' => $this->other_m_price,
            'first_m_nprice' => $this->first_m_nprice,
            'other_m_nprice' => $this->other_m_nprice,
            'ocen_fact' => $this->ocen_fact,
            'ocen_kadr' => $this->ocen_kadr,
            'ocen_mat' => $this->ocen_mat,
            'ocen_obch' => $this->ocen_obch,
            'ocenka' => $this->ocenka,
            'wait_termnate' => $this->wait_termnate,
            'date_termnate' => $this->date_termnate,
            'cert_dol' => $this->cert_dol,
            'payer_dol' => $this->payer_dol,
            'rezerv' => $this->rezerv,
            'paid' => $this->paid,
            'terminator_user' => $this->terminator_user,
            'fontsize' => $this->fontsize,
            'parents_first_month_payment' => $this->parents_first_month_payment,
            'parents_other_month_payment' => $this->parents_other_month_payment,
            'payer_first_month_payment' => $this->payer_first_month_payment,
            'payer_other_month_payment' => $this->payer_other_month_payment,
            'payment_order' => $this->payment_order,
            'balance' => $this->balance,
            'period' => $this->period,
            'cooperate_id' => $this->cooperate_id,
            'created_at' => $this->created_at,
            'requested_at' => $this->requested_at,
            'refused_at' => $this->refused_at,
            'accepted_at' => $this->accepted_at,
            'activated_at' => $this->activated_at,
            'termination_initiated_at' => $this->termination_initiated_at,
            'creation_status' => $this->creation_status,
        ]);

        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'status_comment', $this->status_comment])
            ->andFilterWhere(['like', 'link_doc', $this->link_doc])
            ->andFilterWhere(['like', 'link_ofer', $this->link_ofer])
            ->andFilterWhere(['like', 'change1', $this->change1])
            ->andFilterWhere(['like', 'change2', $this->change2])
            ->andFilterWhere(['like', 'change_org_fio', $this->change_org_fio])
            ->andFilterWhere(['like', 'org_position', $this->org_position])
            ->andFilterWhere(['like', 'org_position_min', $this->org_position_min])
            ->andFilterWhere(['like', 'change_doctype', $this->change_doctype])
            ->andFilterWhere(['like', 'change_fioparent', $this->change_fioparent])
            ->andFilterWhere(['like', 'change6', $this->change6])
            ->andFilterWhere(['like', 'change_fiochild', $this->change_fiochild])
            ->andFilterWhere(['like', 'change8', $this->change8])
            ->andFilterWhere(['like', 'change9', $this->change9])
            ->andFilterWhere(['like', 'change10', $this->change10])
            ->andFilterWhere(['like', 'url', $this->url]);

        $this->applyASpeciallyCondition($query);

        return $dataProvider;
    }

    private function applyASpeciallyCondition(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere([
            'id' => Completeness::find()
                ->select('max(' . Completeness::tableName() . '.[[contract_id]])')
                ->groupBy([
                    'contract_id',
                    'month',
                    'year',
                    'preinvoice',
                ])->having('count([[contract_id]]) > 1')
        ]);
    }
}
