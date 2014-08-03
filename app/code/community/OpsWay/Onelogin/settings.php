<?php

define('ONELOGIN_METADATA_BASE', 'https://app.onelogin.com/saml/metadata/');
define('ONELOGIN_SSO_BASE', 'https://app.onelogin.com/trust/saml2/http-post/sso/');
define('ONELOGIN_SLO_BASE', 'https://app.onelogin.com/trust/saml2/http-redirect/slo/');

$appId = Mage::getStoreConfig('dev/onelogin/app_id');

require_once('_toolkit_loader.php');

$settings = array (

    'strict' => false,
    'debug' => false,

    'sp' => array (
        'entityId' => 'php-saml',
        'assertionConsumerService' => array (
            'url' => Mage::helper("adminhtml")->getUrl(),
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
