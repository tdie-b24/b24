<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);
CModule::IncludeModule("tasks");
/**
 * Действие "Получение данных о задачаx".
 */

class CBPTzTTaskActivity  extends CBPActivity implements IBPEventActivity, IBPActivityExternalEventListener{

    /** Тип поля анкеты "заметка". Полем не является, используется добавления пояснений в анкету. */
    const FIELD_TYPE_NOTE = 'CBPIvSipCallActivity::FIELD_TYPE_NOTE';

    private $taskId = 0;
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
            'SetStatus' => 'Y',

            "Title" => "", //название действия
            "TaskId" => "", //ID задачи
            "TaskName" => "", //название задачи
            "TaskProps" => "", //свойство задачи

            // Output:
            'Comment' => '',
            //'QuestionnaireResults' => array(),
            'ValueStorage' => array(),
        );

        $this->SetPropertiesTypes(array(
            'TaskId' => array('Type' => FieldType::INT),
            'TaskName' => array('Type' => FieldType::STRING),
            'TaskProps' => array('Type' => FieldType::SELECT),
            'SetStatus' => array('Type' => FieldType::BOOL),
            'Result' => array('Type' => FieldType::SELECT),
            'Comment' => array('Type' => FieldType::TEXT),
        ));
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


        $arResultTaskInfo = $this->__GetTaskParams(array("ID"=>$this->TaskId),array($this->TaskProps));

       /*$arTaskCreatedBy = $this->__GetUsers($this->TaskCreatedBy);
        $arTaskAssignedTo = $this->__GetUsers($this->TaskAssignedTo);

        if (count($arTaskCreatedBy) <= 0 || count($arTaskAssignedTo) <= 0)
            return CBPActivityExecutionStatus::Closed;

        $arTaskTrackers = $this->__GetUsers($this->TaskTrackers);

        $bFirst = true;
        $ACCOMPLICES = array();
        foreach($arTaskAssignedTo as $respUser)
        {
            if ($bFirst)
            {
                $RESPONSIBLE_ID = $respUser;
                $bFirst = false;
            }
            else
                $ACCOMPLICES[] = $respUser;
        }

        $arFields = array(
            "MODIFIED_BY" => $arTaskCreatedBy[0],
            "CREATED_BY" => $arTaskCreatedBy[0],
            "SITE_ID" => SITE_ID,
            "STATUS" => "1",
            "DATE_CREATE" => date($GLOBALS["DB"]->DateFormatToPHP(FORMAT_DATETIME)),
            "START_DATE_PLAN" => $this->TaskActiveFrom,
            "END_DATE_PLAN" => $this->TaskActiveTo,
            "DEADLINE" => $this->TaskActiveTo,
            "TITLE" => $this->TaskName,
            "DESCRIPTION" => $this->TaskDetailText,
            "PRIORITY" => $this->TaskPriority,
            "RESPONSIBLE_ID" => $RESPONSIBLE_ID,
            "AUDITORS" => $arTaskTrackers,
            "ADD_IN_REPORT" => $this->TaskReport,
            "TASK_CONTROL" => $this->TaskCheckResult,
            "ALLOW_CHANGE_DEADLINE" => $this->TaskChangeDeadline,
        );
        if ($this->TaskGroupId && $this->TaskGroupId !== 0)
            $arFields["GROUP_ID"] = $this->TaskGroupId;

        if (count ($ACCOMPLICES) > 0)
            $arFields["ACCOMPLICES"] = $ACCOMPLICES;

        $task = new CTasks;
        $result = $task->Add($arFields);

        if ($result)
            $this->WriteToTrackingService(str_replace("#VAL#", $result, GetMessage("BPSA_TRACK_OK")));

        $arErrors = $task->GetErrors();
        if (count($arErrors) > 0)
            $this->WriteToTrackingService(GetMessage("BPSA_TRACK_ERROR"));*/

        return CBPActivityExecutionStatus::Closed;
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
                'TaskId' => '',
                'TaskName' => '',
                'TaskProps' => '',
                'SetStatus' => 'Y',
                //'QuestionnaireResults' => array()
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
                "ID","TITLE",
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
        if (empty($arCurrentValues['TaskId'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_NO_TASKID')
            );
        }

        // проверяем на пустоту
        if (empty($arCurrentValues['TaskProps'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('BPTA1A_TASKPROPS')
            );
        }

        if (!empty($arErrors)) {
            return false;
        }

        $arProperties = array(
            'TaskId' => $arCurrentValues['TaskId'],
            'TaskName' => $arCurrentValues['TaskName'],
            'TaskProps' => $arCurrentValues['TaskProps'],
            'SetStatus' => $arCurrentValues['SetStatus'],
        );

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity['Properties'] = $arProperties;

        return true;
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