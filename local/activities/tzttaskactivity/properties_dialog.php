<?
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

/** @var array $arCurrentValues */
/** @var string $formName */

Loc::loadMessages(__FILE__);

/**
 * Функция-помощник для вывода атрибута selected в <option>.
 * @param bool $condition Условие, когда элемент списка должен быть выбран.
 */
function selected($condition) {
    if ($condition) {
        echo 'selected';
    }
}

/*echo "<pre>";
print_r($arCurrentValues);
echo "</pre>";*/

// __log($arCurrentValues);
?>

<tr>
    <td align="right" width="40%"> <?= GetMessage("BPTA1A_TASKID") ?>:</td>
    <td width="60%">

        <select name="Task_id" id="id_TaskId">
            <option value="">Выберите из списка ...</option>
            <?foreach ($allResultTask as $k=>$itemTask){?>
                <option value="<?=$itemTask["ID"]?>"  <? selected($arCurrentValues['Task_id'] == $itemTask["ID"]) ?>><?="[".$itemTask["ID"]."] ".$itemTask["TITLE"]?></option>
            <?}?>
        </select>
    </td>
</tr>
<tr>
    <td align="right" width="40%"> <?= GetMessage("BPTA1A_TASKPROPS") ?>:</td>
    <td width="60%">

        <select name="Task_props[]" id="id_TaskProps" multiple>
            <option value="">Выберите из списка ...</option>
            <?foreach ($TaskAllProps as $k=>$itemProps){?>
                <option value="<?=$itemProps?>"  <? selected(in_array($itemProps,$arCurrentValues['Task_props'])) ?>><?=$itemProps?></option>
            <?}?>
        </select>
    </td>
</tr>

<?/*<tr class="switchable on-phone">
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('BPTA1A_TASKID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'TaskId', $arCurrentValues['TaskId'], Array('size'=> 10)) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('BPTA1A_TASKID') ?>:</span></td>
    <td width="60%">
        <select id="TaskIdSwitcher" name="TaskId">
            <?foreach ($allResultTask as $k=>$itemTask){?>
                <option value="<?=$itemTask["ID"]?>" <? selected($arCurrentValues["TaskId"] == $itemTask["ID"]) ?>>
                    <?=$itemTask["TITLE"]?>
                </option>
            <?}?>
        </select>
    </td>
</tr>
<tr class="switchable on-crm_contact">
    <td align="right" width="40%"><span><?= Loc::getMessage('BPTA1A_TASKNAME') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'Task_title', $arCurrentValues['Task_title'], Array('size'=> 50)) ?>
    </td>
</tr>
<tr class="switchable on-crm_contact">
    <td align="right" width="40%"><span><?= Loc::getMessage('BPTA1A_TASKPROPS') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'TaskProps', $arCurrentValues['TaskProps'], Array('size'=> 50)) ?>
    </td>
</tr>*/?>