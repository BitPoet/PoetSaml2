# PoetSaml2

A SAML2 Service Provider for the ProcessWire CMS/CMF

Based on OneLogin/php-saml

## Status

alpha - please do not use in production environments

## Requirements

- ProcessWire >= 3.0.218
- FieldTypeOptions

## Description

This modules implements a SAML2 service provider. This means you can let an external
identity provider authenticate your site's users, e.g. Microsoft Entra ID.

You can configure multiple service providers that support different identity providers.

### Prerequisites

You need to set ```$config->sessionCookieSameSite``` to "None" in site/config.php, otherwise SAML2 logins will not work.
This is because the session cookie (wire) needs to be sent along after your browser got redirected to your identity
provider's login form.

### Installation

Extract the [zip file](https://github.com/BitPoet/PoetSaml2/archive/refs/heads/main.zip) into the site/modules directory, then
open the ProcessWire backend and click on "Modules" -> "Refresh".

Click the "Install" button next to "PoetSaml2".

In the module's configuration, enter the base URL under which all your endpoints will live.
The default of "saml2" usually isn't too bad. Just make sure it doesn't overlap with an actual page URL.

### Configuration

You will find a new entry "SAML2 Configuration" under "Access" in the ProcessWire admin.

Click "Add Configuration" to create a new SP. Give it a name and title.

The name will be a part of the URLs of the endpoints.

Then fill in the fields in the configuration:

- Active
  As long as this box isn't checked, your SP endpoints will not be reachable.
  Make sure to fill in all information before activating this checkbox.

#### SP Configuration

- Our Entity Id
  The identity of our SP as it will be known to the identity provider. You will need to enter that
  in the IdP configuration.

- Our NameIdFormat
  The name format we are requesting. For know, the code assumes that you keep the default of
  NAMEID_EMAIL_ADDRESS

- Our X509 Certificate
  We need to sign our requests to the identity provider, so we need a certificate.
  You can create a self-signed cert using openssl, e.g. for 11 months validity:
  ```openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -sha256 -days 395 -nodes```
  You can paste the contents of cert.pem as-is.
  The identity provider will get this certificate to validate our signature.

- Private Key for our Certificate
  Paste the contents of key.pem here as-is.

#### IdP Configuration

The information for these fields comes from the identity provider. Depending on
the provider, you might be able to look them up in your configuration area there
or extract them from their metadata XML.

- IdP Entity Id
  Just like our SP, the identity provider has its own id we need to know.

- IdP Single Sign-On Service URL
  The URL we need to call to start single sign-on at our identity provider.

- IdP Single Logout Service URL
  This URL will be called when we trigger a logout

- IdP X509 Certificate
  The certificate our identity provider signs its requests with.

#### Testing

Once you have configured everything and activated the configuration, you will see your own
metadata.xml by opening https://your-host/path-to-processwire/your-base-url/your-config-name/metadata.

Let's assume:

- Your ProcessWire site resides under https://my.site/blog.
- You configured "saml" as the base URL in the module settings of PoetSaml2.
- You created a config named "entraid".

The link to the metadata will then be:
https://my.site/blog/saml/entraid/metadata

To initiate a login, you need to open:
https://my.site/blog/saml/entraid/start

#### Testing for Real

If you want to play around without setting up things with a "real world" identity provider,
you can use [SamlTest](https://samltest.id). With that test, you have the choice of four
different, pre-configured users, "rick", "morty" and "sheldon".

You will need the following information for their IdP, which you can find at
[their downloads page](https://samltest.id/download/#SAMLtests_IdP):

- entityId: maps to "IdP Entity Id"
- Redirect SSO Location: maps to "IdP Single Sign-On Service URL"
- You can replace "SSO" with "LSO" in the Redirect SSO Location to get the value for IdP Single Logout Service URL (subect to change)
- Signing Certificate maps to "IdP X509 Certificate"

Save the output of your own metadata URL to a file and upload that on samltest.id ("Testing Resources" -> "Upload Metadata").

Then go to "Testing Resources" -> "Test Your SP". Enter your SPs identity ("Our Entity Id") in the "entityId" field and click "Go!".

Enter one of the listed credentials, and you will get a popup with the data for the entered user.

Create a user on the ProcessWire side with the same email address.

Check "Ask me again at next login", then click "Accept".

If all works well, you should be logged in to ProcessWire and see the transmitted properties.

##### Troubleshooting

You will get verbose output from php-saml if you set $config->debug to true.

Make sure to also look into your browser's developer console for errors and check http responses their
if you do not get a meaningful response.

## ToDo

- Make life easier by faciliating metadata import
- Implement alternative certificates for cert rollover
- Clean up code, especially in the loginHook method
- Give some more explanation on buzzwords, e.g. IdP-initiated or SP-initiated
- Do some real world testing with MS Entry Id
- Allow automatic creation of ProcessWire users based on SAML2 data (needs config option)
- Integrate with frontend login
- Allow completely login protected ProcessWire instance, i.e. protected "home"
- Add JWT support or make sure to co-exist with AppApi
- Support basic authentication for select pages to provide a smooth upgrade path for legacy applications

## License

Released under Mozilla Public License 2.0. See file [LICENSE](https://github.com/BitPoet/PoetSaml2/LICENSE) for details.

php-saml is released under its own license. See file [php-saml/LICENSE](https://github.com/BitPoet/PoetSaml2/php-saml/LICENSE) for details.

## Credits

Big kudos to Sixto Martin as the maintainer of php-saml, and to everyone who participated.