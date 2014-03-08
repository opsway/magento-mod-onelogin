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
 */

class OpsWay_Onelogin_Model_Admin_User extends Mage_Admin_Model_User
{
    public function _construct()
    {
        $return = parent::_construct();
        $this->_init('opsway_onelogin/admin_user');
        return $return;
    }
    
    /**
     * @param string $openIdIdentifier
     * @return OpsWay_Onelogin_Model_Admin_User
     */
    public function loadByEmail($email)
    {
        $this->setData($this->getResource()->loadByEmail($email));
        return $this;
    }
    
    /**
     * Authenticate user name and password and save loaded record
     *
     * @param string $username
     * @param string $password
     * @return boolean
     * @throws Mage_Core_Exception
     */
    public function authenticate($username, $password)
    {

        $result = false;

        try {
            $this->loadByEmail($username);

            if ($this->getId()) {
                if ($this->getIsActive() != '1') {
                    Mage::throwException(Mage::helper('adminhtml')->__('This account is inactive.'));
                }
                if (!$this->hasAssigned2Role($this->getId())) {
                    Mage::throwException(Mage::helper('adminhtml')->__('Access denied.'));
                }
                $result = true;
            }

            Mage::dispatchEvent('admin_user_authenticate_after', array(
                'username' => $this->getData('username'),
                'password' => $password,
                'user'     => $this,
                'result'   => $result,
            ));
        }
        catch (Mage_Core_Exception $e) {
            $this->unsetData();
            throw $e;
        }

        if (!$result) {
            $this->unsetData();
        }
        return $result;
    }
}
