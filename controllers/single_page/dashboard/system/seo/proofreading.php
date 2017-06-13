<?php    
namespace Concrete\Package\OunziwProofreading\Controller\SinglePage\Dashboard\System\Seo;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Package\Package;

class Proofreading extends DashboardPageController {

    public function view()
    {
        $pkg = Package::getByHandle('ounziw_proofreading');
        $proofreading = $pkg->getConfig()->get('concrete.ounziw_proofreading');
        $proofreading_ssl = $pkg->getConfig()->get('concrete.ounziw_proofreading_ssl');
        $proofreading_class = $pkg->getConfig()->get('concrete.ounziw_proofreading_class');
        $this->set('proofreading', $proofreading);
        $this->set('proofreading_ssl', $proofreading_ssl);
        $this->set('proofreading_class', $proofreading_class);
    }

    public function updated()
    {
        $this->set('message', t("Settings saved."));
        $this->view();
    }
    
    public function save_settings()
    {
        if ($this->isPost()) {
            if (!$this->token->validate("save_settings")) {
                $this->set('error', array($this->token->getErrorMessage()));
            }

            $proofreading = $this->post('ounziw_proofreading');
            $proofreading_ssl = $this->post('ounziw_proofreading_ssl');
            $proofreading_class = $this->post('ounziw_proofreading_class');

            $str_for_class = '0123456789abcdefghijklmnopqrstuvxwyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_.#';
            if (strlen($proofreading_class) !== strspn($proofreading_class,$str_for_class)) {
                $this->error->add(t('Disallowed charcters found in HTML tag and/or ID/class.  Allowed characters: number, alphabets, hyphen underscore, dot, and sharp.'));
            }
            if (!$this->error->has()) {
                $pkg = Package::getByHandle('ounziw_proofreading');
                $pkg->getConfig()->save('concrete.ounziw_proofreading', $proofreading);
                $pkg->getConfig()->save('concrete.ounziw_proofreading_ssl', $proofreading_ssl);
                $pkg->getConfig()->save('concrete.ounziw_proofreading_class', $proofreading_class);
                $this->redirect('/dashboard/system/seo/proofreading','updated');
            }
        }
        $this->view();
    }
}