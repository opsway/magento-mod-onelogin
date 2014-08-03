Magento Admin - Onelogin integration (SAML)
===========================================

Magento module that makes it possible to login to Magento Admin via [Onelogin](http://onelogin.com) Identity provider

Magento versions support
------------------------
Compatible with Magento CE 1.5+, Magento EE 1.9+

How does it work?
-----------------

Module adds a link "Login via Onelogin" on backend login form. Following this links initiates series of redirects that are described by [SAML 2.0 standart](http://en.wikipedia.org/wiki/SAML_2.0)

User authenticates against onelogin.com application and then information about user email is sent to Magento. Magento authenticate user by email and let him in.


Usage
--------------

1. You should create application in Onelogin.com

We are using "OneLogin SAML Test (IdP)" as a base.
You can set Credentials as "Shared" and put Email you need to let all users login through one Magento account

You should copy two things:
- application ID, which can be found in url: yourcompany.onelogin.com/apps/123456
- X.509 certificate

2. Now you can copy module to your Magento folder and configure it.
Go to System->Configuration->Developer->Onelogin and put there the required settings.

3. Flush Magento caches and you are done - you can now click on "Login via Onelogin" and see how magic happens


User auto-creation
--------------
You can enable Onelogin module to create users based on data supplied by IdP
Just enable System->Configuration->Developer->Onelogin->Create user if not exists 


You'll need to map fields in Magento to those in Onelogin.
Common field names for the attributes are:

  * Username:  User.Username
  * Email: User.email
  * First Name: User.FirstName
  * Last Name: User.LastName
  * Role: memberOf

If the required attributes are not provided by the IdP, the user account can’t be created.
(if the account already exists, only the email is required to log in).

When creating a new account, Magento will try to map the Onelogin provided role to a Magento Role.

As soon as Magento role names might not be the same as Onelogin role names, module enables you to map them. You have three separate fields to map Magento-Onelogin pairs.
You can map several Onelogin roles to single Magento role separating them with comas.

If the magento account does not have a “Magento Admin Role” like “Administrators”, then the user will not be allowed to access to the admin panel.

Credits
--------------
 - Hugely inspired by https://github.com/Flagbit/magento-openid
 - and based on SAML implementation of https://github.com/onelogin/php-saml
 - also xmlseclibs are used from https://code.google.com/p/xmlseclibs/
