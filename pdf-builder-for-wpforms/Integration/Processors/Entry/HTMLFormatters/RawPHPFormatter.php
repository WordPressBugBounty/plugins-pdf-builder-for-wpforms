<?php
/**
 * Created by PhpStorm.
 * User: Edgar
 * Date: 3/28/2019
 * Time: 7:33 AM
 */

namespace rednaoformpdfbuilder\Integration\Processors\Entry\HTMLFormatters;


class RawPHPFormatter extends PHPFormatterBase
{
    public $Value;
    public $SkipPTag=false;
    public function __construct($Value,$field=null,$skipPTag=false)
    {
        parent::__construct($field);
        $this->SkipPTag=$skipPTag;
        $this->Value = $Value;
    }

    public function __toString()
    {
        if($this->SkipPTag)
            return $this->Value;
        return '<p>'.nl2br($this->Value).'</p>';
    }

    public function ToText(){
        return $this->Value;
    }

    public function IsEmpty(){
        return trim($this->Value)=='';
    }


}