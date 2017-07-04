<?php

class InputValidator
{
    /**
     * @var array Values to be validated, e.g. ['name' => 'Sienkiewicz'] 
     */
    protected $input;
    /**
     * @var array Validations applied to values, e.g. ['name' => [[validation1, options][validation2, options]] 
     */
    protected $validations;
    /**
     *
     * @var array List of possible validations, names correspond to method names
     */
    protected $availableValidations = [
        'notEmpty',
        'greaterThan',
        'longerThan',
        'isInt',
        'personNamePattern',
        'whitelist'
    ];
    
    /**
     * @param array $input Values to be validated, e.g. ['name' => 'Sienkiewicz'] 
     * @param array $validations Validations applied to values, e.g. ['name' => [[validation1, options][validation2, options]]
     */
    public function __construct(array $input, array $validations)
    {
        $this->input = [];
        $this->addInput($input);
        $this->validations = [];
        $this->addValidations($validations);
    }
    
    /**
     * @param array $input
     */
    public function addInput(array $input)
    {
        $this->input = array_merge($this->input, $input);
    }
    
    /**
     * @return array
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * merges the provided validations with those already set
     * @param array $validations
     */    
    public function addValidations(array $validations)
    {
        foreach ($validations as $parameter => $validationList) {
            if (!isset($this->validations[$parameter])) {
                $this->validations[$parameter] = [];
            }
            $this->validations[$parameter] += $validationList;
        }
    }
    
    /**
     * @return boolean True if all validations succeeded
     */
    public function validate()
    {
        foreach ($this->validations as $inputName => $validationList)
        {
            $inputValue = $this->getInput()[$inputName];
            if ($this->validateSingleInput($inputValue, $validationList) === false){
                return false;
            }
        }
        return true;
    }
    
    /**
     * Executes all validations of one input value
     * @param mixed $inputValue
     * @param array $validationList
     * @return boolean True if all validations succeeded
     */
    public function validateSingleInput($inputValue, $validationList)
    {
        for ($i = 0; $i < count($validationList); $i++) {
            if ($this->singleValidation($inputValue, $validationList[$i]) === false){
                return false;
            }
        }
        return true;
    }
    
    /**
     * Executes a single validation
     * @param mixed $inputValue
     * @param array $validation
     * @return boolean True if success
     */
    protected function singleValidation($inputValue, array $validation)
    {
        $validationName = $validation[0];
        if (in_array($validationName,$this->availableValidations)) {
            return $this->$validationName($inputValue, $validation);
        }
        return false;
    }
    
    /**
     * @param mixed $inputValue
     * @param array $validation  Form: [validationName, options]. Options: none
     * @return boolean True if success
     */
    protected function notEmpty($inputValue, array $validation)
    {
        return !empty($inputValue);
    }

    /**
     * @param mixed $inputValue
     * @param array $validation  Form: [validationName, options]. Options: none
     * @return boolean True if success
     */
    protected function isInt($inputValue, array $validation)
    {
        return (is_numeric($inputValue) && intval($inputValue) == $inputValue);
    }
    
    /**
     * @param mixed $inputValue
     * @param array $validation  Form: [validationName, options]. Options: int|float reference point (excluded)
     * @return boolean True if success
     */
    protected function greaterThan($inputValue, array $validation)
    {
        return $inputValue > $validation[1];
    }

    /**
     * @param mixed $inputValue
     * @param array $validation  Form: [validationName, options]. Options: int minimum length
     * @return boolean True if success
     */
    protected function longerThan($inputValue, array $validation)
    {
        return mb_strlen($inputValue) > $validation[1];
    }
    
    /**
     * @param mixed $inputValue
     * @param array $validation  Form: [validationName, options]. Options: none
     * @return boolean True if success
     */
    protected function personNamePattern($inputValue, array $validation)
    {
        return (preg_match('/^[-\' \p{L}]+$/u', $inputValue) == 1) ? true : false;
    }
    
    /**
     * @param mixed $inputValue
     * @param array $validation  Form: [validationName, options]. Options: array List
     * @return boolean True if success
     */
    protected function whitelist($inputValue, array $validation)
    {
        return in_array($inputValue,$validation[1]);
    }
    
    /**
     * Clears the input values and validations to allow next validations
     */
    public function clear()
    {
        $this->input = [];
        $this->validations = [];
    }
}