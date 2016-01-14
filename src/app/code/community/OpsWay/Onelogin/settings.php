<?php

define('ONELOGIN_METADATA_BASE', 'https://app.onelogin.com/saml/metadata/');
define('ONELOGIN_SSO_BASE', 'https://app.onelogin.com/trust/saml2/http-post/sso/');
define('ONELOGIN_SLO_BASE', 'https://app.onelogin.com/trust/saml2/http-redirect/slo/');

$appId = Mage::getStoreConfig('dev/onelogin/app_id');

require_once('_toolkit_loader.php');

if (!in_array(Mage::app()->getFrontController()->getAction()->getFullActionName(),
    array('cms_index_noRoute', 'cms_index_defaultNoRoute'))
) {
    $currentUrl = Mage::helper('core/url')->getCurrentUrl();
    $adminUrl = Mage::app()->getStore()->getUrl('adminhtml');
    if (stripos($currentUrl, $adminUrl) === false) {
        $currentUrl = $adminUrl;
    }
} else {
    $currentUrl = Mage::helper("adminhtml")->getUrl();
}

$settings = array (

    'strict' => false,
    'debug' => false,

    'sp' => array (
        'entityId' => 'php-saml',
        'assertionConsumerService' => array (
            'url' => $currentUrl,
        ),
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    ),
    'idp' => array (
        'entityId' => ONELOGIN_METADATA_BASE.$appId,
        'singleSignOnService' => array (
            'url' => ONELOGIN_SSO_BASE.$appId,
        ),
        'singleLogoutService' => array (
            'url' => ONELOGIN_SLO_BASE.$appId,
        ),
        'x509cert' => Mage::getStoreConfig('dev/onelogin/certificate'),
    ),

    'security' => array (
        'signMetadata' => false,
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => false,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => false,
        'wantAssertionsEncrypted' => false,
    )
);
