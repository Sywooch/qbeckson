<?php
use app\models\Contracts;
use app\models\Programs;

/* @var $this yii\web\View */

$this->title = 'Статистическая информация';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if ($this->beginCache('organization-statistic-' . $organization->id, ['duration' => 3600])): ?>
<div class="col-md-10 col-md-offset-1 well">
    <p>Количество сертифицированных программ образовательной организации - <?= Programs::getCountPrograms($organization['id'], 2) ?></p>
    <p>Количество программ образовательной организации ожидающих сертификации - <?= Programs::getCountPrograms($organization['id'], 0) + Programs::getCountPrograms($organization['id'], 1) ?></p>
    <p>Максимально допустимое количество детей для обучения по системе персонифицированного финансирования - <?= $organization['max_child'] ?></p>
    <?php $certificatesUsedCount = Contracts::getCountUsedCertificates(null, ['organizationId' => $organization->id]); ?>
    <p>Количество детей обучающихся по системе персонифицированного финансирования - <?= $certificatesUsedCount ?></p>
    <p>Количество мест по которым могут быть заключены договора по системе персонифицированного финансирования - <?=  $organization['max_child'] - $certificatesUsedCount ?></p>
    <p>Количество заявок на заключение договоров по системе персонифицированного финансирования - <?= Contracts::getCountContracts(['status' => [0, 3], 'organizationId' => $organization->id]) ?></p>
</div>
<?php $this->endCache(); ?>
<?php endif; ?>
