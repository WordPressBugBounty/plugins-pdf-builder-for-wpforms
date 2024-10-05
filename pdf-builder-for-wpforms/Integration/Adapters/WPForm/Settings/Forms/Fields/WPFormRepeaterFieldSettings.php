<?php
/**
 * Created by PhpStorm.
 * User: Edgar
 * Date: 3/21/2019
 * Time: 6:19 AM
 */

namespace rednaoformpdfbuilder\Integration\Adapters\WPForm\Settings\Forms\Fields;
use rednaoformpdfbuilder\Integration\Processors\Settings\Forms\Fields\FieldSettingsBase;

class WPFormRepeaterFieldSettings extends FieldSettingsBase
{

    public $Columns;

    public function __construct()
    {
        $this->Type='repeater';
        $this->UseInConditions=false;

    }

    public function SetColumns($colums)
    {
        $this->Columns=$colums;
        return $this;
    }

    public function GetType()
    {
        return 'Repeater';
    }

    public function InitializeFromOptions($options)
    {
        $this->Columns=$this->GetStringValue($options,['Columns']);
        parent::InitializeFromOptions($options);
    }


}