# PoetSaml2

A SAML2 Service Provider for the ProcessWire CMS/CMF

Based on OneLogin/php-saml

## Status

very alpha - please do not use in production environments. Even in dev enviroments, make sure to backup
your database and files before installing PoetSaml2.

## Requirements

- ProcessWire >= 3.0.218
- FieldTypeOptions
- FieldtypeRepeater
- PHP OpenSSL extension

## Description

This modules implements a SAML2 service provider. This means you can let an external
identity provider authenticate your site's users, e.g. Microsoft Entra ID.

You can configure multiple service providers that support different identity providers.

If have so far performed successful tests with [SamlTest.id](https://samltest.id) (a
free SAML2 testing service) and Microsoft Entra ID.

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

#### Active
  As long as this box isn't checked, your SP endpoints will not be reachable.
  Make sure to fill in all information before activating this checkbox.

#### SP Configuration

##### Our Entity Id
  The identity of our SP as it will be known to the identity provider. You will need to enter that
  in the IdP configuration.

##### Our NameIdFormat
  The name format we are requesting. For know, the code assumes that you keep the default of
  NAMEID_EMAIL_ADDRESS

##### Our X509 Certificate
We need to sign our requests to the identity provider, so we need a certificate.
You can create a self-signed cert using openssl, e.g. for 11 months validity:   
```openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -sha256 -days 395 -nodes```
You can paste the contents of cert.pem as-is.
The identity provider will get this certificate to validate our signature.

##### Private Key for our Certificate  Paste the contents of key.pem here as-is.

##### Create Self-Signed Certificate and Private Key
You can let PoetSaml2 create a self-signed certificate and key pair
with OpenSSL by checking "Create Self-Signed Certificate and Private Key"
and saving the page. *This will overwrite any previous values for
certificate and private key!*

#### IdP Configuration

The information for these fields comes from the identity provider. Depending on
the provider, you might be able to look them up in your configuration area there
or, hopefully, get their metadata XML.

##### IdP Entity Id
Just like our SP, the identity provider has its own id we need to know.

##### IdP Single Sign-On Service URL
The URL we need to call to start single sign-on at our identity provider.

##### IdP Single Logout Service URL
This URL will be called when we trigger a logout

##### IdP X509 Certificate
The certificate our identity provider signs its requests with.

##### Import IdP Metadata

If you have a metadata xml file for the identity provider or have its URL,
you can use the import function. *This will overwrite any previous values!*

### Quick Configuration

In the best case, all you need to get up and running are just a few steps:

- Create a config with a name and title
- Assign an entity Id, e.g. "https://mysite.url/pw"
- Check box to create self-signed certificate
- Download your IdP's metadata.xml file
- Check "Import Metadata from File"
- Save your configuration, then go to "SAML2 Configuration" and download
  the metadata.xml for your newly created configuration
- Import the metadata.xml into your IdP

### Testing

Once you have configured everything and activated the configuration, you will see your own
metadata.xml by opening https://your-host/path-to-processwire/your-base-url/your-config-name/metadata
or by clicking the "Download" link on the SAML2 Configuration overwiew table.

Let's assume:

- Your ProcessWire site resides under https://my.site/blog.
- You configured "saml" as the base URL in the module settings of PoetSaml2.
- You created a config named "entraid".

The link to the metadata will then be:
https://my.site/blog/saml/entraid/metadata

To initiate a login, you need to open:
https://my.site/blog/saml/entraid/start

### Testing for Real

