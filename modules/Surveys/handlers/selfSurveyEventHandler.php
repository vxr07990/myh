<?php

//require_once 'include/events/VTEventHandler.inc';
use MoveCrm\InputUtils;

class selfSurveyEventHandler extends VTEventHandler {

    protected $surveyObject;
    protected $entityData;
    protected static $MOVE_EASY_ACCOUNT_VARIABLE = 'moveEasyAccount';
    protected static $MOVE_EASY_PASS_THROUGH_VARIABLE = 'x_hash';
    protected static $CHECK_SURVEY_TYPE = 'Self Survey';
    protected static $ALLOWED_MODULES = [
        'Surveys'
        ];

    public function handleEvent($eventName, $entityData) {
        if ($eventName != 'vtiger.entity.beforesave') {
            return;
        }

        //@NOTE: Event triggers are NOT module based. so limit by module here...
        $moduleName = $entityData->getModuleName();
        if (!in_array($moduleName, self::$ALLOWED_MODULES)) {
            return;
        }
        
        //@NOTE: I think $id indicates this is an existing record.
        //$id = $entityData->getId();

        $survey_type = $entityData->get('survey_type');
        if ($survey_type != self::$CHECK_SURVEY_TYPE) {
            return;
        }

        $self_survey_url = $entityData->get('self_survey_url');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if (!$moduleModel) {
           return;
        }

        $fieldModel = $moduleModel->getField('self_survey_url');
        if (!$fieldModel) {
            return;
        }

        if (
            $self_survey_url &&
            $self_survey_url != $fieldModel->defaultvalue
        ) {
            return;
        }

        $this->entityData = $entityData;
        $this->surveyObject = $entityData->focus;

        $url = $this->getSurveyUrl();
        //$entityData->set('self_survey_url', $url.'&time='.time());
        $entityData->set('self_survey_url', $url);
        return;
    }

    protected function getSurveyUrl() {
        $parsed_url = parse_url(getenv('MOVE_EASY_LINK_URL'));

        $url = isset($parsed_url['scheme'])?$parsed_url['scheme'].'://':'http://';
        $user = isset($parsed_url['user'])?$parsed_url['user']:'';
        $pass = isset($parsed_url['pass'])?':'.$parsed_url['pass']:'';
        $url .= ($user || $pass)?$user.$pass.'@':'';
        $url .= isset($parsed_url['host'])?$parsed_url['host']:'';
        $url .= isset($parsed_url['port'])?':'.$parsed_url['port']:'';
        $url .= isset($parsed_url['path'])?$parsed_url['path']:'';
        $url .= isset($parsed_url['query'])?'?'.$parsed_url['query'].'&':'?';
        //$url  .= isset($parsed_url['fragment'])?'#'.$parsed_url['fragment']:'';

        //This will need to include an account Id parameter which will indicate which sync service the Move Easy system will send the data
        $url .= self::$MOVE_EASY_ACCOUNT_VARIABLE . '='. getenv('MOVE_EASY_ACCOUNT_IDENTIFIER');
        $url .= self::$MOVE_EASY_PASS_THROUGH_VARIABLE . '='. $this->getPassThroughHash();

        return $url;
    }

    protected function getPassThroughHash() {
        if (!$this->surveyObject) {
            return;
        }

        //It will also include a hash that will indicate the environment,
        //instance key and
        //opportunity Id
        //so we can verify that the data is being sent to the correct instance
        //and attach the survey data to the correct opportunity in MoveCRM.
        $relatedRecords = $this->getRelatedRecords();
        $array = [
            'host' => $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'],
            //'host' => $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'],
            'moveHQ' => InputUtils::CheckboxToBool(getenv('IGC_MOVEHQ')) ? true : false,
            'instance_name' => getenv('INSTANCE_NAME'),
            'related_records' => $relatedRecords
        ];

        $jsonString = json_encode($array, true);
        $base64String = base64_encode($jsonString);

        return $base64String;
    }

    protected function getRelatedRecords() {
        //@TODO: this must be an existing thing to do this.
        $returnArray    = [];
        $relatedRecords = [
            'Opportunities' => 'potential_id',
            'Orders'        => 'order_id',
            'Contacts'      => 'contact_id',
            'Accounts'      => 'account_id',
        ];

        foreach ($relatedRecords as $module => $field) {
            //$returnArray[] = $this->getSingleRelatedRecord($module, $field);
            $singleRecord = $this->getSingleRelatedRecord($module, $field);
            if ($singleRecord) {
                $returnArray[] = [$module => $singleRecord];
            }
        }

        return $returnArray;
    }

    protected function getSingleRelatedRecord($module, $field) {
        if (!$module) {
            return;
        }
        if (!$this->entityData) {
            return;
        }
        if (!$this->entityData->get($field)) {
            return;
        }
        return vtws_getWebserviceEntityId($module, $this->entityData->get($field));
    }

    protected function log($note, $data = NULL) {
        $this->db->pquery('INSERT INTO vtiger_mecumapi_log (trigger_crmid,`transaction`,mode,notes,`data`,`time`) 
                      VALUES (?,?,?,?,?,NOW())',
                          [$this->orderId, $this->transactionId, $this->mode, $note, $data?json_encode($data):'']);
    }

    protected function logException($note, $exception) {
        $this->log($note.' '.$exception->getMessage().PHP_EOL.$exception->getTraceAsString());
    }
}
