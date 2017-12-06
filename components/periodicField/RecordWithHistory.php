<?php


namespace app\components\periodicField;


interface RecordWithHistory
{
    public function fieldResolver(PeriodicFieldAR $history);
}
