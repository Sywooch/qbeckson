<?php

namespace app\models\programs;

use app\models\Informs;
use app\models\Programs;
use app\models\UserIdentity;
use Yii;

class ProgramsVerificator extends ProgramsActions
{

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        return (
                $this->setLimits() && $this->setStateDone() && $this->createInformer()
            )
            || $transactionTerminator();
    }

    private function setLimits()
    {
        if ($this->program->directivity == 'Техническая (робототехника)') {
            $this->program->limit = Yii::$app->coefficient->data->blimrob * $this->program->year;
        } elseif ($this->program->directivity == 'Техническая (иная)') {
            $this->program->limit = Yii::$app->coefficient->data->blimtex * $this->program->year;
        } elseif ($this->program->directivity == 'Естественнонаучная') {
            $this->program->limit = Yii::$app->coefficient->data->blimest * $this->program->year;
        } elseif ($this->program->directivity == 'Физкультурно-спортивная') {
            $this->program->limit = Yii::$app->coefficient->data->blimfiz * $this->program->year;
        } elseif ($this->program->directivity == 'Художественная') {
            $this->program->limit = Yii::$app->coefficient->data->blimxud * $this->program->year;
        } elseif ($this->program->directivity == 'Туристско-краеведческая') {
            $this->program->limit = Yii::$app->coefficient->data->blimtur * $this->program->year;
        } elseif ($this->program->directivity == 'Социально-педагогическая') {
            $this->program->limit = Yii::$app->coefficient->data->blimsoc * $this->program->year;
        } else {
            return false;
        }

        return true;
    }

    private function setStateDone()
    {
        $this->program->verification = Programs::VERIFICATION_DONE;

        return true;
    }

    private function createInformer()
    {
        $informs = new Informs();
        $informs->program_id = $this->program->id;
        $informs->prof_id = $this->program->organization_id;
        $informs->text = 'Сертифицированна программа';
        $informs->from = UserIdentity::ROLE_OPERATOR_ID;
        $informs->date = date("Y-m-d");
        $informs->read = 0;

        return $informs->save();
    }

}
