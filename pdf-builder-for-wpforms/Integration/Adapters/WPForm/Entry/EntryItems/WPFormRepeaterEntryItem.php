<?php

namespace rednaoformpdfbuilder\Integration\Adapters\WPForm\Entry\EntryItems;

use rednaoformpdfbuilder\Integration\Processors\Entry\EntryItems\EntryItemBase;
use rednaoformpdfbuilder\Integration\Processors\Entry\HTMLFormatters\BasicPHPFormatter;
use rednaoformpdfbuilder\Integration\Processors\Entry\HTMLFormatters\RawPHPFormatter;

class WPFormRepeaterEntryItem extends EntryItemBase
{
    public $Value;
    public $Items=[];
    public function GetHtml($style='standard',$field=null)
    {
        $html='';
        $index=0;
        foreach($this->Items as $item)
        {
            if($index>0)
                $html.='<div style="margin-top:10px;margin-bottom: 5px">------</div>';
            $html.='<div style="margin-bottom: 20px">';
            foreach($item as $field)
            {
                $html.='<div style="margin-top: 10px">';
                if(trim($field->Field->Label)!='')
                    $html.='<label style="font-weight: bold;margin-bottom: 10px;">'.$field->Field->Label.'</label>';
                $html.=strval($field->GetHtml());
                $html.='</div>';
            }
            $html.='</div>';
            $index++;

        }

        return new RawPHPFormatter($html,null,true);

    }

    public function SetItems($items){
        $this->Items=$items;
        return $this;
    }

    protected function InternalGetObjectToSave()
    {
        return (object)array(
            'Value'=>$this->Value
        );
    }

    public function InitializeWithOptions($field, $options)
    {
        if(isset($options->Value))
            $this->Value=$options->Value;
    }

    public function SetValue($value)
    {
        $this->Value=$value;
        return $this;
    }

}