<?php
namespace AdvancedDescription\Loop;

use AdvancedDescription\Model\AdvancedDescriptionConfigQuery;
use AdvancedDescription\Model\AdvancedDescriptionObjectQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Element\SearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;
use Thelia\Model\ConfigQuery;
use Thelia\Core\Event\DefaultActionEvent;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;


class AdvancedDescriptionDescription extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('folder'),
            Argument::createIntListTypeArgument('category'),
            Argument::createIntListTypeArgument('product'),
            Argument::createIntListTypeArgument('content'),
            Argument::createIntTypeArgument('objid'),
            Argument::createAnyTypeArgument('objtype')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = AdvancedDescriptionObjectQuery::create();
		$this->configureI18nProcessing($search, array('VALEURS'));
        $id = $this->getId();
        if ($id) {
            $search->filterById($id, Criteria::IN);
        }
        
        $objtype = $this->getObjtype();
        $objid = $this->getObjid();
        if($objtype != null && $objid != null ){
            switch($objtype){
                case 'category': $objtype = 0; break;
                case 'product': $objtype = 1; break;
                case 'folder': $objtype = 2; break;
                case 'content': $objtype = 3; break;
            }
            $search->filterByObjtype($objtype, Criteria::IN);
            $search->filterByObjid($objid, Criteria::IN);
        }
        
        $category = $this->getCategory();
        if ($category) {
            $search->filterByObjtype(0, Criteria::IN);
            $search->filterByObjid($category, Criteria::IN);
        }
        
        $product = $this->getProduct();
        if ($product) {
            $search->filterByObjtype(1, Criteria::IN);
            $search->filterByObjid($product, Criteria::IN);
        }
        
        $folder = $this->getFolder();
        if ($folder) {
            $search->filterByObjtype(2, Criteria::IN);
            $search->filterByObjid($folder, Criteria::IN);
        }
        
        $content = $this->getContent();
        if ($content) {
            $search->filterByObjtype(3, Criteria::IN);
            $search->filterByObjid($content, Criteria::IN);
        }
        
        return $search;
    }


    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $objet) {  
            $object_id = $objet->getObjid();
            $configs = explode(',', $objet->getStructures());
            $variables = unserialize( $objet->getVariables());
            $valeur = unserialize( $objet->getVirtualColumn('i18n_VALEURS'));
            
            $numline=0;
            foreach($configs as $config_id){
                if(!$config_id)continue;
                if(null !== $config = AdvancedDescriptionConfigQuery::create()->findPk($config_id)){
                    $loopResultRow = new LoopResultRow($objet);
                    $numline++;
                    $structure_bo = $config->getStructureBo();
                    $structure_fo = $config->getStructureFo();
                    $value[0]='';
                    $urlimg='';
                    $optionTitre=0;
                    $option='';
                    $class='';
                    $option_class=1;
                    $option_class_tab=0;
                    $option_class_tab_1=0;
                    $option_class_0='';
                    $option_class_1='';
                    $option_class_2='';
                    $option_class_3='';

                    if(strpos($structure_bo, '{$VALUE')){
                        
                            $value = $valeur[$numline]['variable'];
                            $option = $variables[$numline]['option'];
                            $class = $variables[$numline]['class'];
                            if($config->getId() == 3){
                                $structure_fo = str_replace('{$TITLE0}', $value[0], $structure_fo);
                                $option_class=0;
                                $option_class_tab_1=1;

                                $structure_bo = str_replace('{$VALUE0}', $value[0], $structure_bo);
                                $file=$value[0];
                                $advancedDescription = new \AdvancedDescription\AdvancedDescription;
                                if($file && is_file($advancedDescription->getUploadDir() . DS . $file)){
                                    $event = new ImageEvent();
                                    $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                    $event->setWidth(100);
                                    $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                    $structure_bo = str_replace('{$BALISE_IMAGE1}', '<img src="'. $event->getFileUrl() .'" alt="'. $file .'" class="advdesc-vignette-'. $numline .'-1">' , $structure_bo);


                                    $event = new ImageEvent();
                                    $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                    $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                    $urlimg = $event->getFileUrl();

                                }else{
                                    $structure_bo = str_replace('{$BALISE_IMAGE1}', '' , $structure_bo);
                                }

                                $structure_fo = str_replace('{$VALUE0}', $urlimg, $structure_fo);
                            }
                            if($config->getId() == 5){
                                $structure_fo = str_replace('{$TITLE}', $value[0], $structure_fo);
                                $option_class=0;
                                $option_class_tab=1;

                                $structure_bo = str_replace('{$VALUE}', $value[0], $structure_bo);
                                $file=$value[0];
                                $advancedDescription = new \AdvancedDescription\AdvancedDescription;
                                if($file && is_file($advancedDescription->getUploadDir() . DS . $file)){
                                    $event = new ImageEvent();
                                    $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                    $event->setWidth(100);
                                    $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                    $structure_bo = str_replace('{$BALISE_IMAGE1}', '<img src="'. $event->getFileUrl() .'" alt="'. $file .'" class="advdesc-vignette-'. $numline .'-1">' , $structure_bo);


                                    $event = new ImageEvent();
                                    $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                    $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                    $urlimg = $event->getFileUrl();

                                }else{
                                    $structure_bo = str_replace('{$BALISE_IMAGE1}', '' , $structure_bo);
                                }

                                $structure_fo = str_replace('{$VALUE0}', $urlimg, $structure_fo);
                            }
                            if($config->getId() == 6){
                                $structure_fo = str_replace('{$TITLE}', $value[2], $structure_fo);
                                $option_class=0;
                                $option_class_tab=1;

                                $file=$value[2];
                                $advancedDescription = new \AdvancedDescription\AdvancedDescription;
                                if($file && is_file($advancedDescription->getUploadDir() . DS . $file)){
                                    $event = new ImageEvent();
                                    $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                    $event->setWidth(100);
                                    $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                    $structure_bo = str_replace('{$BALISE_IMAGE3}', '<img src="'. $event->getFileUrl() .'" alt="'. $file .'" class="advdesc-vignette-'. $numline .'-3">' , $structure_bo);

                                    $event = new ImageEvent();
                                    $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                    $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                    $urlimg = $event->getFileUrl();

                                }else{
                                    $structure_bo = str_replace('{$BALISE_IMAGE3}', '' , $structure_bo);
                                }



                                $structure_fo = str_replace('{$VALUE2}', $urlimg, $structure_fo);
                            }
                            if($config->getId() == 7){
                                $structure_fo = str_replace('{$TITLE0}', $value[0], $structure_fo);
                                $structure_fo = str_replace('{$TITLE1}', $value[1], $structure_fo);
                                $structure_fo = str_replace('{$TITLE2}', $value[2], $structure_fo);
                                $urlimg0 = ConfigQuery::Read('url_site') . '/media/upload/advanced-description/' . $value[0];
                                $urlimg1 = ConfigQuery::Read('url_site') . '/media/upload/advanced-description/' . $value[1];
                                $urlimg2 = ConfigQuery::Read('url_site') . '/media/upload/advanced-description/' . $value[2];
                                $option_class=0;
                                $option_class_tab=1;

                                for($i=0; $i<=2; $i++){
                                    $file=$value[$i];
                                    $advancedDescription = new \AdvancedDescription\AdvancedDescription;
                                    if($file && is_file($advancedDescription->getUploadDir() . DS . $file)){
                                        $event = new ImageEvent();
                                        $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                        $event->setWidth(100);
                                        $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                        $structure_bo = str_replace('{$BALISE_IMAGE'. ($i+1) .'}', '<img src="'. $event->getFileUrl() .'" alt="'. $file .'" class="advdesc-vignette-'. $numline .'-'. ($i+1) .'" >' , $structure_bo);


                                        $event = new ImageEvent();
                                        $event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');
                                        $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                                        $urlimg = $event->getFileUrl();
                                        $nomVar = 'urlimg' . $i;
                                        $$nomVar = $event->getFileUrl();

                                    }else{
                                        $structure_bo = str_replace('{$BALISE_IMAGE'. ($i+1) .'}', '' , $structure_bo);
                                    }
                                }

                                $structure_fo = str_replace('{$VALUE0}', $urlimg0, $structure_fo);
                                $structure_fo = str_replace('{$VALUE1}', $urlimg1, $structure_fo);
                                $structure_fo = str_replace('{$VALUE2}', $urlimg2, $structure_fo);

                            }
                        }
                    }
                    if(is_array($class)){
                        foreach($class as $index => $val)
                        $structure_fo = str_replace('{$CLASS'. $index .'}', $val, $structure_fo);
                    }else{
                        $structure_fo = str_replace('{$CLASS}', $class, $structure_fo);                
                    }
                    if($config->getId() == 1){
                        if($option === '')$option=1;
                        $structure_fo = str_replace('{$OPTION}', $option, $structure_fo);
                        $structure_fo = str_replace('{$CLASS}', 'h1', $structure_fo);
                    }
                    if($config->getId() == 1 || $config->getId() == 5 || $config->getId() == 6){
                        if($option === '')$option=2;
                        $structure_fo = str_replace('{$OPTION}', $option, $structure_fo);
                        $structure_fo = str_replace('{$CLASS}', '', $structure_fo);
                        $optionTitre=1;
                    }
                    $structure_fo = str_replace('{$CLASS}', '', $structure_fo);
                    if(is_array($value)){
                        $structure_bo = str_replace('{$VALUE}', $value[0], $structure_bo);
                        $structure_fo = str_replace('{$VALUE}', $value[0], $structure_fo);
                    }elseif(isset($value)){
                        $structure_bo = str_replace('{$VALUE}', $value, $structure_bo);
                        $structure_fo = str_replace('{$VALUE}', $value, $structure_fo);
                    }

                    $structure_bo = str_replace('{$LINE}', $numline, $structure_bo);
                    $structure_bo = str_replace('{$OPTION}', $option, $structure_bo);

                    if(is_array($value)){
                        foreach($value as $index => $val){
                            $structure_bo = str_replace('{$VALUE'. $index .'}', $val, $structure_bo);
                        //    $this->dispatcher->dispatch(TheliaEvents::DOCUMENT_PROCESS, $event);

                            $event = new DefaultActionEvent();
                            $event->__set('text', $val);
                            $this->dispatcher->dispatch('urlintexte_parsetext', $event);
                            $val = $event->__get('text');
                            $structure_fo = str_replace('{$VALUE'. $index .'}', $val, $structure_fo);
                        }
                    }

                    $structure_bo = str_replace('{$VALUE0}', '', $structure_bo);
                    $structure_bo = str_replace('{$VALUE1}', '', $structure_bo);
                    $structure_bo = str_replace('{$VALUE2}', '', $structure_bo);

                    $structure_bo = str_replace('{$NAME}', 'variable['. $numline .'][variable][]', $structure_bo);


                    if(is_array($class)){
                        foreach($class as $index => $val)
                            $loopResultRow->set('CLASS' . $index, $val);
                    }else{
                        $loopResultRow->set('CLASS', $class);
                    }
                
                    $structure_fo = str_replace('<h0 class="mt-0 "></h0>', '', $structure_fo);
                
                    $loopResultRow
                        ->set('CONFIG_ID', $config->getId())
                        ->set('TITLE', $config->getTitle())
                        ->set('STRUCTURE_BO', $structure_bo)
                        ->set('STRUCTURE_FO', $structure_fo)
                        ->set('DESCRIPTION', $structure_fo)
                        ->set('IMAGE', $config->getImage())
                        ->set('NUMLINE', $numline)
                        ->set('VARIABLE', $variable)
                        ->set('OPTION_TITRE', $optionTitre)
                        ->set('OPTION_H', $option)
                        ->set('OPTION_CLASS', $option_class)
                        ->set('OPTION_CLASS_1', $option_class_tab_1)
                        ->set('OPTION_CLASS_3', $option_class_tab)
                        ->set('CLASS0', $option_class_0)
                        ->set('CLASS1', $option_class_1)
                        ->set('CLASS2', $option_class_2)
                        ->set('CLASS3', $option_class_3)
                        ->set('ID', $objet->getId())
                        ->set('OBJTYPE', $objet->getObjtype())
                        ->set('OBJID', $objet->getObjid())
                        ->set('STRUCTURES', $objet->getStructures())
                        ->set('VARIABLES', $objet->getVariables())
                    ;

                    $loopResult->addRow($loopResultRow);
                }
            }
        
        return $loopResult;
    }
}