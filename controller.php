 c<?php
namespace Concrete\Package\OunziwProofreading;

use Concrete\Core\Page\Single;
use Core;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\Page\Page;
use Concrete\Core\Package\Package;
use Concrete\Core\View\View;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Route;

class Controller extends \Concrete\Core\Package\Package {

    protected $pkgHandle = 'ounziw_proofreading';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion = '0.9.3';
    
    public function getPackageDescription()
    {
        return t("Proofreading support using Yahoo API.");
    }
    
    public function getPackageName()
    {
        return t("Proofreading");
    }
    
    public function install()
    {
        $pkg = parent::install();
        
        $sp = Single::add('/dashboard/system/seo/proofreading', $pkg);
        if (is_object($sp)) {
            $sp->update(array('cName'=>t('Proofreading Settings'), 'cDescription'=>t('You can set Yahoo API here.') . t('Yahoo API is required to connect Yahoo and execute proofreading.')));
        }
    }
    
    public function uninstall()
    {
        $pkg = Package::getByHandle('ounziw_proofreading');
        $pkg->getConfig()->clear('concrete.ounziw_proofreading');
        parent::uninstall();
    }

    
    public function on_start()
    {
        Events::addListener('on_before_render', array($this,'check'));
        Events::addListener('on_page_view', array($this,'on_page_view'));
        $this->registerRoutes();
    }


    public function registerRoutes()
    {
        /*
         *  Registering EventList Calendar AJAX Views
         */
        Route::register(
            '/ounziw_proofreading/tools/proofreading_token',
            '\Concrete\Package\OunziwProofreading\Controller\Tools\Proofreading::return_token'
        );
        Route::register(
            '/ounziw_proofreading/tools/proofreading',
            '\Concrete\Package\OunziwProofreading\Controller\Tools\Proofreading::render'
        );
        Route::register(
            '/ounziw_proofreading/tools/proofpost',
            '\Concrete\Package\OunziwProofreading\Controller\Tools\Proofreading::yahooapi'
        );
    }
    public function check()
    {
        $ihm = Core::make('helper/concrete/ui/menu');
        $ihm->addPageHeaderMenuItem('ounziw_proofreading', 'ounziw_proofreading',
            array(
                'label' => t('Proofreading'),
                'icon' => 'edit',
                'position' => 'left',
                'href' => Url::to('/ounziw_proofreading/tools/proofreading'),
                'linkAttributes' => array(
                    'id' => 'ounziw_proofreading_link',
                    'dialog-title' => t('Proofreading'),
                    'dialog-on-close' => "location.reload();",
                    'dialog-width' => '700',
                    'dialog-height' => "600",
                    'dialog-modal' => "false",
                    'class' => 'dialog-launch'
                )
            )
        );
    }

    public function on_page_view(){
        $al = AssetList::getInstance();
        $al->register(
            'javascript', 'proofreadajax', 'js/proofreadajax.js', array(), $this->pkgHandle
        );

        $u = new User();
        if (is_object($u) && $u->checkLogin()) {
            $v = View::getInstance();
            $v->requireAsset('javascript', 'proofreadajax');
        }
    }
}