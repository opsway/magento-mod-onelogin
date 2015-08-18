<?php

if (class_exists('Mage_Admin_Model_Resource_User')){

    class OpsWay_Onelogin_Model_Resource_Admin_User extends Mage_Admin_Model_Resource_User
    {
        public function loadByEmail($email)
        {
            $adapter = $this->_getReadAdapter();

            $select = $adapter->select()
                        ->from($this->getMainTable())
                        ->where('email = :email');

            $binds = array(
                'email' => $email
            );

            return $adapter->fetchRow($select, $binds);
        }
    }

} else {

    class OpsWay_Onelogin_Model_Resource_Admin_User extends Mage_Admin_Model_Mysql4_User
    {
        public function loadByEmail($email)
        {
            $adapter = $this->_getReadAdapter();

            $select = $adapter->select()
                        ->from($this->getMainTable())
                        ->where('email = :email');

            $binds = array(
                'email' => $email
            );

            return $adapter->fetchRow($select, $binds);
        }
    }

}