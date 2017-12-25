<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 14.12.17
 * Time: 11:32
 */

namespace app\components;


interface ModelDecoratorInterface
{
    public static function decorate($entity);

    public static function decorateMultiple(array $entitys);
}
