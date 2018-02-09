<?php
/**
 * Created by PhpStorm.
 */

namespace MoveCrm;
include_once ('vendor/respect/validation/library/Validator.php');
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;


class FieldValidation {
    /**
     * @var Respect\Validation\Validator $v
     */
    private $v;

    /**
     * @var bool $error
     */
    protected $error;

    /**
     * @var array
     */
    protected $errorFieldArray;

    /**
     * @var string
     */
    protected $errorLastField;

    /**
     * @var string
     */
    protected $errorLastMessage;

    /**
     * FieldValidation constructor.
     */
    public function __construct() {
        $this->v = new Validator;
    }

    /**
     * Build rule adds the rule to the current rule set.
     * @param $rule
     *
     * @return null
     */
    public function buildRule($rule) {
        $this->v->addRule($this->v, $rule);
        return null;
    }

    /**
     * Build optional Rule should wrap the rule in "optional" which is true when input is null
     * @param $rule
     *
     * @return null
     */
    public function buildOptionalRule($rule){
        //@TODO: This probably won't work.
        $this->buildRule($rule);
        $this->v->optional($this->v);
        return null;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function processRule($input = false) {
            try {
                $this->v->assert($input);
                return true;
            } catch(NestedValidationException $exception) {
                $this->setError(
                    [
                        'errorFieldArray' => $exception->getMessages(),
                        'errorLastField' => $this->fieldName,
                        'errorLastMessage' => $exception->getMainMessage(),
                    ]);
            } catch (\Exception $exception) {
                $this->setError(
                    [
                        'errorFieldArray' => [$exception->getMessage()],
                        'errorLastField' => $this->fieldName,
                        'errorLastMessage' => $exception->getMessage(),
                    ]);
            }
            return false;
    }

    /**
     * @param array $errorArray
     *
     * @return null
     */
    public function setError(array $errorArray) {
        $this->error = true;
        $this->errorFieldArray = $errorArray['errorFieldArray'];
        $this->errorLastField = $errorArray['errorLastField'];
        $this->errorLastMessage = $errorArray['errorLastMessage'];

        return null;
    }

    /**
     * @return array
     */
    public function getError() {
        return [
            'error' => $this->error,
            'errorFieldArray' => $this->errorFieldArray,
            'errorLastField' => $this->errorLastField,
            'errorLastMessage' => $this->errorLastMessage,
        ];
    }
}
