<?php
namespace AdvancedDescription\Loop;

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


class AdvancedDescriptionObject extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntTypeArgument('objtype'),
            Argument::createIntTypeArgument('objid')
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
		
        $id = $this->getId();
        if ($id) {
            $search->filterById($id, Criteria::IN);
        }
        $objtype = $this->getObjtype();
        if ($objtype) {
            $search->filterByObjtype($objtype, Criteria::IN);
        }
        $objid = $this->getObjid();
        if ($objid) {
            $search->filterByObjid($objid, Criteria::IN);
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
            $loopResultRow = new LoopResultRow($objet);
            $structures = $objet->getStructures();
            $orderlines='';
            if($objet->getVariables()){
                $variables = unserialize($objet->getVariables());
                if(is_array($variables)){
                    $structures='';
                    foreach($variables as $index => $infos){
                        if($structures) $structures .= ',';
                        $structures .= $infos['structure'];
                        if($orderlines) $orderlines .= ',';
                        $orderlines .= $index;
                    }
                }
            }
            if(!$structures)$structures=$objet->getStructures();
            $loopResultRow
                ->set('ID', $objet->getId())
                ->set('OBJTYPE', $objet->getObjtype())
                ->set('OBJID', $objet->getObjid())
                ->set('STRUCTURES', $structures)
                ->set('VARIABLES', $objet->getVariables())
                ->set('ORDERLINES', $orderlines)
			;
			
            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}