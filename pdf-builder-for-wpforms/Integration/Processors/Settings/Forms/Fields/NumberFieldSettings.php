<?php
/**
 * Created by PhpStorm.
 * User: Edgar
 * Date: 3/21/2019
 * Time: 5:05 AM
 */

namespace rednaoformpdfbuilder\Integration\Processors\Settings\Forms\Fields;
use rednaoformpdfbuilder\Utils\Sanitizer;

class NumberFieldSettings extends FieldSettingsBase
{
    public $FormatAsCurrency=false;
    public function __construct()
    {
        $this->UseInConditions=true;
    }

    public function SetFormatAsCurrency()
    {
        $this->FormatAsCurrency=true;
        return $this;
    }

    public function GetType()
    {
        return "Number";
    }

    public function InitializeFromOptions($options)
    {
        parent::InitializeFromOptions($options);
        $this->FormatAsCurrency=Sanitizer::GetBooleanValueFromPath($options,['FormatAsCurrency'],false);
    }
}