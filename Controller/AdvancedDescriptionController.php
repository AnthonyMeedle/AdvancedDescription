<?php 

namespace AdvancedDescription\Controller ;


use Propel\Runtime\ActiveQuery\Criteria;
use AdvancedDescription\AdvancedDescription;
use AdvancedDescription\Model\AdvancedDescriptionConfigQuery;
use AdvancedDescription\Model\AdvancedDescriptionConfig;
use AdvancedDescription\Model\AdvancedDescriptionObjectQuery;
use AdvancedDescription\Model\AdvancedDescriptionObject;
use Thelia\Model\ModuleHookQuery;
use Thelia\Model\ModuleHook;
use Thelia\Log\Tlog;
use Thelia\Core\Event\ActionEvent;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Model\ProductQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Tools\Password;
use Thelia\Core\Event\PdfEvent;
use Thelia\Core\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdvancedDescriptionController extends BaseAdminController
{
    public function __construct(){}
	public function noAction(){

	}
	public function formAction(){
		if(empty($_REQUEST['action']))$_REQUEST['action']='';
		
//		echo '<pre>'; print_r($_REQUEST); echo '</pre>'; exit;
		
		switch($_REQUEST['action']){
			case 'editvalue':
//				print_r($_REQUEST);
				if(null !== $advancedDescriptionObject = AdvancedDescriptionObjectQuery::create()->findPk($_REQUEST['id'])){
					$variables = json_decode( $advancedDescriptionObject->getVariables() , true );
					$numligne = $_REQUEST['numline'];
					$variables[$numligne]['css_class'] = $_REQUEST['css_class'];
					$variables[$numligne]['css_id'] = $_REQUEST['css_id'];
					if(isset($_REQUEST['structure_id']) && $_REQUEST['structure_id'] == 1){
						if(isset($_REQUEST['option'])) $variables[$numligne]['option'] = $_REQUEST['option'];
					}
					$advancedDescriptionObject->setVariables( json_encode($variables) )->save();
					
					if(isset($_REQUEST['value']) || isset($_REQUEST['img_file']) || isset($_REQUEST['img_alt'])){
						$advancedDescriptionObject->setLocale($this->getCurrentEditionLocale());
						$valeurs = json_decode( $advancedDescriptionObject->getValeurs() , true );
						if(isset($_REQUEST['value'])) $valeurs[$numligne]['value'] = $_REQUEST['value'];
						
						if(isset($_REQUEST['structure_id']) && $_REQUEST['structure_id'] == 3){
							if(isset($_REQUEST['img_file'])) $valeurs[$numligne]['img_file'] = $_REQUEST['img_file'];
							if(isset($_REQUEST['img_alt'])) $valeurs[$numligne]['img_alt'] = $_REQUEST['img_alt'];
						}
						$advancedDescriptionObject->setValeurs( json_encode($valeurs) )->save();
					}
					
					if(isset($_REQUEST['structure_id']) && $_REQUEST['structure_id'] == 10){
						$_REQUEST['value'] = nl2br($_REQUEST['value']);
					}
					
					echo json_encode($_REQUEST);
				}
				exit;
			break;
			case 'update-emplacement-hook':
                
                if(null === $moduleHook = ModuleHookQuery::create()->filterByModuleId(AdvancedDescription::getModuleId())->filterByHookId($_REQUEST['thelia_module_hook_creation']['hook_id'])->findOne()){
                    $moduleHook = new ModuleHook();
                    $moduleHook->setModuleId(AdvancedDescription::getModuleId())->setHookId($_REQUEST['thelia_module_hook_creation']['hook_id'])->setClassname(AdvancedDescription::HOOK_CLASS_NAME)->setMethod(AdvancedDescription::HOOK_METHOD)->setActive(true)->setHookActive(true)->setModuleActive(true)->setPosition($this->getLastPositionInHook($_REQUEST['thelia_module_hook_creation']['hook_id']))->save();
                }
                
                if(null !== $object = AdvancedDescriptionObjectQuery::create()->findPk($_REQUEST['id'])){
                    if($object->getObjid() != $_REQUEST['thelia_module_hook_creation']['hook_id']){
                        if(null !== $moduleHook = ModuleHookQuery::create()->filterByModuleId(AdvancedDescription::getModuleId())->filterByHookId($object->getObjid())->findOne()){
                            $moduleHook->delete();
                        }
                    }
                    $object->setObjtype(AdvancedDescription::HOOK)->setObjid($_REQUEST['thelia_module_hook_creation']['hook_id'])->save();
                }
            break;
			case 'advanceddescription_creation_dialog':
                if(null === $moduleHook = ModuleHookQuery::create()->filterByModuleId(AdvancedDescription::getModuleId())->filterByHookId($_REQUEST['thelia_module_hook_creation']['hook_id'])->findOne()){
                    $moduleHook = new ModuleHook();
                    $moduleHook->setModuleId(AdvancedDescription::getModuleId())->setHookId($_REQUEST['thelia_module_hook_creation']['hook_id'])->setClassname(AdvancedDescription::HOOK_CLASS_NAME)->setMethod(AdvancedDescription::HOOK_METHOD)->setActive(true)->setHookActive(true)->setModuleActive(true)->setPosition($this->getLastPositionInHook($_REQUEST['thelia_module_hook_creation']['hook_id']))->save();
                }
                
				if(null === $object = AdvancedDescriptionObjectQuery::create()->filterByObjtype(AdvancedDescription::HOOK)->filterByObjid($_REQUEST['thelia_module_hook_creation']['hook_id'])->findOne()){
                    $object = new AdvancedDescriptionObject();
                    $object->setObjtype(AdvancedDescription::HOOK)->setObjid($_REQUEST['thelia_module_hook_creation']['hook_id'])->save();
                }
            break;
			case 'save_lines':
				if(null === $object = AdvancedDescriptionObjectQuery::create()->filterByObjtype($_REQUEST['objtype'])->filterByObjid($_REQUEST['objid'])->findOne()){
                    $object = new AdvancedDescriptionObject();
                }
                $variables=NULL;
				/*
                if(isset($_REQUEST['variable']) && is_array($_REQUEST['variable']) && isset($_REQUEST['orderlines'])){
                    $orders = explode(',', $_REQUEST['orderlines']);
                    $variables = [];
                    $compteur=0;
                    foreach($orders as $position){
                        $compteur++;
                        $variables[$compteur]=$_REQUEST['variable'][$position];
                    }
                    $variables = json_encode($variables);
                }*/
				if(isset($_REQUEST['variable']) && is_array($_REQUEST['variable'])){
					$variables = json_encode($_REQUEST['variable']);
				}
				
                $object->setLocale($this->getCurrentEditionLocale())->setObjtype($_REQUEST['objtype'])->setObjid($_REQUEST['objid']);
				if(isset($_REQUEST['structures']))	$object->setStructures($_REQUEST['structures']);
				$object->setVariables($variables)->save();
			break;
			case 'add-img':
                $fileBeingUploaded = $this->getRequest()->files->get('file', null, true);
                if($fileBeingUploaded){
    //                if($file) unlink(__DIR__.'/../../../media/images/advancedDescription/' . $file);

                    $namefile = $this->processFile($fileBeingUploaded, $_REQUEST['objid'], 'advancedDescription', 'image', array(), ['exe'=>'exe','pdf'=>'pdf']);
                //    $seo->setFile($namefile)->save();
                }
            break;
			case 'advanceddescription_delete':
				if(isset($_REQUEST['objtype']) && isset($_REQUEST['objid'])){
					if(null !== $object = AdvancedDescriptionObjectQuery::create()->filterByObjtype($_REQUEST['objtype'])->filterByObjid($_REQUEST['objid'])->findOne()){
						$object->delete();
					}
				}
				if(isset($_REQUEST['id'])){
					if(null !== $object = AdvancedDescriptionObjectQuery::create()->findPk($_REQUEST['id'])){
						$object->delete();
					}
				}
            break;
		}
		if(!empty($_REQUEST["success_url"])) return $response = $this->generateRedirect($_REQUEST["success_url"]);
	}
    
	public function processFile(
        $fileBeingUploaded,
        $parentId,
        $parentType,
        $objectType,
        $validMimeTypes = array(),
        $extBlackList = array()
    ) {

        // Validate if file is too big
        if ($fileBeingUploaded->getError() == 1) {
            $message = 'File is too large, please retry with a file having a size less than '. ini_get('upload_max_filesize') .'.';
            throw new ProcessFileException($message, 403);
        }

        $message = null;
        $realFileName = $fileBeingUploaded->getClientOriginalName();
		$mimeType = $fileBeingUploaded->getMimeType();
        if (! empty($validMimeTypes)) {
            
            if (!isset($validMimeTypes[$mimeType])) {
                $message = 'Only files having the following mime type are allowed: ' . implode(', ', array_keys($validMimeTypes));
            } else {
                $regex = "#^(.+)\.(".implode("|", $validMimeTypes[$mimeType]).")$#i";

                if (!preg_match($regex, $realFileName)) {
                    $message = 'There\'s a conflict between your file extension "'. $mimeType .'" and the mime type "'. $fileBeingUploaded->getClientOriginalExtension() .'"';
                }
            }
        }

        if (!empty($extBlackList)) {
            $regex = "#^(.+)\.(".implode("|", $extBlackList).")$#i";

            if (preg_match($regex, $realFileName)) {
                $message = 'Files with the following extension are not allowed: '. $fileBeingUploaded->getClientOriginalExtension() .', please do an archive of the file if you want to upload it';
            }
        }

        if ($message !== null) {
            throw new ProcessFileException($message, 415);
        }
		$realFileName = $this->ereg_caracspec($realFileName);
	//	$fichier = $this->ereg_caracspec($parentId.'_'.$realFileName);
		$fichier = $this->ereg_caracspec($realFileName);
		$up = new UploadedFile($fileBeingUploaded, $realFileName, $mimeType);
		$retour = $up->move(__DIR__.'/../../../media/images/advancedDescription/', $fichier);
        return $fichier;
    }
	public function ereg_caracspec($chaine){
		$chaine = trim($chaine);
		if(function_exists('mb_strtolower'))
			$chaine = mb_strtolower($chaine);
		else
			$chaine = strtolower($chaine);
		$chaine = $this->supprAccent($chaine);
		
		$chaine = str_replace(
			array(':', ';', ',', '°'),
			array('-', '-', '-', '-'),
			$chaine
		 );
		$chaine = str_replace(" ", "-", $chaine);
		$chaine = str_replace("(", "", $chaine);
		$chaine = str_replace(")", "", $chaine);
		$chaine = str_replace(" ", "-", $chaine);
		$chaine = str_replace("'", "-", $chaine);
		$chaine = str_replace("&nbsp;", "-", $chaine);
		$chaine = str_replace("\"", "-", $chaine);
		$chaine = str_replace("?", "", $chaine);
		$chaine = str_replace("*", "-", $chaine);
		$chaine = str_replace("!", "", $chaine);
		$chaine = str_replace("+", "-", $chaine);
		$chaine = str_replace("ß", "ss", $chaine);
		$chaine = str_replace("%", "", $chaine);
		$chaine = str_replace("²", "2", $chaine);
		$chaine = str_replace("³", "3", $chaine);
		$chaine = str_replace("œ", "oe", $chaine);
		$chaine = str_replace(chr(128), "", $chaine);
		$chaine = str_replace(chr(226), "", $chaine);
		$chaine = str_replace(chr(146), "-", $chaine);
		$chaine = str_replace(chr(150), "-", $chaine);
		$chaine = str_replace(chr(151), "-", $chaine);
		$chaine = str_replace(chr(153), "", $chaine);
		$chaine = str_replace(chr(169), "", $chaine);
		$chaine = str_replace(chr(174), "", $chaine);
		$chaine = str_replace("&", "et", $chaine);
		$chaine = str_replace("?", "", $chaine);
		$chaine = str_replace("é", "e", $chaine);
		return $chaine;
	}

	public function supprAccent($texte) {
	   $texte = str_replace(    array(
									'à', 'â', 'ä', 'á', 'ã', 'å',
									'î', 'ï', 'ì', 'í',
									'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
									'ù', 'û', 'ü', 'ú',
									'é', 'è', 'ê', 'ë',
									'ç', 'ÿ', 'ñ', 'ý'
								),
								array(
									'a', 'a', 'a', 'a', 'a', 'a',
									'i', 'i', 'i', 'i',
									'o', 'o', 'o', 'o', 'o', 'o',
									'u', 'u', 'u', 'u',
									'e', 'e', 'e', 'e',
									'c', 'y', 'n', 'y'
								),
								$texte
					);
		$texte = str_replace(    array(
									'À', 'Â', 'Ä', 'Á', 'Ã', 'Å',
									'Î', 'Ï', 'Ì', 'Í',
									'Ô', 'Ö', 'Ò', 'Ó', 'Õ', 'Ø',
									'Ù', 'Û', 'Ü', 'Ú',
									'É', 'È', 'Ê', 'Ë',
									'Ç', 'Ÿ', 'Ñ', 'Ý',
								),
								array(
									'A', 'A', 'A', 'A', 'A', 'A',
									'I', 'I', 'I', 'I',
									'O', 'O', 'O', 'O', 'O', 'O',
									'U', 'U', 'U', 'U',
									'E', 'E', 'E', 'E',
									'C', 'Y', 'N', 'Y',
								),
								$texte
							);
		return $texte;
	}
    
    protected function getLastPositionInHook($hook_id)
    {
        $result = ModuleHookQuery::create()
            ->filterByHookId($hook_id)
            ->withColumn('MAX(ModuleHook.position)', 'maxPos')
            ->groupBy('ModuleHook.hook_id')
            ->select(array('maxPos'))
            ->findOne();

        return \intval($result) + 1;
    }
}
