<?php

namespace app\components\widgets\ContractPayDetails;

use yii\base\Widget;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;

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
        return Collapse::widget(['items' => [
            ['label' => $this->title,
                'content' => $content,]]
        ]);
    }

}
