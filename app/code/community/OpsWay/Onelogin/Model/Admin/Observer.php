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
            if(!isset($event)) { $event = ''; }
            return parent::actionPreDispatchAdmin($event);
        }
    }

    private function getUserData() {
        $postSAMLResponse =$this->_request->getPost("SAMLResponse");
        require(dirname(dirname(dirname(__FILE__))).'/settings.php');
        $SAMLsettings = new OneLogin_Saml2_Settings($settings);
        $samlResponse = new OneLogin_Saml2_Response($SAMLsettings, $postSAMLResponse);
        try {
            if ($samlResponse->isValid()) {
                $userData = array();
                $attrs = $samlResponse->getAttributes();
                if (!empty($attrs)) {                    
                    $usernameMap = Mage::getStoreConfig('dev/onelogin/username');
                    if (isset($attrs[$usernameMap])) {
                        $userData['username'] = $attrs[$usernameMap][0];
                    }
                    $emailMap = Mage::getStoreConfig('dev/onelogin/email');
                    if (isset($attrs[$emailMap])) {
                        $userData['email'] = $attrs[$emailMap][0];
                    }

                    $firstNameMap = Mage::getStoreConfig('dev/onelogin/firstname');
                    if (isset($attrs[$firstNameMap])) {
                        $userData['first_name'] = $attrs[$firstNameMap][0];
                    }


                    $lastNameMap = Mage::getStoreConfig('dev/onelogin/lastname');
                    if (isset($attrs[$lastNameMap])) {
                        $userData['last_name'] = $attrs[$lastNameMap][0];
                    }

                    $roleMap = Mage::getStoreConfig('dev/onelogin/role');
                    if (isset($attrs[$roleMap])) {
                        $roles = $attrs[$roleMap];
                        if (!empty($roles)) {
                            $userData['role'] = array();
                            $role1 = Mage::getStoreConfig('dev/onelogin/magentorole1');
                            $roleMap1 = explode(',', Mage::getStoreConfig('dev/onelogin/magentomapping1'));
                            $role2 = Mage::getStoreConfig('dev/onelogin/magentorole2');
                            $roleMap2 = explode(',', Mage::getStoreConfig('dev/onelogin/magentomapping2'));
                            $role3 = Mage::getStoreConfig('dev/onelogin/magentorole3');
                            $roleMap3 = explode(',', Mage::getStoreConfig('dev/onelogin/magentomapping3'));

                            foreach ($roles as $role) {
                                if (in_array($role, $roleMap1)) {
                                    $userData['role'][] = $role1;
                                }
                                if (in_array($role, $roleMap2)) {
                                    $userData['role'][] = $role2;
                                }
                                if (in_array($role, $roleMap3)) {
                                    $userData['role'][] = $role3;
                                }
                            }
                        }
                    }
                }

                if (!isset($userData['email']) || empty($userData['email'])) {
                    $userData['email'] = $samlResponse->getNameId();    
                }

                if (!isset($userData['username']) || empty($userData['username'])) {
                    $userData['username'] = $userData['email'];   
                }

                if (!isset($userData['first_name']) || empty($userData['first_name'])) {
                    $userData['first_name'] = '.';
                }
                
                return $userData;
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
            $userData = $this->getUserData();
            $this->_session->loginByEmail($userData, $this->_request);
        } else {
            return parent::actionPreDispatchAdmin($event);
        }
        $this->_session->refreshAcl();
    }
}
