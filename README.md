Magento Admin - Onelogin integration (SAML)
==============

Magento module that makes it possible to login to Magento Admin via [Onelogin](http://onelogin.com) Identity provider

How does it work?
--------------

Module adds a link "Login via Onelogin" on backend login form. Following this links initiates series of redirects that are described by [SAML 2.0 standart](http://en.wikipedia.org/wiki/SAML_2.0)

User authenticates against onelogin.com application and then information about user email is sent to Magento. Magento authenticate user by email and let him in.

Usage
--------------

1. You should create application in Onelogin.com

We are using "OneLogin SAML Test (IdP)" as a base.
You can set Credentials as "Shared" and put Email you need to let all users login through one Magento account

You should copy two things:

* application ID, which can be found in url: yourcompany.onelogin.com/apps/123456
* X.509 certificate

2. Now you can copy module to your Magento folder and configure it.
Go to System->Configuration->Developer->Onelogin and put application ID and certificate that you found on previous step.

3. Flush Magento caches and you are done - you can now click on "Login via Onelogin" and see how magic happens

Credits
--------------
 - Hugely inspired by https://github.com/Flagbit/magento-openid
 - and based on SAML implementation of https://github.com/onelogin/php-saml
 - also xmlseclibs are used from https://code.google.com/p/xmlseclibs/