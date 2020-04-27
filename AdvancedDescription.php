<?php
namespace AdvancedDescription;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Model\ConfigQuery;
use Symfony\Component\Finder\Finder;
use Thelia\Core\Template\TemplateDefinition;

class AdvancedDescription extends BaseModule
{
    var $file;
    /** @var string */
    const DOMAIN_NAME = 'advanceddescription';
    const FOLDER_NAME = 'advancedDescription';
    const HOOK_CLASS_NAME = 'advanceddescription.hook';
    const HOOK_METHOD = 'onAdvancedDescription';
    const CATEGORY = 0;
    const PRODUCT = 1;
    const FOLDER = 2;
    const CONTENT = 3;
    const BRAND = 4;
    const HOOK = 5;

    public function postActivation(ConnectionInterface $con = null){
        $database = new Database($con->getWrappedConnection());
        $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));
        $database->insertSql(null, array(__DIR__ . '/Config/update.sql'));
    }
    
    public function getHooks() 
    {
        return array(

          array(
              "type" => TemplateDefinition::FRONT_OFFICE,
              "code" => "advanced-description",
              "title" => array(
                  "fr_FR" => "Déscription avancée",
                  "en_US" => "Advanced description",
              ),
              "description" => array(
                  "fr_FR" => "Affiche le contenu html",
                  "en_US" => "html code",
              ),
              "chapo" => array(
                  "fr_FR" => "code html",
                  "en_US" => "html code",
              ),
              "block" => false,
              "active" => true
          )
     );
    }
    
    public function getUploadDir(){
        $uploadDir = ConfigQuery::read('images_library_path');

        if ($uploadDir === null) {
            $uploadDir = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $uploadDir = THELIA_ROOT . $uploadDir;
        }

        return $uploadDir . DS . AdvancedDescription::FOLDER_NAME;
    }

	public function setFile($file){
		$this->file = $file;
	}
	public function getFile(){
		return $this->file;
	}
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
    {
        $uploadDir = $this->getUploadDir();
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($uploadDir) && $fileSystem->exists(__DIR__ . DS . 'media' . DS . 'advancedDescription')) {
            $finder = new Finder();
            $finder->files()->in(__DIR__ . DS . 'media' . DS . 'advancedDescription');

            $fileSystem->mkdir($uploadDir);

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                copy($file, $uploadDir . DS . $file->getRelativePathname());
            }
            $fileSystem->remove(__DIR__ . DS . 'media');
        }
    }
    
    
}