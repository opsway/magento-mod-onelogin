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

class OpsWay_Onelogin_Model_Admin_Session extends Mage_Admin_Model_Session
{

    public function loginByEmail($userData, $request)
    {
        $this->login($userData['email'], null, $request, $userData);
    }

    /**
     * Try to login user in admin
     *
     * @param  string $username
     * @param  string $password
     * @param  Mage_Core_Controller_Request_Http $request
     * @return Mage_Admin_Model_User|null
     */
    public function login($username, $password, $request = null, $userData=array())
    {
        if ($request instanceof Mage_Core_Controller_Request_Http) {
            try {
                /* @var $user OpsWay_Onelogin_Model_Admin_User */
                $user = Mage::getModel('opsway_onelogin/admin_user');
                $user = $user->loadByEmail($username);

                if (!$user->getId() && Mage::getStoreConfig('dev/onelogin/provisioning')) {

                    if (!isset($userData['username']) || !isset($userData['email']) || !isset($userData['first_name']) ||
                        !isset($userData['last_name']) || !isset($userData['role']))  {
                        Mage::throwException(Mage::helper('adminhtml')->__('Successfully logged at Onelogin but required attributes were not provided.'));
                        die;
                    }

                    $data = array(
                        'username' => $userData['username'],
                        'password' => '@@@nopassword@@@',
                        'email' =>  $userData['email'],
                        'firstname' => $userData['first_name'],
                        'lastname'  => $userData['last_name'],
                        'modified'  => now(),
                        'is_active' => true
                    );
                    $user->setData($data);
                    $user = $user->save();

                    foreach ($userData['role'] as $roleName) {
                        $role = Mage::getModel('admin/role')->load($roleName, 'role_name');
                        if ($role) {
                            $user->setRoleIds(array($role->getId()))
                                ->setRoleUserId($user->getUserId())
                                ->saveRelations();
                        }
                    }
                }
                $user->login($username, '');

                if ($user->getId()) {
                    if (method_exists($this,'renewSession')) {
                        $this->renewSession();
                    } else {
                        $this->renewCompatibleSession();
                    }
                    if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                        Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                    }
                    $this->setIsFirstPageAfterLogin(true);
                    $this->setUser($user);
                    $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                    if ($requestUri = $this->_getRequestUri($request)) {
                        Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
                        header('Location: ' . $requestUri);
                        exit;
                    }
                } else {
                    Mage::throwException(Mage::helper('adminhtml')->__('Successfully logged at Onelogin but the user does not exist.'));
                    die;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::dispatchEvent(
                    'admin_session_user_login_failed',
                    array('user_name' => $username, 'exception' => $e)
                );
                if ($request && !$request->getParam('messageSent')) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $request->setParam('messageSent', true);
                }
            }
            return $user;
        }
        return false;
    }

    public function renewCompatibleSession()
    {
        $this->getCookie()->delete($this->getSessionName());
        session_regenerate_id(true);
        return $this;
    }
}