If you want to play around without setting up things with a "real world" identity provider,
you can use [SamlTest](https://samltest.id). With that test, you have the choice of four
different, pre-configured users, "rick", "morty" and "sheldon".

You will need the following metadata.xml for their IdP, which you can find at
[their downloads page](https://samltest.id/download/#SAMLtests_IdP):

Create a configuration for samltest.id like explained above in "Quick Configuration".

Save the output of your own metadata URL to a file and upload that on samltest.id ("Testing Resources" -> "Upload Metadata").

**Note:** it may sometimes take a few minutes until your SP data gets propagated in samltest.id's cache. So if you
get an error that your Entity ID is not know, try again a little later.

Finally, go to "Testing Resources" -> "Test Your SP". Enter your SPs identity ("Our Entity Id")
in the "entityId" field and click "Go!".

Enter one of the listed credentials, and you will get a popup with the data for the entered user.

Create a user on the ProcessWire side with the same email address.

Check "Ask me again at next login", then click "Accept".

If all works well, you should be logged in to ProcessWire and see the transmitted properties.

### Troubleshooting

You will get verbose output from php-saml if you set $config->debug to true.

Make sure to also look into your browser's developer console for errors and check http responses their
if you do not get a meaningful response.

## Advanced

### Redirect URLs

When logging into ProcessWire with PoetSaml2, there are two different ways to log in:

#### From ProcessWire

This is what is technically called an "SP-initiated login". For that, you redirect your
browser to your own "start" endpoint, and you can pass on a "RelayState" parameter that
the IdP passed back to let your "login" endpoint know to where to redirect you once you
have been logged in.

That's what the "Login with xxx" buttons do which PoetSaml2 adds to the admin login form.

#### From the Identity Provider

User may just open the generic app link at the IdP, which performs all the login work
but leaves it to your site to decide where to redirect the user to.

That's when the Redirect Target configuration kicks in.

#### Default Redirect for Successful Login

You can enter any path on your website here. If you only have backend users (editors, admins)
authenticated by your IdP, you could enter "/processwire/" to take them to the admin panel.

If you also have frontend users that can't / shouldn't access the backend, you can put
any page url here. This might be "/" for the homepage or something like "/members/" if
you have a member area under that path.

#### Role Based Redirects

You may want to have different landing pages after login for members of different roles.

With this, you can e.g. let superusers land at the admin and vip members land at the vip page.

If no match is made here, it falls back to the Default Redirect URL above.

**Note:** You can even attach your own logic by hooking PoetSaml2::getLoginRedirectFor.

### Hooks

You can extend the module's functionality with the following hooks:

#### PoetSaml2::canLogin

This hook is called with the user object found in the database. You can make whatever
fine grained decisions there your heart desires and return true or false.

Example:
```php
wire()->addHookAfter("PoetSaml2::canLogin", function(HookEvent $event) {
  $user = $event->arguments('user');
  if($pages->find("template=bill, debitor=$user, unpaid=1")->count())
    $event->return = false;
});
```

#### PoetSaml2::getLoginRedirectFor

The original method checks if one of the role based URLs apply, then falls back to
the Default Target URL.

It returns a string with the found URL or _false_.

Example:
```php
wire()->addHookAfter("PoetSaml2::canLogin", function(HookEvent $event) {

  $confPage = $event->arguments('conf');
  $user = $event->arguments('user');

  if($pages->find("template=privatemessage, recipient=$user, unraid=1")->count())
    $event->return = '/messages/inbox/';
});
```

#### PoetSaml2::processSamlUserdata

This is a convenience hook that is called with the SAML-supplied user data
(aka "Claims") from the IdP. It gets passed two arguments:
- **userdata** An associative array with the raw keys and values in the claims
- **friendlyUserdata** An associative array with human readable keys and their
  associated values. Only known keys are converted. This is a WIP.

You can do whatever you want here, but its primary purpose is to let you
create new users and update data for existing users.

## 

## ToDo

- ~~Make life easier by faciliating metadata import~~
- Implement alternative IdP certificates for cert rollover
- Clean up code, especially in the loginHook method
- Give some more explanation on buzzwords, e.g. IdP-initiated or SP-initiated
- ~~Do some real world testing with MS Entry Id~~
- ~~Allow automatic creation of ProcessWire users based on SAML2 data~~ (provide hook examples)
- Integrate with frontend login
- Allow completely login protected ProcessWire instance, i.e. protected "home"
- Add JWT support or make sure to co-exist with AppApi
- Support basic authentication for select pages to provide a smooth upgrade path for legacy applications
- Add configurable mapping from passed identity to unique PW user field
- Add identity mapping hook

## License

Released under Mozilla Public License 2.0. See file [LICENSE](https://github.com/BitPoet/PoetSaml2/LICENSE) for details.

php-saml is released under its own license. See file [php-saml/LICENSE](https://github.com/BitPoet/PoetSaml2/php-saml/LICENSE) for details.

xmlseclib is released under its own license. See file [xmlseclib/LICENSE](https://github.com/robrichards/xmlseclibs) for details.



## Credits

Big kudos to OneLogin for php-saml and to Sixto Martin as its maintainer.
