<?php

namespace Bentley\Eadoc\forms;


class Validation
{
    /**
     * @var string[]
     */
    public $patterns = array(
        'uri' => '[A-Za-z0-9-\/_?&=]+',
        'url' => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha' => '[\p{L}]+',
        'words' => '[\p{L}\s]+',
        'alphanum' => '[\p{L}0-9]+',
        'int' => '[0-9]+',
        'float' => '[0-9\.,]+',
        'tel' => '[0-9+\s()-]+',
        'text' => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
        'file' => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder' => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address' => '[\p{L}0-9\s.,()°-]+',
        'date_dmy' => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd' => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email' => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+[.]+[a-z-A-Z]'
    );

    /**
     * @var array $errors
     */
    public $errors = array();

    /**
     * @param $value
     * @return bool|void
     */
    public static function isInt($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT)) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isFloat($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT)) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isAlpha($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/")))) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isAlphanum($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isUrl($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isUri($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isBool($value)
    {
        if (is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) return true;
    }

    /**
     * @param $value
     * @return bool|void
     */
    public static function isEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) return true;
    }

    /**
     * @param $array
     * @return void
     */
    public function validate($array)
    {
        if (is_array($array) and count($array) > 0) {
            foreach ($array as $key => $row) {
                $validation = $this->name($key)->value($_GET[$key] ?? $_POST[$key])->pattern($row['pattern']);
                if (isset($row['required']) and isset($row['required'])) {
                    $validation->required();
                }
                //->value($email)->pattern('email')->required()
            }
        }
        if ($this->isSuccess()) {
            echo "Validation ok!";
        } else {
            echo "Validation error!";
            var_dump($this->getErrors());
        }

    }

    /**
     * @return $this
     */
    public function required()
    {

        if ((isset($this->file) && $this->file['error'] == 4) || ($this->value == '' || $this->value == null)) {
            $this->errors[] = 'Campo ' . $this->name . ' obbligatorio.';
        }
        return $this;

    }

    /**
     * @param $name
     * @return $this
     */
    public function pattern($name)
    {
        if ($name == 'array') {

            if (!is_array($this->value)) {
                $this->errors[] = 'field ' . $this->name . ' non valid.';
            }

        } else {

            $regex = '/^(' . $this->patterns[$name] . ')$/u';
            if ($this->value != '' && !preg_match($regex, $this->value)) {
                $this->errors[] = 'Field ' . $this->name . ' non valid.';
            }

        }
        return $this;

    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {

        $this->value = $value;
        return $this;

    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function file($value)
    {

        $this->file = $value;
        return $this;

    }

    /**
     * @param $pattern
     * @return $this
     */
    public function customPattern($pattern)
    {

        $regex = '/^(' . $pattern . ')$/u';
        if ($this->value != '' && !preg_match($regex, $this->value)) {
            $this->errors[] = 'Field ' . $this->name . ' non valid.';
        }
        return $this;

    }

    /**
     * @param $length
     * @return $this
     */
    public function min($length)
    {

        if (is_string($this->value)) {

            if (strlen($this->value) < $length) {
                $this->errors[] = 'Valore campo ' . $this->name . ' inferiore al valore minimo';
            }

        } else {

            if ($this->value < $length) {
                $this->errors[] = 'Valore campo ' . $this->name . ' inferiore al valore minimo';
            }

        }
        return $this;

    }

    /**
     * @param $length
     * @return $this
     */
    public function max($length)
    {

        if (is_string($this->value)) {

            if (strlen($this->value) > $length) {
                $this->errors[] = 'Valore campo ' . $this->name . ' superiore al valore massimo';
            }

        } else {

            if ($this->value > $length) {
                $this->errors[] = 'Valore campo ' . $this->name . ' superiore al valore massimo';
            }

        }
        return $this;

    }

    /**
     * @param $value
     * @return $this
     */
    public function equal($value)
    {

        if ($this->value != $value) {
            $this->errors[] = 'Valore campo ' . $this->name . ' non corrispondente.';
        }
        return $this;

    }

    /**
     * @param $size
     * @return $this
     */
    public function maxSize($size)
    {

        if ($this->file['error'] != 4 && $this->file['size'] > $size) {
            $this->errors[] = 'Il file ' . $this->name . ' supera la dimensione massima di ' . number_format($size / 1048576, 2) . ' MB.';
        }
        return $this;

    }

    /**
     * @param $extension
     * @return $this
     */
    public function ext($extension)
    {

        if ($this->file['error'] != 4 && pathinfo($this->file['name'], PATHINFO_EXTENSION) != $extension && strtoupper(pathinfo($this->file['name'], PATHINFO_EXTENSION)) != $extension) {
            $this->errors[] = 'Il file ' . $this->name . ' non è un ' . $extension . '.';
        }
        return $this;

    }

    /**
     * @param $string
     * @return string
     */
    public function purify($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return string
     */
    public function displayErrors()
    {

        $html = '<ul>';
        foreach ($this->getErrors() as $error) {
            $html .= '<li>' . $error . '</li>';
        }
        $html .= '</ul>';

        return $html;

    }

    /**
     * @return array|void
     */
    public function getErrors()
    {
        if (!$this->isSuccess()) return $this->errors;
    }

    /**
     * Campi validati
     *
     * @return boolean
     */
    public function isSuccess()
    {
        if (empty($this->errors)) return true;
    }

    /**
     * @return bool|void
     */
    public function result()
    {

        if (!$this->isSuccess()) {

            foreach ($this->getErrors() as $error) {
                echo "$error\n";
            }
            exit;

        } else {
            return true;
        }

    }
}

?>

