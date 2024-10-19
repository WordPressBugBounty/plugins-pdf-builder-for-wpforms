<?php


namespace rednaoformpdfbuilder\Integration\Adapters\WPForm\Loader;
use rednaoformpdfbuilder\core\Loader;
use rednaoformpdfbuilder\htmlgenerator\generators\PDFGenerator;
use rednaoformpdfbuilder\Integration\Adapters\WPForm\FormProcessor\WPFormFormProcessor;
use rednaoformpdfbuilder\pr\PRLoader;
use rednaoformpdfbuilder\Integration\Adapters\WPForm\Entry\Retriever\WPFormEntryRetriever;

class WPFormSubLoader extends Loader
{

    public function __construct($rootFilePath,$config)
    {
        $this->ItemId=12;
        $prefix='rednaopdfwpform';
        $formProcessorLoader=new WPFormProcessorLoader($this);
        $formProcessorLoader->Initialize();
        parent::__construct($prefix,$formProcessorLoader,$rootFilePath,$config);
        $this->AddMenu('WPForm PDF Builder',$prefix.'_pdf_builder','pdfbuilder_manage_templates','','Pages/BuilderList.php');
        $this->AddMenu('Our WPForms Plugins',$prefix.'_additional_plugins','administrator','','Pages/AdditionalPlugins.php');
        \add_filter('wpforms_frontend_confirmation_message',array($this,'AddPDFLink'),10,2);
        add_action( 'admin_notices', array($this,'NewPluginNotice') );

        if($this->IsPR())
        {
            $this->PRLoader=new PRLoader($this);
        }else{
            $this->AddMenu('Entries',$prefix.'_pdf_builder_entries','manage_options','','Pages/EntriesFree.php');
        }
    }

    public function NewPluginNotice()
    {
        global $IsShowingAutomationNotice;
        if($IsShowingAutomationNotice==true)
            return;

        $IsShowingAutomationNotice=true;

        if(get_option('automation_dont_show_again',false)==true)
            return;

        ?>
        <style type="text/css">
            .sfReviewButton{
                display: inline-block;
                padding: 6px 12px;
                margin-bottom: 0;
                font-size: 14px;
                font-weight: 400;
                line-height: 1.42857143;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -ms-touch-action: manipulation;
                touch-action: manipulation;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                background-image: none;
                border: 1px solid transparent;
                border-radius: 4px;
                color: #fff;
                background-color: #5bc0de;
                border-color: #46b8da;
                text-decoration: none;
            }

            .sfReviewButton:hover{
                color: #fff;
                background-color: #31b0d5;
                border-color: #269abc;
            }
        </style>
        <div class="notice is-dismissible notice-info sfReviewNotice" style="clear:both; padding-bottom:0;">
            <div style="padding-top: 5px;">


                <table >
                    <tbody  style="width:calc(100% - 135px);">
                    <tr>
                        <td>
                            <img style="display: inline-block;width:128px;vertical-align: top;" src="<?php echo $this->URL?>images/adIcons/automation.png">
                        </td>
                        <td>
                            <div style="display: flex; vertical-align: top;margin-left: 5px;flex-direction: column;height: 100%">

                                    <div style="padding-bottom: 1px;margin-bottom: 0;font-size: 16px;font-family: Verdana">
                                        Streamline your WPForms workflow with our new plugin: <span style="font-weight: bold">Automation for WPForms</span>
                                    </div>
                                    <ul style="list-style: circle;list-style-position: inside">
                                        <li>Add actions that WPForms alone can't do, like rejecting an entry when a value has been submitted before</li>
                                        <li>Create workflows that make your life easier, like sending your form to an approval process</li>
                                        <li>Do repetitive actions (like sending emails, updating an entry add notes etc) with a click of a button that you can add in the entries screen, another page or even directly in an email</li>
                                    </ul>
                                    <div>
                                        <a target="_blank" href="https://formwiz.rednao.com/downloads/automation-for-wpforms/" class="button button-primary">Check it out</a>
                                        <button id="closePluginNotice" class="button button-secondary">Close and don't show again</button>
                                    </div>
                            </div>
                        </td>

                    </tr>

                    </tbody>
                </table>
                <div style="clear: both;"></div>
            </div>

        </div>

        <script type="text/javascript">
            jQuery(document).ready( function($) {

                jQuery('#closePluginNotice').click(function(e){
                    e.preventDefault();
                    $.post( ajaxurl, {
                        action: 'pdf_builder_dont_show_again_notice',
                        nonce:'<?php echo wp_create_nonce('pdf_builder_dont_show_again')?>'
                    });
                    jQuery('.sfReviewNotice').remove();
                });
            });
        </script> <?php

    }

