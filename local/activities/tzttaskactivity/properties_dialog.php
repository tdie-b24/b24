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


echo "<pre>";
print_r($arCurrentValues);
echo "</pre>";

?>

<!-- Поле произвольного номера телефона (отображается, когда выбран соотв. тип контакта). -->
<?/*<tr class="switchable on-phone">
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('BPTA1A_TASKID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'TaskId', $arCurrentValues['TaskId'], Array('size'=> 10)) ?>
    </td>
</tr>*/?>


<!-- Тип контакта: произвольный номер телефона, контакт CRM. -->
<?/*<tr>
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
</tr>*/?>

<tr>
    <td align="right" width="40%"> <?= GetMessage("BPTA1A_TASKID") ?>:</td>
    <td width="60%">

        <select name="TaskId" id="id_TaskId">
            <option value="">Выберите из списка ...</option>
            <?foreach ($allResultTask as $k=>$itemTask){?>
                <option value="<?=$itemTask["ID"]?>"  <? selected($arCurrentValues['TaskId'] == $itemTask["ID"]) ?>><?="[".$itemTask["ID"]."] ".$itemTask["TITLE"]?></option>
            <?}?>
        </select>
    </td>
</tr>
<tr>
    <td align="right" width="40%"> <?= GetMessage("BPTA1A_TASKPROPS") ?>:</td>
    <td width="60%">

        <select name="TaskProps" id="id_TaskProps" multiple>
            <option value="">Выберите из списка ...</option>
            <?foreach ($TaskAllProps as $k=>$itemProps){?>
                <option value="<?=$itemProps?>"  <? selected(in_array($itemProps,$arCurrentValues['TaskProps'])) ?>><?=$itemProps?></option>
            <?}?>
        </select>
    </td>
</tr>

<?/*<tr class="switchable on-crm_contact">
    <td align="right" width="40%"><span><?= Loc::getMessage('BPTA1A_TASKNAME') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'TaskName', $arCurrentValues['TaskName'], Array('size'=> 50)) ?>
    </td>
</tr>

<!-- Поле указания CRM контакта (отображается, когда выбран соотв. тип контакта). -->
<tr class="switchable on-crm_contact">
    <td align="right" width="40%"><span><?= Loc::getMessage('BPTA1A_TASKPROPS') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'TaskProps', $arCurrentValues['TaskProps'], Array('size'=> 50)) ?>
    </td>
</tr>*/?>