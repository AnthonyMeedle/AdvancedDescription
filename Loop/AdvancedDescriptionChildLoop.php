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


class AdvancedDescriptionChildLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntTypeArgument('parent'),
            Argument::createAnyTypeArgument('locale')
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
	//	$this->configureI18nProcessing($search, array('VALEURS'));
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
    public function htmlLine($objet, $structure_bo, $numline){
		$structure = $structure_bo->$numline;
		$structure_bo_return = '';
		if(null !== $structureHtml = AdvancedDescriptionConfigQuery::create()->findPk($structure->structure)){
			$cssClass='';
			$cssId='';
			$childStructure='';
			
			
			
			$child = '<div><div id="insertline_'. $numline .'">'. $childStructure .'</div><div class="text-center"><a title="Éditer une ligne" href="#advanceddescritpion_edit_dialog" class="btn btn-info btn-responsive action-btn btn-edit-advdesc" data-toggle="modal" data-numline="'. $numline .'"  data-valueid="'. $objet->getId() .'" ><i class="glyphicon glyphicon-edit"></i></a> <a title="Ajouter une ligne" href="#advanceddescritpion_creation_dialog" class="btn btn-warning btn-responsive action-btn btn-add-advdesc" data-toggle="modal" data-parent="'. $numline .'"><i class="glyphicon glyphicon-plus-sign"></i></a></div></div>';
		}
	}
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $objet) {  

            $numlineParent = $this->getParent();
 			if(!$objet->getVariables())return $loopResult;
			$structure_bo = json_decode( $objet->getVariables() );
			$locale = $this->getLocale();
			if(!$locale){
				$locale = $this->locale;
			}
			$valeurs = json_decode( $objet->getValeurs() );
			if(isset($structure_bo->child->$numlineParent)){
				foreach($structure_bo->child->$numlineParent as $numline){
					$structure = $structure_bo->$numline;
					$structure_bo_return = '';
					if(null !== $structureHtml = AdvancedDescriptionConfigQuery::create()->findPk($structure->structure)){
						$loopResultRow = new LoopResultRow($objet);
						$cssClass='';
						$cssId='';
	//					$numline++;
						$childStructure = '';
						$value = '';
						$option = '';
						$img_file = '';
						$img_alt = '';
						$img_miniature = '';
						$img_url = '';
						if(isset($valeurs->$numline->value)) $value = $valeurs->$numline->value;
						if(isset($structure->option)) $option = $structure->option;
						if(isset($valeurs->$numline->img_file)) $img_file = $valeurs->$numline->img_file;
						if(isset($valeurs->$numline->img_alt)) $img_alt = $valeurs->$numline->img_alt;
						if(isset($structure->css_class)) $cssClass = $structure->css_class;
						if(isset($structure->css_id)) $cssId = $structure->css_id;
						if($structure->structure == 10){
							$value = nl2br($value);
						}
						$balise = $structureHtml->getStructureBo();
						$balise = str_replace('{$NAME}', 'variable['. $numline .'][variable][]' , $balise);	
						$balise = str_replace('{$CSS_CLASS}', $cssClass , $balise);	
						$balise = str_replace('{$CSS_ID}', $cssId , $balise);	
						$balise = str_replace('{$CHILD}', $childStructure , $balise);	
						$structure_bo_return .= $balise;
						
						
						if($structureHtml->getId() == 3){
							$value='';
							
							$advancedDescription = new \AdvancedDescription\AdvancedDescription;
							if($img_file && is_file($advancedDescription->getUploadDir() . DS . $img_file)){
								$event = new ImageEvent();
								$event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $img_file)->setCacheSubdirectory('advancedDescription');
								$event->setWidth(100);
								$this->dispatcher->dispatch($event, TheliaEvents::IMAGE_PROCESS);
//								$structure_bo = str_replace('{$BALISE_IMAGE1}', '<img src="'. $event->getFileUrl() .'" alt="'. $img_file .'" class="advdesc-vignette-'. $numline .'-1">' , $structure_bo);
								$img_miniature = $event->getFileUrl();

								$event = new ImageEvent();
								$event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $img_file)->setCacheSubdirectory('advancedDescription');
								$this->dispatcher->dispatch($event, TheliaEvents::IMAGE_PROCESS);
								$img_url = $event->getFileUrl();

							}
						}
						
						
						$loopResultRow
							->set('ID', $objet->getId())
							->set('NUMLINE', $numline)
							->set('STRUCTURE_BO', $structure_bo_return)
							->set('STRUCTURE_ID', $structureHtml->getId())
							->set('CSS_CLASS', $cssClass)
							->set('CSS_ID', $cssId)
							->set('VALUE', $value)
							->set('OPTION', $option)
							->set('FILE', $img_file)
							->set('ALT', $img_alt)
							->set('IMAGE_URL', $img_url)
							->set('MINIATURE_URL', $img_miniature)
						;
						$loopResult->addRow($loopResultRow);
					}
				}
			}
			

		}
		return $loopResult;
	}
}