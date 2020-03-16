<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = array(
    'NAME' => Loc::getMessage('SIPCALL_NAME'),
    'DESCRIPTION' => Loc::getMessage('SIPCALL_DESCRIPTION'),
    'TYPE' => 'activity',
    'CLASS' => 'TzTTaskActivity',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => array(
        //'ID' => 'interaction', // Уведомления
        'ID' => 'other', // прочее
    ),
    // в случае если возвращаемые параметры будут изместны только после выбора
    'ADDITIONAL_RESULT' => array('TaskResults'),
    // в случае если возвращаемые параметры известны
    /*"RETURN" => array(
        'Task_id' => [
            'NAME' => 'ID',
            'TYPE' => 'string'
        ],
        "Task_title" => array(
            "NAME" => "Наименование задачи",
            "TYPE" => "string",
        ),
        "Task_props" => array(
            "NAME" => "Свойство задачи",
            "TYPE" => "string",
        ),
        "TaskResults" => array(
            "NAME" => "Результат",
            "TYPE" => "string",
        ),
    ),*/
);