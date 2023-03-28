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


class AdvancedDescriptionFrontLoop extends BaseI18nLoop implements PropelSearchLoopInterface
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
            Argument::createAnyTypeArgument('objtype'),
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
    public function htmlLine($valeurs, $variables, $numline){
		$structure = $variables->$numline;
		$variables_return = '';
		
		
		if(null !== $structureHtml = AdvancedDescriptionConfigQuery::create()->findPk($structure->structure)){
			$cssClass='';
			$cssId='';
			$childStructure = '';
			$value = '';
			$option = '';
			$img_file = '';
			$img_alt = '';
			$img_url = '';
			$img_miniature = '';


			if($variables->child){
				foreach($variables->child as $numlineChild){
					$childStructure .= $this->htmlLine($valeurs, $variables, $numlineChild);
				}
			}
			
			if(isset($valeurs->$numline->value)) $value = $valeurs->$numline->value;
			if(isset($valeurs->$numline->option)) $option = $valeurs->$numline->option;
			if(isset($valeurs->$numline->img_file)) $img_file = $valeurs->$numline->img_file;
			if(isset($valeurs->$numline->img_alt)) $img_alt = $valeurs->$numline->img_alt;
			if(isset($structure->css_class)) $cssClass = $structure->css_class;
			if(isset($structure->css_id)) $cssId = $structure->css_id;
			if($structure->structure == 10){
				$value = nl2br($value);
			}
			if($structureHtml->getId() == 3){
				$advancedDescription = new \AdvancedDescription\AdvancedDescription;
				if($img_file && is_file($advancedDescription->getUploadDir() . DS . $img_file)){
					$event = new ImageEvent();
					$event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $img_file)->setCacheSubdirectory('advancedDescription');
					$event->setWidth(800);
					$this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
					$value = $img_url = $event->getFileUrl();
				}
			}

			$balise = $structureHtml->getStructureFo();
			$balise = str_replace('{$NAME}', 'variable['. $numline .'][variable][]' , $balise);	
			$balise = str_replace('{$CLASS}', $cssClass , $balise);	
			$balise = str_replace('{$ID}', $cssId , $balise);	
			if($cssClass)$cssClass = ' class="' . $cssClass . '"';
			if($cssId)$cssId = ' id="' . $cssId . '"';
			$balise = str_replace('{$CSS_CLASS}', $cssClass , $balise);	
			$balise = str_replace('{$CSS_ID}', $cssId , $balise);	
			$balise = str_replace('{$CHILD}', $childStructure , $balise);	
			$balise = str_replace('{$VALUE}', $value , $balise);	
			$variables_return .= $balise;

		}	
		return $variables_return;
	}
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $objet) {  
			$loopResultRow = new LoopResultRow($objet);
            $numline=0;
			$description='';
			
 			if(!$objet->getVariables())return $loopResult;
			$variables = json_decode( $objet->getVariables() );
			$locale = $this->getLocale();
			if(!$locale){
				$locale = $this->locale;
			}
			echo $locale;
			$objet->setLocale($locale);
			$valeurs = json_decode( $objet->getValeurs() );
			
			foreach($variables->racine as $numline){
				$description .= $this->htmlLine($valeurs, $variables, $numline);
			}
			$loopResultRow
				->set('ID', $objet->getId())
				->set('DESCRIPTION', $description)
			;
			$loopResult->addRow($loopResultRow);

		}
		return $loopResult;

	}
}