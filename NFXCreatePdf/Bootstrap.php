<?php


if (!class_exists("NFXCreatePdf", false)) {
     include_once realpath(dirname(__FILE__)) . "/Logs.php";
 }

    /**
    * NFXCreatePdf
    *
    * @link http://www.nfxmedia.de
    * @copyright Copyright (c) 2016 , nfx:MEDIA
    * @author JZ ,  jahanzeab@nfxmedia.de 
    * @package nfxMEDIA
    * 
    */

    /**
    * Include mpdf library
    */
include_once(Shopware()->OldPath()."engine/Library/Mpdf/mpdf.php");

    /**
    * Shopware standard Plugin Class
    */
class Shopware_Plugins_Frontend_NFXCreatePdf_Bootstrap extends Shopware_Components_Plugin_Bootstrap {
	
	private $_document = array(); 
    
    /**
    * Reads Plugins Meta Information
    * @return string
    */
    public function getInfo() {
        
        
         $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
        if ($info) {
            return array(
                'version' => $info['currentVersion'],
                'author' => $info['author'],
                'copyright' => $info['copyright'],
                'label' => $this->getLabel(),
                'source' => $info['source'],
                'description' => $info['description'],
                'license' => $info['license'],
                'support' => $info['support'],
                'link' => $info['link'],
                'changes' => $info['changelog'],
                'revision' => '1'
            );
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
            

    }
    
    /**
    * Get name for plugin manager list
    *
    * @return string
    */
    public function getLabel() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
        if ($info) {
            return $info['label']["de"];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }
    
    /**
    * Set Capabalities in your plugin
    * @return string
    */
    public function getCapabilities() {

        return [
            'install' => true,
            'enable' => true,
            'update' => true
        ];

    }

    /**
    * Install NFXCreatePdf Plugin
    * @return string
    */
    public function install() {

        try {

            $this->createConfiguration();
            
            
            $this->nfxLicenseCheck(true);
            
            $this -> registerEvents();
                     
                 
            return [
                'success' => true,
                'invalidateCache' => array('frontend')
            ];


        } catch (Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];

        }

    }

    /**
    * Uninstall NFXCreatePdf Plugin
    * @return string
    */ 
    public function uninstall() {

        try {
            return [
                'success' => true,
                'invalidateCache' => array('frontend')
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];

        }

    }
    
    /**
    * Updates the plugin
    * @return bool
    */
    public function update($version) {
         $this->nfxLicenseCheck(true);
         $this->registerController('Backend', 'NFXCreatePdf');
            return array('success' => true,  'invalidateCache' => array('frontend'));
     }
    
    /**
    * check both nfx and Shopware licenses (at least one of them should pass)
    * @param type $throwException
    * @throws Exception
    */
     private function nfxLicenseCheck($throwException = false) {
         $return = true;
         if ($this->getName() == "NFXCreatePdf") {
             $return = $this->nfxLicenceCheck($throwException);
         } else {
            
             $return = $this->checkLicense($throwException);
         }
         return $return;
     }
 

    /**
    * Returns the current version of the plugin.
    *
    * @return string|void
    * @throws Exception
    */
     public function getVersion() {
         $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
 
         if ($info) {
             return $info['currentVersion'];
         } else {
             throw new Exception('The plugin has an invalid version file.');
         }
     }
    
    
    /**
    * This method checks back if the software has a valid licence to use the plugin. For this plugin the lic_easy.php passes all requests as long as the host is not blocked in our blacklist.
    * @param ActionHappend $action
    * @return bool
    */
     public function nfxLicenceCheck() {
         $repository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
         $shop = $repository->getActiveDefault();
         $host = $shop->getHost();
         $host = ($host) ? $host : $_SERVER['HTTP_HOST'];
 
         $handle = fopen("http://www.nfxmedia.de/tools/lic_easy.php?lic_host=" . $host . "&software=QuickOrder", "r");
         $result = fgets($handle);
         if ($result == 'nfxsaidok') {
             return true;
         } else {
             return true;
         }
     }
    
    /**
    * Register Events for the NFXCreatePdf Plugin
    * @return Void
    */
    private function registerEvents() {
		$this -> subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend_Detail', 'onPostDispatchFrontend');
		$this -> subscribeEvent('Enlight_Controller_Action_PostDispatch', 'createPdf');
	}

	
    /**
    * Configure and Create Pdf
    * get and set header, header image and footer text from configration form to pdf  
    * 
    */
    public function createPdf(Enlight_Event_EventArgs $arguments) {
		$controller = $arguments -> getSubject();
		$request = $controller -> Request();
		if($request -> getPdf && $request -> aricleId){
			$article = Shopware() -> Modules() -> Articles() -> sGetArticleById($request -> aricleId);
			$_template = clone Shopware()->Template();
			$_template -> addTemplateDir($this -> Path() . 'Views/');
            $headerTitle  = $this->Config()->get('headerTitle');
            $footerTitle  = $this->Config()->get('footerTitle');
            $mediaselection  = $this->Config()->get('mediaselection');
			$_view = $_template->createData();
			$_view -> assign('article', $article);
            $_view -> assign('pdf_header_image', $mediaselection);
			$data = $_template->fetch(dirname(__FILE__)."\Views\document\Datenblatt.tpl", $_view);
          	$this -> _document["left"] = 10;
			$this -> _document["right"] = 10;
			$this -> _document["top"] = 18;
			$this -> _document["bottom"] = 18;
			$mpdf = new mPDF("utf-8", "A4", "", "", $this -> _document["left"], $this -> _document["right"], $this -> _document["top"], $this -> _document["bottom"]);
            $mpdf->mirrorMargins = 0;
            $mpdf->showImageErrors = false;
            $mpdf->debug = false;
			$mpdf->defaultheaderfontsize = 10;
			$mpdf->defaultheaderfontstyle = B;
			$mpdf->defaultheaderline = 1;
			$mpdf->defaultfooterfontsize = 10;
			$mpdf->defaultfooterfontstyle = B;
			$mpdf->defaultfooterline = 1;
			$mpdf->SetHeader($headerTitle);
			$mpdf->SetFooter($footerTitle);
			$mpdf -> WriteHTML($data);
			$mpdf -> Output();
			exit;
		}
		return;
	}

    /**
    * Include the  'PDF Datenblatt' Link to article detail page
    */
    public function onPostDispatchFrontend(Enlight_Event_EventArgs $arguments) {
		$controller = $arguments -> getSubject();
		$request = $controller -> Request();
		$View = $controller -> view();
		$View -> addTemplateDir($this -> Path() . 'Views/');
		$id = $request -> sArticle;
		$View -> assign("aricleId", $id);
		$View -> extendsTemplate('frontend/nfx_create_pdf/detail/actions.tpl');
	}
    
    /**
    * Creates the configuration fields
    * @return void
    */
    public function createConfiguration(){
        $form = $this->Form();
     
        $form->setElement('text', 'headerTitle', 
            array(
                'label' => 'Header Title',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
                'description' => 'Enter Header Title'
            )
        );
            $form->setElement('text', 'footerTitle', 
            array(
                'label' => 'Footer Title',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
                'description' => 'Enter Footer Title'
            )
        );
            $form->setElement('mediaselection','mediaselection',
        array(
            'label' => 'Header Logo',
            'value' => NULL
        )
    );
    }

}
?>