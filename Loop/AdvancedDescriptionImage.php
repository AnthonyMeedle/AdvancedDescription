<?php
namespace AdvancedDescription\Loop;
use AdvancedDescription\Model\AdvancedDescriptionConfigQuery;
use AdvancedDescription\Model\AdvancedDescriptionObjectQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Element\SearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;
use Thelia\Type\EnumListType;
use Thelia\Type\EnumType;
use Thelia\Type\TypeCollection;

class AdvancedDescriptionImage extends BaseLoop implements PropelSearchLoopInterface{
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
		 	Argument::createIntTypeArgument('width'),
            Argument::createIntTypeArgument('height'),
            Argument::createIntTypeArgument('rotation', 0),
            Argument::createAnyTypeArgument('file'),
            Argument::createAnyTypeArgument('background_color'),
            Argument::createIntTypeArgument('quality'),
            new Argument(
                'resize_mode',
                new TypeCollection(
                    new EnumType(array('crop', 'borders', 'none'))
                ),
                'none'
            ),
            Argument::createAnyTypeArgument('effects'),
            Argument::createBooleanTypeArgument('allow_zoom', false)
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
        return $search;
    }
    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult){
		$advancedDescription = new \AdvancedDescription\AdvancedDescription;
        

            $dir = $advancedDescription->getUploadDir();
            $files = array();
            if(is_dir($dir)){
                $files = scandir($dir);
            }
            $fileImg = $this->getFile();
            if($fileImg){ $files = array($fileImg); }
        
            foreach($files as $file){
                if($file == '.' || $file == '..') continue;
			if($file && is_file($advancedDescription->getUploadDir() . DS . $file)){
                $loopResultRow = new LoopResultRow();
                $iu ='';
                $oiu ='';
                $ip ='';
                $oip ='';
				$event = new ImageEvent();
				$event->setSourceFilepath($advancedDescription->getUploadDir() . DS . $file)->setCacheSubdirectory('advancedDescription');		
				switch ($this->getResizeMode()) {
					case 'crop':
						$resize_mode = \Thelia\Action\Image::EXACT_RATIO_WITH_CROP;
					break;
					case 'borders':
						$resize_mode = \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS;
					break;
					case 'none':
					default:
						$resize_mode = \Thelia\Action\Image::KEEP_IMAGE_RATIO;
				}

				// Prepare tranformations
				$width = $this->getWidth();
				$height = $this->getHeight();
				$rotation = $this->getRotation();
				$background_color = $this->getBackgroundColor();
				$quality = $this->getQuality();
				$effects = $this->getEffects();

				if (!is_null($width)) {
					$event->setWidth($width);
				}
				if (!is_null($height)) {
					$event->setHeight($height);
				}
				$event->setResizeMode($resize_mode);
				if (!is_null($rotation)) {
					$event->setRotation($rotation);
				}
				if (!is_null($background_color)) {
					$event->setBackgroundColor($background_color);
				}
				if (!is_null($quality)) {
					$event->setQuality($quality);
				}
				if (!is_null($effects)) {
					$event->setEffects($effects);
				}

				$event->setAllowZoom($this->getAllowZoom());

				$this->dispatcher->dispatch($event, TheliaEvents::IMAGE_PROCESS);
                $iu =  $event->getFileUrl();
                $oiu = $event->getOriginalFileUrl();
                $ip = $event->getCacheFilepath();
                $oip = $event->getSourceFilepath();

                $loopResultRow
                    ->set('FILE', $file)
                    ->set("IMAGE_URL", $iu)
                    ->set("ORIGINAL_IMAGE_URL", $oiu)
                    ->set("IMAGE_PATH", $ip)
                    ->set("ORIGINAL_IMAGE_PATH", $oip)
                ;
                $loopResult->addRow($loopResultRow);
            }
        }
        return $loopResult;
    }
}