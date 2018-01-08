<?php
/**
 * Created by PhpStorm.
 * User: eugene-kei
 * Date: 18.12.17
 * Time: 0:35
 */

/**
 * @var $this \yii\web\View
 */

$isOrganization = \Yii::$app->user->can('organization');
?>
<?php if ($isOrganization) { ?>
    <div id="organization-menu-delete-order-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Внимание!</h4>
                </div>
                <div class="modal-body">
                    <p>Функционал удаления договоров доступен лишь в исключительных случаях, когда одновременно
                        выполняются
                        следующие условия:</p>
                    <p>1. Есть согласие обеих сторон (Вы и заказчик) с тем, что договор был зарегистрирован в системе по
                        ошибке и ни одна из сторон не имеет возражения для удаления договора.</p>
                    <p>2. Договор не был еще включен ни в один из выставленных счетов.</p>
                    <p>3. Основание для удаление договора действительно значимое, а не просто перепутан срок начала
                        обучения,
                        или группа.</p>
                    <p>4. Есть острая необходимость возврата средств договора на сертификат ребенка.</p>

                    <p>В случае если хотя бы одно из представленных условий не выполняется - не направляйте запрос на
                        удаление договора. Вы обязательно должны понимать, что факт регистрации договора в системе
                        означает
                        факт подписания заявления родителя на прием, а значит акцепт оферты. Если удаление договора
                        может
                        привести к возражению от какой-либо из сторон, то НИ В КОЕМ СЛУЧАЕ не удаляйте его. Удаление
                        такого договора может рассматриваться как попытка уничтожения документов. Система хранит всю
                        историю
                        запросов на удаление.</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['organization/cleanup/contract']) ?>">Продоложить</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php } ?>