    public function GetForm($formId){
        global $wpdb;
        $results=$wpdb->get_results($wpdb->prepare("select id ID, post_title,post_content from ".$wpdb->posts." where ID=%d",$formId),'ARRAY_A');
        if(count($results)==0)
            return null;


        /** @var WPFormFormProcessor $formProcessor */
        $formProcessor=$this->ProcessorLoader->FormProcessor;
        return $formProcessor->SerializeForm($results[0]);

    }

    public function GetRootURL()
    {
        return 'https://formwiz.rednao.com/';
    }

    public function GetEntry($entryId)
    {
        if(isset(wpforms()->entry))
            $entry= wpforms()->entry->get( $entryId);
        else{
            $entry=null;
        }
        if($entry==null)
            return null;

        $entry->fields=\json_decode($entry->fields,true);
        $entry->date_created=$entry->date;
        return $entry;
    }


    public function AddPDFLink($message,$formData)
    {
        global $RNWPCreatedEntry;
        if(!isset($RNWPCreatedEntry['CreatedDocuments']))
            return $message;

        if(\strpos($message,'[wpformpdflink]')===false)
            return $message;

        $links=array();
        foreach($RNWPCreatedEntry['CreatedDocuments'] as $createdDocument)
        {
            $data=array(
              'entryid'=>$RNWPCreatedEntry['EntryId'],
              'templateid'=>$createdDocument['TemplateId'],
              'nonce'=>\wp_create_nonce($this->Prefix.'_'.$RNWPCreatedEntry['EntryId'].'_'.$createdDocument['TemplateId'])
            );
            $url=admin_url('admin-ajax.php').'?data='.\json_encode($data).'&action='.$this->Prefix.'_view_pdf';
            $links[]='<a target="_blank" href="'.esc_attr($url).'">'.\esc_html($createdDocument['Name']).'.pdf</a>';
        }

        $message=\str_replace('[wpformpdflink]',\implode($links),$message);

        return $message;


    }

    /**
     * @return WPFormEntryRetriever
     */
    public function CreateEntryRetriever()
    {
        return new WPFormEntryRetriever($this);
    }



    public function AddAdvertisementParams($params)
    {
        if(\get_option($this->Prefix.'never_show_add',false)==true)
        {
            $params['Text']='';

        }else
        {
            $params['Text'] = 'Already have a pdf and just want to fill it with the form information?';
            $params['LinkText'] = 'Try PDF Importer for WPForms';
            $params['LinkURL'] = 'https://wordpress.org/plugins/pdf-importer-for-wpform/';
            $params['Icon'] = $this->URL . 'images/adIcons/wpform.png';
            $params['PageBuilderIcon'] = $this->URL . 'images/adIcons/pagebuilder.png';
            $params['PageBuilder']=true;
        }
        return $params;
    }

    public function AddBuilderScripts()
    {
        $this->AddScript('wpformbuilder','js/dist/WPFormBuilder_bundle.js',array('jquery', 'wp-element','@builder','regenerator-runtime'));
    }

    public function GetPurchaseURL()
    {
        return 'https://formwiz.rednao.com/pdf-builder/';
    }
}


