<?php
namespace AdvancedDescription\Loop;

use AdvancedDescription\Model\AdvancedDescriptionConfigQuery;
use AdvancedDescription\Model\AdvancedDescriptionObjectQuery;
use AdvancedDescription\Loop\AdvancedDescriptionImage;
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
use Thelia\Model\LangQuery;
use Thelia\Core\Event\DefaultActionEvent;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;


class AdvancedDescriptionConfig extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntTypeArgument('numline'),
            Argument::createIntTypeArgument('object_id'),
            Argument::createIntTypeArgument('lang')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = AdvancedDescriptionConfigQuery::create();
		
        $id = $this->getId();
        if ($id) {
            $search->filterById($id, Criteria::IN);
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
        $locale='fr_FR';
        if($this->getLang()){
            if(null !== $lang = LangQuery::create()->findPk($this->getLang())){
                $locale=$lang->getLocale();
            }
        }
        
        foreach ($loopResult->getResultDataCollection() as $objet) {
            $loopResultRow = new LoopResultRow($objet);
            $structure_bo = $objet->getStructureBo();
            $structure_fo = $objet->getStructureFo();
            $value[0]='';
            $numline = $this->getNumline();
            $urlimg='';
            $optionTitre=0;
            $option='';
            $class='';
            $option_class=1;
            $option_class_tab_1=0;
            $option_class_tab=0;
            $option_class_0='';
            $option_class_1='';
            $option_class_2='';
            $option_class_3='';
            if(strpos($structure_bo, '{$VALUE')){
                $object_id = $this->getObjectId();
                
                if(null !== $object = AdvancedDescriptionObjectQuery::create()->findPk($object_id)){
                    $object->setLocale($locale);
                    $structures = explode($object->getStructures(), ',');
                    $variables = unserialize( $object->getVariables());
                    $valeurs = unserialize( $object->getValeurs());
                    if(isset($valeurs[$numline]['variable']))
                        $value = $valeurs[$numline]['variable'];
                    else
                        $value = $variables[$numline]['variable'];
                    $option = $variables[$numline]['option'];
                    $class = $variables[$numline]['class'];
                    if($objet->getId() == 3){
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
                    if($objet->getId() == 5){
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
                    if($objet->getId() == 6){
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
                    if($objet->getId() == 7){
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
            if($objet->getId() == 1){
                if($option === '')$option=1;
                $structure_fo = str_replace('{$OPTION}', $option, $structure_fo);
                $structure_fo = str_replace('{$CLASS}', 'h1', $structure_fo);
            }
            if($objet->getId() == 1 || $objet->getId() == 5 || $objet->getId() == 6){
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
            $structure_bo = str_replace('{$BALISE_IMAGE1}', '', $structure_bo);
            $structure_bo = str_replace('{$BALISE_IMAGE2}', '', $structure_bo);
            $structure_bo = str_replace('{$BALISE_IMAGE3}', '', $structure_bo);

            $structure_bo = str_replace('{$NAME}', 'variable['. $numline .'][variable][]', $structure_bo);
            
            
            $loopResultRow
                ->set('ID', $objet->getId())
                ->set('TITLE', $objet->getTitle())
                ->set('STRUCTURE_BO', $structure_bo)
                ->set('STRUCTURE_FO', $structure_fo)
                ->set('IMAGE', $objet->getImage())
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
			;
            
            if(is_array($class)){
                foreach($class as $index => $val)
                    $loopResultRow->set('CLASS' . $index, $val);
            }else{
                $loopResultRow->set('CLASS', $class);
            }
            
			
            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}