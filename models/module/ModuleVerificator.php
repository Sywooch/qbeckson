<?php

namespace app\models\module;

use Yii;

class ModuleVerificator extends ModuleActions
{
    public function setStateWait()
    {
        if (!$this->isPossibleToSaveTheCurrentUser()) {
            $this->module->verification = $this->module::VERIFICATION_WAIT;

            return $this->module->save(false);
        }

        return $this->throwForbidden();
    }

    public function calcNormativPrice()
    {
        if ($this->module->program->p3z == 1) {
            $p3r = 'p3v';
        }
        if ($this->module->program->p3z == 2) {
            $p3r = 'p3s';
        }
        if ($this->module->program->p3z == 3) {
            $p3r = 'p3n';
        }

        $p3 = Yii::$app->coefficient->data->$p3r;

        $mun = $this->module->program->municipality;

        if ($this->module->program->ground == 1) {
            $p5 = $mun->pc;
            $p6 = $mun->zp;
            $p12 = $mun->stav;
            $p7 = $mun->dop;
            $p8 = $mun->uvel;
            $p9 = $mun->otch;
            $p10 = $mun->otpusk;
            $p11 = $mun->polezn;
            $p4 = $mun->nopc;
            if ($this->module->program->directivity == 'Техническая (робототехника)') {
                $p1 = $mun->rob;
            }
            if ($this->module->program->directivity == 'Техническая (иная)') {
                $p1 = $mun->tex;
            }
            if ($this->module->program->directivity == 'Естественнонаучная') {
                $p1 = $mun->est;
            }
            if ($this->module->program->directivity == 'Физкультурно-спортивная') {
                $p1 = $mun->fiz;
            }
            if ($this->module->program->directivity == 'Художественная') {
                $p1 = $mun->xud;
            }
            if ($this->module->program->directivity == 'Туристско-краеведческая') {
                $p1 = $mun->tur;
            }
            if ($this->module->program->directivity == 'Социально-педагогическая') {
                $p1 = $mun->soc;
            }
        }

        if ($this->module->program->ground == 2) {
            $p5 = $mun->pc;
            $p6 = $mun->cozp;
            $p12 = $mun->costav;
            $p7 = $mun->codop;
            $p8 = $mun->couvel;
            $p9 = $mun->cootch;
            $p10 = $mun->cootpusk;
            $p11 = $mun->copolezn;
            $p4 = $mun->conopc;
            if ($this->module->program->directivity == 'Техническая (робототехника)') {
                $p1 = $mun->corob;
            }
            if ($this->module->program->directivity == 'Техническая (иная)') {
                $p1 = $mun->cotex;
            }
            if ($this->module->program->directivity == 'Естественнонаучная') {
                $p1 = $mun->coest;
            }
            if ($this->module->program->directivity == 'Физкультурно-спортивная') {
                $p1 = $mun->cofiz;
            }
            if ($this->module->program->directivity == 'Художественная') {
                $p1 = $mun->coxud;
            }
            if ($this->module->program->directivity == 'Туристско-краеведческая') {
                $p1 = $mun->cotur;
            }
            if ($this->module->program->directivity == 'Социально-педагогическая') {
                $p1 = $mun->cosoc;
            }
        }

        $p14 = Yii::$app->coefficient->data->weekmonth;
        $p16 = Yii::$app->coefficient->data->norm;
        $p15 = Yii::$app->coefficient->data->pk;
        $p13 = Yii::$app->coefficient->data->weekyear;

        if ($this->module->programYears->p21z == 1) {
            $p1y = 'p21v';
        }
        if ($this->module->programYears->p21z == 2) {
            $p1y = 'p21s';
        }
        if ($this->module->programYears->p21z == 3) {
            $p1y = 'p21o';
        }
        $p21 = Yii::$app->coefficient->data->$p1y;

        if ($this->module->programYears->p22z == 1) {
            $p2y = 'p22v';
        }
        if ($this->module->programYears->p22z == 2) {
            $p2y = 'p22s';
        }
        if ($this->module->programYears->p22z == 3) {
            $p2y = 'p22o';
        }
        $p22 = Yii::$app->coefficient->data->$p2y;

        $childrenAverage = $this->module->programYears->getChildrenAverage()
            ? $this->module->programYears->getChildrenAverage()
            : ($this->module->programYears->maxchild
                + $this->module->programYears->minchild) / 2;
        $nprice =
            (
                $p6
                * (
                    (
                        (
                            (
                                ($p21
                                    * (
                                        $this->module->programYears->hours
                                        - $this->module->programYears->hoursindivid
                                    )
                                )
                                + (
                                    $p22
                                    * $this->module->programYears->hoursdop
                                )
                            )
                            / ($childrenAverage)
                        )
                        + (
                            $p21
                            * $this->module->programYears->hoursindivid
                        )
                    )
                    / (
                        $p12
                        * $p16
                        * $p14
                    )
                )
                * $p7
                * (
                    1
                    + $p8
                )
                * $p9
                * $p10
            )
            + (
                (
                    (
                        (
                            $this->module->programYears->hours
                            - $this->module->programYears->hoursindivid
                        )
                        + (
                            $this->module->programYears->hoursindivid
                            * $childrenAverage
                        )
                    )
                    / (
                        $p11
                        * $childrenAverage
                    )
                )
                * (
                    $p1
                    * $p3
                    + $p4
                )
            )
            + (
                (
                    (
                        (
                            (
                                $this->module->programYears->hours
                                - $this->module->programYears->hoursindivid
                            )
                            + $this->module->programYears->hoursdop
                            + (
                                $this->module->programYears->hoursindivid
                                * $childrenAverage
                            )
                        )
                        * $p10
                        * $p7
                    )
                    / (
                        $p15
                        * $p13
                        * $p12
                        * $p16
                        * $childrenAverage
                    )
                )
                * $p5
            );

        return round($nprice);
    }

    public function calcAndSaveNormativePrice(): bool
    {
        $this->module->normative_price = $this->calcNormativPrice();

        return $this->module->save();
    }


    public function canVerify()
    {

    }

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
        // TODO: Implement saveActions() method.
    }


}
