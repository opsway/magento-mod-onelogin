<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 */

class OpsWay_Onelogin_Model_Admin_Observer extends Mage_Admin_Model_Observer
{

    private $_session;
    private $_request;

    private function _skipIfNotOnelogin() {
        if ($this->_session->isLoggedIn() ||  
             !('admin' === $this->_request->getModuleName() &&
             'onelogin' === $this->_request->getControllerName())) {
            return parent::actionPreDispatchAdmin($event);
        }
    }

    private function getAssumedUserEmail() {
        $postSAMLResponse =$this->_request->getPost("SAMLResponse");
        $settings = new OneLogin_Saml_Settings(); 
        $settings->idpSingleSignOnUrl = $settings->idpSingleSignOnUrl . Mage::getStoreConfig('dev/onelogin/app_id');
        $settings->idpPublicCertificate = Mage::getStoreConfig('dev/onelogin/certificate');

        $samlResponse = new OneLogin_Saml_Response($settings, $postSAMLResponse);
        try {
            if ($samlResponse->isValid()) {
                return $samlResponse->getNameId();
            } else {
                Mage::throwException(Mage::helper('adminhtml')->__('Invalid SAML response.'));
            }
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid SAML response: ' . $e->getMessage()));
        }

    }

    public function actionPreDispatchAdmin($event)
    {
        /* @var $session Mage_Admin_Model_Session */
        $this->_session = Mage::getSingleton('opsway_onelogin/admin_session');
        $this->_request = Mage::app()->getRequest();


        $this->_skipIfNotOnelogin();
        if ('saml' === $this->_request->getActionName()) {
            $this->_request->setDispatched(true);
        } elseif ('login' === $this->_request->getActionName() && $this->_request->getPost("SAMLResponse")) {
            $this->_session->loginByEmail($this->getAssumedUserEmail(),$this->_request);
        } else {
            return parent::actionPreDispatchAdmin($event);
        }
        $this->_session->refreshAcl();
    }
}
