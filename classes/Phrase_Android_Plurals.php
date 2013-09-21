<?php

require_once('Phrase_Android.php');

class Phrase_Android_Plurals extends Phrase_Android {

    const QUANTITY_ZERO = 'zero';
    const QUANTITY_ONE = 'one';
    const QUANTITY_TWO = 'two';
    const QUANTITY_FEW = 'few';
    const QUANTITY_MANY = 'many';
    const QUANTITY_OTHER = 'other';

    protected static $list = array(
        self::QUANTITY_ZERO,
        self::QUANTITY_ONE,
        self::QUANTITY_TWO,
        self::QUANTITY_FEW,
        self::QUANTITY_MANY,
        self::QUANTITY_OTHER
    );
    protected $values;

    public function __construct($id, $phraseKey, $enabledForTranslation = true) {
        parent::__construct($id, $phraseKey, $enabledForTranslation);
        $this->values = array(
            self::QUANTITY_ZERO => '',
            self::QUANTITY_ONE => '',
            self::QUANTITY_TWO => '',
            self::QUANTITY_FEW => '',
            self::QUANTITY_MANY => '',
            self::QUANTITY_OTHER => ''
        );
    }

    /**
     * Adds a new value for the given quantity
     *
     * @param int $quantityID the quantity ID to set the value for
     * @param string $value the value to set
     * @throws Exception if the quantity ID is unknown
     */
    public function addValue($quantityID, $value) {
        if (isset($this->values[$quantityID])) {
            $this->values[$quantityID] = $value;
        }
        else {
            throw new Exception('Unknown quantity ID: '.$quantityID);
        }
    }

    /**
     * @return array the plurals' contents
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * Returns the output of this phrase for the specific platform and type of phrase
     *
     * @return string output of this phrase
     */
    public function output() {
        $out = "\t".'<plurals name="'.$this->phraseKey.'">'."\n";
        foreach ($this->values as $quantity => $value) {
            $out .= "\t\t".'<item quantity="'.$quantity.'">'.self::writeToRaw($value).'</item>'."\n";
        }
        $out .= "\t".'</plurals>'."\n";
        return $out;
    }

    /**
     * Returns the percentage of completion for this phrase where 0.0 is empty and 1.0 is completed
     *
     * @return float the percentage of completion for this phrase
     */
    public function getCompleteness() {
        $complete = 0;
        $total = 0;
        foreach ($this->values as $value) {
            if (!empty($value)) {
                $complete++;
            }
            $total++;
        }
        return $complete/$total;
    }

    /**
     * Gets the phrase's payload in form of JSON data
     *
     * @return string payload as JSON data
     */
    public function getPayload() {
        return self::getPayloadFromValue($this->values);
    }

    public static function getList() {
        return self::$list;
    }

    /**
     * Creates JSON payload from a phrase's value(s)
     *
     * @param mixed $value single string or array of strings (value for the phrase)
     * @return string JSON payload
     */
    public static function getPayloadFromValue($value) {
        $data = array(
            'class' => 'Phrase_Android_Plurals',
            'values' => $value
        );
        return json_encode($data);
    }

    /**
     * @return array list of values for this phrase
     */
    public function getPhraseValues() {
        return $this->values;
    }

    /**
     * Sets the phrase's payload from the given JSON data
     *
     * @param string $json JSON data to get the payload from
     * @param bool $createKeysOnly whether the complete phrase should be created (true) or keys only (false)
     */
    public function setPayload($json, $createKeysOnly = false) {
        $data = json_decode($json, true);
        if (!$createKeysOnly) {
            $this->values = $data['values'];
        }
        else {
            $this->values = array();
            $keys = array_keys($data['values']);
            foreach ($keys as $key) {
                $this->values[$key] = '';
            }
        }
    }

}

?>