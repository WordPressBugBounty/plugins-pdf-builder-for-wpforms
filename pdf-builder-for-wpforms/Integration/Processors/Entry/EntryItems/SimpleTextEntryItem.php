<?php
/**
 * Created by PhpStorm.
 * User: Edgar
 * Date: 3/22/2019
 * Time: 5:50 AM
 */

namespace rednaoformpdfbuilder\Integration\Processors\Entry\EntryItems;


use rednaoformpdfbuilder\htmlgenerator\sectionGenerators\DocumentGenerator;
use rednaoformpdfbuilder\Integration\Adapters\WPForm\Settings\Forms\Fields\WPFormAddressFieldSettings;
use rednaoformpdfbuilder\Integration\Processors\Entry\HTMLFormatters\BasicPHPFormatter;
use rednaoformpdfbuilder\Integration\Processors\Entry\HTMLFormatters\MultipleBoxFormatter\MultipleBoxFormatter;
use rednaoformpdfbuilder\Integration\Processors\Entry\HTMLFormatters\MultipleBoxFormatter\SingleBoxFormatter;
use rednaoformpdfbuilder\Integration\Processors\Settings\Forms\Fields\NumberFieldSettings;
use stdClass;

class SimpleTextEntryItem extends EntryItemBase
{
    public $Value;
    public function SetValue($value)
    {
        $this->Value=$value;
        return $this;
    }


    protected function InternalGetObjectToSave()
    {
        return (object)array(
            'Value'=>$this->Value
        );
    }

    public function InitializeWithOptions($field,$options)
    {
        $this->Field=$field;
        if(isset($options->Value))
            $this->Value=$options->Value;
    }

    public function GetHtml($style='standard',$field=null)
    {
        $value=$this->Value;
        if($this->Field!=null&&$this->Field instanceof NumberFieldSettings&&$this->Field->FormatAsCurrency==true&& DocumentGenerator::$LatestDocument!=null)
        {
            $value=DocumentGenerator::$LatestDocument->Formatter->FormatCurrency($this->Value);
        }
        if($style=='similar')
        {
            /** @var WPFormAddressFieldSettings $field */
            $field = $this->Field;
            $formatter = new SingleBoxFormatter($value);

            return $formatter;
        }
        return new BasicPHPFormatter($value);
    }


}