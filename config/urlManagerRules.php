<?php

namespace app\config\urlManagerRules;

return [
    '<module:\w+>/<controller:\w+>/<action:(\w|-)+>' => '<module>/<controller>/<action>',
    '<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
];
