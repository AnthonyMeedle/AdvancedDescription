<?php
namespace AdvancedDescription\Hook;

use AdvancedDescription\AdvancedDescription;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;
use Thelia\Model\ConfigQuery;
use Thelia\Model\HookQuery;

class AdvancedDescriptionHook extends BaseHook {
    public function onMainTopMenuToolsContents(HookRenderBlockEvent $event)
    {
		$event->add(array(
			"id" => "advancedDescriptionTools",
			"class" => '',
			"url" => URL::getInstance()->absoluteUrl('/admin/modules/advanceddescription/advdesc-list'),
			"title" => $this->trans("Advanced Description", array(), 'AdvancedDescription')
		));		
    }
	
    public function onAdvancedDescription(HookRenderEvent $event){
        if(null !== $hook = HookQuery::create()->filterByCode($event->getCode())->findOne()){
            $html = $this->render("AdvancedDescription/insertTemplate.html", array('objtype' => 5, 'objid' => $hook->getId()));
            $event->add($html);	
        }
    }
    public function onCategoryEditBottom(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionForm.html", array('objtype'=>'category', "objid" => $event->getArgument('category_id', null), "object_type" => $event->getArgument('type', null), "object_type_id" => AdvancedDescription::CATEGORY, "success_url"=> ConfigQuery::read("url_site") . '/admin/categories/update?category_id=' . $event->getArgument('category_id', null)));
		$event->add($html);	
    }
    public function onCategoryEditJs(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionJs.html");
		$event->add($html);	
    }
    public function onProductEditBottom(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionForm.html", array('objtype'=>'product', "objid" => $event->getArgument('product_id', null), "object_type" => $event->getArgument('type', null), "object_type_id" => AdvancedDescription::PRODUCT, "success_url"=> ConfigQuery::read("url_site") . '/admin/products/update?product_id=' . $event->getArgument('product_id', null)));
		$event->add($html);	
    }
    public function onProductEditJs(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionJs.html");
		$event->add($html);	
    }
    public function onFolderEditBottom(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionForm.html", array('objtype'=>'folder', "objid" => $event->getArgument('folder_id', null), "object_type" => $event->getArgument('type', null), "object_type_id" => AdvancedDescription::FOLDER, "success_url"=> ConfigQuery::read("url_site") . '/admin/folders/update/' . $event->getArgument('folder_id', null)));
		$event->add($html);	
    }
    public function onFolderEditJs(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionJs.html");
		$event->add($html);	
    }
    public function onContentEditBottom(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionForm.html", array('objtype'=>'content', "objid" => $event->getArgument('content_id', null), "object_type" => $event->getArgument('type', null), "object_type_id" => AdvancedDescription::CONTENT, "success_url"=> ConfigQuery::read("url_site") . '/admin/content/update/' . $event->getArgument('content_id', null)));
		$event->add($html);	
    }
    public function onContentEditJs(HookRenderEvent $event){
		$html = $this->render("AdvancedDescription/hook/advancedDescriptionJs.html");
		$event->add($html);	
    }
}