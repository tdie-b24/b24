<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

use Bitrix\Bizproc\FieldType;

Loc::loadMessages(__FILE__);
CModule::IncludeModule("tasks");
/**
 * Действие "Получение данных о задачаx".
 */

class CBPTzTTaskActivity  extends CBPActivity implements IBPEventActivity, IBPActivityExternalEventListener{

    /** Тип поля анкеты "заметка". Полем не является, используется добавления пояснений в анкету. */
    const FIELD_TYPE_NOTE = 'CBPIvSipCallActivity::FIELD_TYPE_NOTE';

    private $task_id = 0;
    private $taskStatus = false;

    private $isInEventActivityMode = false;

    /**
     * Инициализирует действие.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = array(
            //'SetStatus' => 'Y',

            "Title" => "", //название действия
            "Task_id" => "", //ID задачи
            "Task_title" => "", //название задачи
            "Task_props" => array(), //свойство задачи
            "Result" => array(),
            'TaskResults' => array(),

            // Output:
            //'Comment' => '',
            'ValueStorage' => array(),
        );

        $this->SetPropertiesTypes(array(
            'Task_id' => array('Type' => FieldType::INT),
            'Task_title' => array('Type' => FieldType::STRING),
            'Task_props' => array('Type' => FieldType::SELECT),
            //'Result' => array('Type' => FieldType::SELECT),
            //'SetStatus' => array('Type' => FieldType::BOOL),
            //'Comment' => array('Type' => FieldType::TEXT),
        ));
    }

    protected function ReInitialize()
    {
        parent::ReInitialize();

        $this->Task_id = 0;
        $this->Task_title = '';
        $this->Task_props = array();
        $this->TaskResults = array();
    }

    private function __GetTaskParams($filterTask,$PropsFields = array())
    {

        if(empty($filterTask)){
            return false;
        }

        $allResultTask = array();
        $arFilter = array();
        $arSelect = array();

        $arFilter = array_merge($arFilter,$filterTask);
        $arSelect = array_merge($arSelect,$PropsFields);

        //__log(array($arFilter, $arSelect));

        $resTaskObj = CTasks::GetList(
            Array("TITLE" => "ASC"),
            $arFilter,
            $arSelect
        );

        while ($arTask = $resTaskObj->Fetch())
        {
            $allResultTask[] = $arTask;
        }

        return $allResultTask;
    }

    // Исполняющийся метод действия
    public function Execute()
    {

        if (!CModule::IncludeModule("tasks"))
            return CBPActivityExecutionStatus::Closed;

        $arResultTaskInfo = array();
        $arResultTaskInfo = $this->__GetTaskParams(array("ID"=>$this->Task_id),$this->Task_props);


        if(empty($arResultTaskInfo)){
            return CBPActivityExecutionStatus::Closed;
        }else{
            $resT = array();
            if(count($arResultTaskInfo) == 1){
                foreach ($arResultTaskInfo[0] as $k=>$item){
                    $resT["Task_".strtolower($k)] = $item;
                }
            }

            $this->SetProperties($resT);

        }

        // Вернем указание исполняющей среде, что действие еще выполняется
        // return CBPActivityExecutionStatus::Executing;

        return CBPActivityExecutionStatus::Closed;
    }


    public function SetProperties($arProperties = array())
    {
        if (count($arProperties) > 0)
        {
            foreach ($arProperties as $key => $value)
                $this->arProperties[$key] = $value;
        }
    }

    /**
     * Готовит текущие настройки действия к отображению в форме настройки действия и генерирует HTML формы настройки.
     * @param array $documentType (string модуль, string класс документа, string код типа документа).
     * @param string $activityName Название действия.
     * @param array $arWorkflowTemplate Шаблон БП.
     * @param array $arWorkflowParameters Параметры шаблона БП.
     * @param array $arWorkflowVariables Переменные БП.
     * @param array|null $arCurrentValues Значения параметров действия, если есть.
     * @param string $formName
     * @return string HTML-код формы настройки шага для конструктора БП.
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        if (!is_array($arCurrentValues))
        {
            $arCurrentValues = array(
                'Task_id' => '',
                'Task_title' => '',
                'Task_props' => array(),
                'TaskResults' => array()
                //'SetStatus' => 'Y',

            );

            $arCurrentActivity= &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate,
                $activityName
            );
            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
               /* $arCurrentValues['Responsible'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Responsible'],
                    $arWorkflowTemplate,
                    $documentType
                );*/
            }
        }

        $allResultTask = array();
        $TaskAllProps = array();
        $resTaskObj = CTasks::GetList(
            Array("TITLE" => "ASC"),
            Array(
                //"RESPONSIBLE_ID" => "2"
            ),
            array(
                "ID",
                "TITLE",
                "PARENT_ID",
                "GROUP_ID",
                "PRIORITY",
                "TAG",
                "REAL_STATUS",
                "STATUS",
                "XML_ID",
                "DEPARTMENT_ID",
                "AUDITOR",
                "ACCOMPLICE",
                "MARK",
                "FORUM_TOPIC_ID",
                "RESPONSIBLE_ID",
                "STATUS_CHANGED_BY"
            )
        );

        $s = false;
        while ($arTask = $resTaskObj->Fetch())
        {
            $allResultTask[$arTask["ID"]] = $arTask;

            // выводим все ключи что возвращаются
            if(!$s){
                $TaskAllProps = array_keys($arTask);
            }

        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(
            __FILE__,
            'properties_dialog.php',
            array(
                'allResultTask' => $allResultTask,
                'TaskAllProps' => $TaskAllProps,
                'arCurrentValues' => $arCurrentValues,
                'formName' => $formName,
            )
        );
    }

    /**
     * Сохраняет настройки действия, принимает на вход данные из формы настройки действия.
     * @param array $documentType (string модуль, string класс документа, string код типа документа)
     * @param string $activityName Название действия в шаблоне БП.
     * @param array $arWorkflowTemplate Шаблон БП.
     * @param array $arWorkflowParameters Параметры шаблона БП.
     * @param array $arWorkflowVariables Переменные БП.
     * @param array $arCurrentValues Данные из формы настройки действия.
     * @param array $arErrors [Выходные данные] Ошибки валидации.
     * @return bool true, если настройки дейтсвия сохранены успешно.
     */
    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = array();

        $runtime = CBPRuntime::GetRuntime();

        // проверяем на пустоту
        if (empty($arCurrentValues['Task_id'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_NO_TASKID')
            );
        }

        // проверяем на пустоту
        if (empty($arCurrentValues['Task_props'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('BPTA1A_TASKPROPS')
            );
        }

        if (!empty($arErrors)) {
            return false;
        }

        $arProperties = array(
            'Task_id' => $arCurrentValues['Task_id'],
            'Task_title' => $arCurrentValues['Task_title'],
            'Task_props' => $arCurrentValues['Task_props'],
            'TaskResults' => array(),
        );

        // вариант когда возвращаем выбранные поля
        $arProperties["TaskResults"] = self::buildTResults($arCurrentValues['Task_props']);

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity['Properties'] = $arProperties;

        return true;
    }


    private static function buildTResults($fields)
    {
        $result = array();
        if(!empty($fields)){
            foreach ($fields as $itemProp){

                $result["Task_".strtolower($itemProp)] = array(
                    "Name"=>$itemProp,
                    "Type"=>($itemProp == "ID" ? FieldType::INT : FieldType::STRING),
                );

            }
        }

        return $result;
    }


    /**
     * Создает задание на звонок и подписывается на событие завершения задания (нажатие кнопки "Готово").
     * @param IBPActivityExternalEventListener $eventHandler Обработчик события завершения задания.
     * @throws Exception
     */
    public function Subscribe(IBPActivityExternalEventListener $eventHandler)
    {
        //
    }

    /**
     * Удаляет задание и отписывается от его событий.
     * Метод будет вызван в случае ошибки или удаления БП, чтобы корректно отменить выполнение действия.
     * @param IBPActivityExternalEventListener $eventHandler
     * @throws Exception
     */
    public function Unsubscribe(IBPActivityExternalEventListener $eventHandler)
    {
        //
    }

    public function OnExternalEvent($arEventParameters = array())
    {
        //

    }
}