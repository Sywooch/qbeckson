<?php

namespace app\components\widgets\ContractPayDetails;

use yii\base\Widget;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

class ContractPayDetails extends Widget
{

    public $query = null;
    public $title = 'Транзакции';

    public function run()
    {
        $dataProvider = new ActiveDataProvider(['query' => $this->query, 'pagination' => ['pageSize' => 10]]);
        $table = $this->render('_table', ['dataProvider' => $dataProvider]);

        return $this->collapsed($table);
    }

    private function collapsed($content)
    {
        $icoCaret =
            Html::tag(
                'span',
                null,
                ['class' => ['glyphicon', 'glyphicon-chevron-down', 'pull-right']]
            );
        $icoList =
            Html::tag(
                'span',
                null,
                ['class' => ['glyphicon', 'glyphicon-list']]
            );
        $title = Html::tag(
            'div',
            $icoList . ' ' . $this->title . $icoCaret,
            ['style' => ['display' => 'block']]
        );

        return Collapse::widget(
            ['items' => [
                [
                    'label' => $title,
                    'content' => $content,
                ]
            ],
                'encodeLabels' => false,
            ]
        );
    }

}
