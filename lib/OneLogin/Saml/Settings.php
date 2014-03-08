<?php

/**
 * Holds SAML settings for the SamlResponse and SamlAuthRequest classes.
 *
 * These settings need to be filled in by the user prior to being used.
 */
class OneLogin_Saml_Settings
{
    const NAMEID_EMAIL_ADDRESS                 = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
    const NAMEID_X509_SUBJECT_NAME             = 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName';
    const NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName';
    const NAMEID_KERBEROS   = 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos';
    const NAMEID_ENTITY     = 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity';
    const NAMEID_TRANSIENT  = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';
    const NAMEID_PERSISTENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';

    /**
     * The URL to submit SAML authentication requests to.
     * @var string
     */
    public $idpSingleSignOnUrl = 'https://app.onelogin.com/trust/saml2/http-post/sso/';

    /**
     * The x509 certificate used to authenticate the request.
     * @var string
     */

    // The certificate for the users account in the IdP
    public $idpPublicCertificate = "";

    /**
     * The name of the application.
     * @var string
     */
    public $spIssuer = 'magento';

    /**
     * Specifies what format to return the authentication token, i.e, the email address.
     * @var string
     */
    public $requestedNameIdFormat = self::NAMEID_EMAIL_ADDRESS;
}