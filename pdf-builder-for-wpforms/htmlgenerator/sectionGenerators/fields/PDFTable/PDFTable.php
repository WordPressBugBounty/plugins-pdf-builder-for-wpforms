<?php


namespace rednaoformpdfbuilder\htmlgenerator\sectionGenerators\fields\PDFTable;


use rednaoformpdfbuilder\DTO\RowItemOptions;
use rednaoformpdfbuilder\DTO\TableControlOptions;
use rednaoformpdfbuilder\htmlgenerator\sectionGenerators\fields\FieldFactory;
use rednaoformpdfbuilder\htmlgenerator\sectionGenerators\fields\PDFFieldBase;
use rednaoformpdfbuilder\htmlgenerator\tableCreator\HTMLTableCreator;
use rednaoformpdfbuilder\Utils\Sanitizer;

class PDFTable extends PDFFieldBase
{
    /** @var TableControlOptions */
    public $options;
    /** @var HTMLTableCreator */
    public $TableCreator;
    protected function InternalGetHTML()
    {
        $this->TableCreator=new HTMLTableCreator('tablefield','');
        $this->TableCreator->CreateTBody();
        $this->CreateRows();

        return $this->TableCreator->GetHTML();


    }

    private function CreateRows()
    {
        $index=0;
        foreach($this->options->TableItem->Rows as $row)
        {
            $classes=[];
            if($index==0)
                $classes[]='first';
            else
                $classes[]='notfirst';

            if($index==count($this->options->TableItem->Rows)-1)
                $classes[]='last';
            else
                $classes[]='notlast';
            $this->TableCreator->CreateRow(implode(' ',$classes));
            $this->CreateColumns($row);
            $index++;
        }
    }

    /**
     * @param $row RowItemOptions
     */
    private function CreateColumns($row)
    {

        $index=0;
        foreach($row->Columns as $column)
        {
            $classes=[];
            if($index==0)
                $classes[]='first';
            else
                $classes[]='notfirst';

            if($index==count($row->Columns)-1)
                $classes[]='last';
            else
                $classes[]='notlast';

            $alignment=Sanitizer::GetStringValueFromPath($column,['Alignment'],'middle');
            if($alignment=='')
                $alignment='middle';
            if(count($column->Fields)>0)
            {
                $html='';
                foreach ($column->Fields as $field)
                {

                    $createdField = FieldFactory::GetField($this->Loader, $this->AreaGenerator, $field, $this->entryRetriever);
                    $html.=$createdField->GetHTML(true);

                }
                $this->TableCreator->CreateRawColumn($html, implode(' ',$classes), 'td', array('width' => $column->Width . '%','vertical-align'=>$alignment));
            }
            else
                $this->TableCreator->CreateTextColumn('',implode(' ',$classes),'td',array('width'=>$column->Width.'%','vertical-align'=>$alignment));
            $index++;

        }
    }
}