<?php namespace ProcessWire;

class PoetSaml2 extends WireData implements Module, ConfigurableModule {
	
	protected static $templateName = 'poetsaml2config';
	
	protected static $endpoints = [
		'metadata'		=>	[
			'pattern'		=>	'{sp}',
			'method'		=>	'metadataHook'
		],
		'logout'		=>	[
			'pattern'		=>	'{sp}',
			'method'		=>	'logoutHook'
		],
		'login'			=>	[
			'pattern'		=>	'{sp}',
			'method'		=>	'loginHook'
		],
		'start'			=>	[
			'pattern'		=>	'{sp}',
			'method'		=>	'startHook'
		]
	];
	
	
	protected static $nameIdFormats = [
			'NAMEID_EMAIL_ADDRESS'					=>	'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
			'NAMEID_X509_SUBJECT_NAME'				=>	'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName',
			'NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME'	=>	'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName',
			'NAMEID_UNSPECIFIED'					=>	'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
			'NAMEID_KERBEROS'						=>	'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos',
			'NAMEID_ENTITY'							=>	'urn:oasis:names:tc:SAML:2.0:nameid-format:entity',
			'NAMEID_TRANSIENT'						=>	'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
			'NAMEID_PERSISTENT'						=>	'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
			'NAMEID_ENCRYPTED'						=>	'urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted'
	];
	
	protected static $urnMapping = [
		'urn:oasis:names:tc:SAML:attribute:subject-id' => [
			'friendly' => 'identifier',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:0.9.2342.19200300.100.1.1' => [
			'friendly' => 'uid',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:0.9.2342.19200300.100.1.3' => [
			'friendly' => 'mail',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:2.5.4.4' => [
			'friendly' => 'sn',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:2.16.840.1.113730.3.1.241' => [
			'friendly' => 'displayName',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:2.5.4.20' => [
			'friendly' => 'telephoneNumber',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:2.5.4.42' => [
			'friendly' => 'givenName',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'https://samltest.id/attributes/role' => [
			'friendly' => 'role',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		],
		'urn:oid:1.3.6.1.4.1.5923.1.1.1.7' => [
			'friendly' => 'eduPersonEntitlement',
			'nameformat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri'
		]
	];

	
	
	public static function getModuleInfo() {
		return [
			"title"			=>	__('Poet SAML2', __FILE__),
			"summary"		=>	__('A SAML2 Service Provider implementation based on OneLogin/php-saml'),
			"version"		=>	'0.0.23',
			"requires"		=>	'PHP>=7.2.0,ProcessWire>=3.0.218,FieldtypeOptions,FieldtypeRepeater',
			"installs"		=>	'ProcessPoetSaml2',
			"autoload"		=>	true
		];
	}
	
	public function __constrcut() {
		$this->set('urlBase', '');
		parent::__construct();
	}
	
	public function init() {
		require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
		require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'onelogin' . DIRECTORY_SEPARATOR . 'php-saml' . DIRECTORY_SEPARATOR . '_toolkit_loader.php');
	}
	
	public function ready() {
		if(!$this->modules->isInstalled('ProcessPoetSaml2'))
			return;
		if($this->urlBase) {
			$this->addEndpointHooks();
		}
		$this->addHookAfter('Pages::saveReady', $this, 'pageSaveProcessing');
		$this->addHookAfter('ProcessLogin::buildLoginForm', $this, 'addSamlLoginButtons');
	}
	
	
	public function addSamlLoginButtons(HookEvent $event) {
		
		$configs = $this->pages->find('template=' . self::$templateName . ', ps2Active=1, include=all');
		
		if($configs->count() > 0) {
			
			$form = $event->return;
			
			$colWidth = floor(100 / ($configs->count() > 4 ? 4 : $configs->count()));

			$wrap = new InputfieldWrapper();
			$wrap->label = $this->_('External Login Providers');
			
			foreach($configs as $conf) {
				
				$httpHostBase = ($this->config->https ? 'https' : 'http') . '://' . $this->config->httpHost;
				$myUrl = $httpHostBase . $this->urls->root . $this->urlBase . '/' . $conf->name . '/start';
				$adminUrl = $httpHostBase . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
				
				$f = $this->modules->get('InputfieldButton');
				$f->attr('href', $myUrl . '?RelayState=' . urlencode($adminUrl));
				$f->text = sprintf($this->_('Log in with %s'), $conf->title);
				$f->columnWidth = $colWidth;
				$wrap->add($f);
				
			}
			
			$form->append($wrap);
		}
		
	}
	
	
	public function pageSaveProcessing(HookEvent $event) {
		
		$page = $event->arguments('page');
		
		if($page->template->name !== self::$templateName)
			return;
		
		// Request to create a self-signed certificate
		if($page->ps2CreateSelfSignedCert) {
			
			$page->ps2CreateSelfSignedCert = 0;
			
			try {
				
				$newCert = '';
				$newKey  = '';
				
				$cnfPath = __DIR__ . DIRECTORY_SEPARATOR . 'openssl.cnf';
				
				$key = openssl_pkey_new([
					'private_key_bits'		=>	4096,
					'default_md'			=>	"sha256",
					'private_key_type'		=>	\OPENSSL_KEYTYPE_RSA,
					'config'				=>	$cnfPath
				]);
				
				if($key === false) {
						$this->error($this->_('Unable to generate private key') . ': ' . openssl_error_string());
				} else {

					$csr = openssl_csr_new([
						"countryName"					=>	"DE",
						"commonName"					=>	strtok($this->config->httpHost, ':') ?: $_SERVER['SERVER_NAME']
					], $key, ['digest_alg' => 'sha256', 'config' => $cnfPath]);
					
					if($csr === false) {
						
						$this->error(sprintf($this->_('Unable to create CSR for host %s'), strtok($this->config->httpHost) . ': ' . openssl_error_string()));
						
					} else {
						
						$x509 = openssl_csr_sign($csr, null, $key, $days=390, ['digest_alg' => 'sha256', 'config' => $cnfPath]);
						
						if($x509 === false) {
							
							$this->error($this->_('Unable to sign X509 certificate') . ': ' . openssl_error_string());
							
						} else {
						
							openssl_x509_export($x509, $newCert);
							openssl_pkey_export($key, $newKey, null, ['config' => $cnfPath]);
							
							$page->ps2OurX509Cert = $newCert;
							$page->ps2OurPrivateKey = $newKey;

							$this->message($this->_("Self-Signed certificate and private key created"));
						}

					}
					
				}
				
			} catch(Exception $e) {
				$this->error('Unable to generate self-signed certificate: ' . $e->getMessage());
			}
		}
		
		// Request to import metadata from XML file
		if($page->ps2IdpImportXmlFile) {
			
			$page->ps2IdpImportXmlFile = 0;
			
			if($page->ps2IdpXmlFile->count() > 0) {
				
				try {
					
					$file = $page->ps2IdpXmlFile->first();
					$data = $this->parseMetadataXml($file->filename, true);
					
					$this->assignIdpData($page, $data);
					
				} catch(Exception $e) {
					
					$this->error($e->getMessage());
					
				}
				
				$page->getUnformatted('ps2IdpXmlFile')->delete($file);
				
			}
			
		}
		
		// Request to import metadata from URL
		if($page->ps2IdpImportUrl) {
			
			$page->ps2IdpImportUrl = 0;
			
			if($page->psIdPUrl) {
				
				try {
					
					$http = new WireHttp();
					$data = $http->get($page->ps2IdPUrl);
					
					$this->assignIdpData($page, $data);
						
				} catch(Exception $e) {
					
					$this->error($e->getMessage());
					
				}
				
				$page->ps2IdPUrl = '';
				
			}
			
		}
		
		if($page->isChanged('ps2OurX509Cert'))
			$page->ps2OurX509CertBin = $this->extractCert($page->ps2OurX509Cert);
		
		if($page->isChanged('ps2OurPrivateKey'))
			$page->ps2OurPrivateKeyBin = $this->extractKey($page->ps2OurPrivateKey);
		
		if($page->isChanged('ps2IdpX509Cert'))
			$page->ps2IdpX509CertBin = $this->extractCert($page->ps2IdpX509Cert);
		
	}
	
	
	protected function assignIdpData($page, $data) {
		
		if(isset($data['idp']['entityId']))
			$page->ps2IdpEntityId = $data['idp']['entityId'];
		else
			$this->warning($this->_("Could not determine entity id from XML"));
		
		if(isset($data['idp']['x509cert']))
			$page->ps2IdpX509Cert = implode("\n", preg_split('/\s+/', $data['idp']['x509cert']));
		elseif(isset($data['idp']['x509certMulti']['encryption']))
			$page->ps2IdpX509Cert = implode("\n", preg_split('/\s+/', $data['idp']['x509certMulti']['encryption'][0]));
		else
			$this->warning($this->_("Could not determine certificate from XML"));
		
		if(isset($data['idp']['singleSignOnService']['url']))
			$page->ps2IdpSOSUrl = $data['idp']['singleSignOnService']['url'];
		else
			$this->warning($this->_("Could not determine sign-on service URL from XML"));

		if(isset($data['idp']['singleLogoutService']['url']))
			$page->ps2IdpSLSUrl = $data['idp']['singleLogoutService']['url'];
		else
			$this->warning($this->_("Could not determine logout service URL from XML"));

	}
	
	
	protected function parseMetadataXml($xml, $isFile = false) {
		
		if($isFile)
			$parsed = \OneLogin\Saml2\IdPMetadataParser::parseFileXML($xml);
		else
			$parsed = \OneLogin\Saml2\IdPMetadataParser::parseXML($xml);
		
		return $parsed;
		
	}
	

	protected function extractCert($pem) {

		if(empty($pem))
			return '';

		// This removes the PEM certificate header/footer and joins all lines to one
		$pem = trim($pem);
		$pem = str_replace('-----BEGIN CERTIFICATE-----', '', $pem);
		$pem = str_replace('-----END CERTIFICATE-----', '', $pem);
		$pem = implode('', preg_split('/\\r?\\n/', $pem));
		return $pem;
		
	}
	
	protected function extractKey($pem) {

		if(empty($pem))
			return '';

		// This removes the PEM certificate header/footer and joins all lines to one
		$pem = trim($pem);
		$pem = str_replace('-----BEGIN PRIVATE KEY-----', '', $pem);
		$pem = str_replace('-----END PRIVATE KEY-----', '', $pem);
		$pem = implode('', preg_split('/\\r?\\n/', $pem));
		return $pem;
		
	}
	
	protected function addEndpointHooks() {
		foreach(self::$endpoints as $name => $ep) {
			$epPath = '/' . $this->urlBase . '/' . $ep['pattern'] . '/' . $name;
			$this->wire->addHook($epPath, $this, $ep['method']);
		}
	}
	
	public function metadataHook(HookEvent $event) {
		
		$sp = $event->arguments('sp');
		
		$this->initAuth($sp);

		$settings = $this->auth->getSettings();
	    $metadata = $settings->getSPMetadata();
	    $errors = $settings->validateMetadata($metadata);
	    
	    if (empty($errors)) {
	        header('Content-Type: text/xml');
	        if($this->input->get->download)
	        	header('Content-Disposition: download; filename="metadata.xml"');
	        echo $metadata;
	        exit;
	    }
	    
        throw new \OneLogin\Saml2\Error(
            'Invalid SP metadata: '.implode(', ', $errors),
            \OneLogin\Saml2\Error::METADATA_SP_INVALID
        );

	}
	
	
	protected function startHook(HookEvent $event) {
		
		$sp = $event->arguments('sp');
		
		session_regenerate_id();
		
		if (!$this->session->samlUserdata) {
			
			$this->initAuth($sp);

			$settings = $this->auth->getSettings();

		    $authRequest = new \OneLogin\Saml2\AuthnRequest($this->auth->getSettings());
		    $samlRequest = $authRequest->getRequest();

			$relayState = $this->rawSettings['sp']['assertionConsumerService']['url'];
			if($this->input->get->RelayState)
				$relayState = $this->input->get->RelayState;

		    $parameters = array('SAMLRequest' => $samlRequest);
		    $parameters['RelayState'] = $relayState;

		    $idpData = $settings->getIdPData();
		    $ssoUrl = $idpData['singleSignOnService']['url'];
		    $url = \OneLogin\Saml2\Utils::redirect($ssoUrl, $parameters, true);

		    header("Location: $url");
		    exit;
		    
		} else {
			
			$this->session->location($this->config->urls->root);
			
		}
	}
	
	
	public function loginHook(HookEvent $event) {
		
		$session = $this->session;
		$post = $this->input->post;
		
		$sp = $event->arguments('sp');
		
		$this->initAuth($sp);
		$auth = $this->auth;
		
		if (isset($session->AuthNRequestID)) {
		    $requestID = $session->AuthNRequestID;
		} else {
		    $requestID = null;
		}

		$auth->processResponse($requestID);
		$session->remove('AuthNRequestID');

		$errors = $auth->getErrors();

		if (!empty($errors)) {
			echo "<html><body>";
			echo "<p>Errors found</p>";
		    echo '<p>' . implode(', ', $errors) . '</p>';
		    echo "</html>";
		    exit();
		}

		if (!$auth->isAuthenticated()) {
			http_response_code(401);
			echo "<html><body>";
			echo "<p>Errors found</p>";
		    echo "<p>Not authenticated</p>";
		    echo "</html>";
		    exit();
		}

		$uname = $auth->getNameId();
		$user = $this->users->get('email=' . $this->sanitizer->email($uname));
		if($user instanceof NullPage || $user->isGuest()) {
			echo "<html><body>";
			echo "<p>Error!</p>";
		    echo "<p>User not found in local database!</p>";
		    echo "</html>";
		    exit();
		}
		
		if($this->user->isLoggedIn)
			$session->logout(false);

		$session->samlUserdata = $auth->getAttributes();
		$session->samlNameId = $auth->getNameId();
		$session->samlNameIdFormat = $auth->getNameIdFormat();
		$session->samlNameidNameQualifier = $auth->getNameIdNameQualifier();
		$session->samlNameidSPNameQualifier = $auth->getNameIdSPNameQualifier();
		$session->samlSessionIndex = $auth->getSessionIndex();
		
		$attributes = $auth->getAttributes();
		$friendlyData = [];
		foreach($attributes as $k => $v) {
			if(array_key_exists($k, self::$urnMapping))
				$friendlyData[self::$urnMapping[$k]['friendly']] = $v;
		}
		$this->processSamlUserdata($attributes, $friendlyData);

		$canLogin = $this->canLogin($user);

		if($canLogin === true) {

			$session->forceLogin($user);

		} else {
			
			echo "<html><body>";
			echo "<p>Not allowed to log in</p>";
		    echo "<p>$canLogin</p>";
		    echo "</html>";
		    exit();
		    
		}
		
		$this->users->setCurrentUser($user);
		$this->wire('user', $user);

		// If we have a RelayState set, we redirect there		
		if (isset($post->RelayState) && \OneLogin\Saml2\Utils::getSelfURL() !== $post->RelayState) {
		    $session->redirect($post->RelayState);
		}

		// Otherwise, we check if we have some kind of redirect URL set, either generally
		// or by role
		$conf = $this->confPage;
		
		$redirUrl = $this->getLoginRedirectFor($conf, $user);
		if($redirUrl !== false)
			$this->session->redirect($redirUrl);
			
		
		$attributes = $session->samlUserdata;
		$nameId = $session->samlNameId;

		echo '<h1>Identified user: '. htmlentities($nameId) .'</h1>';
		echo "<h2>Logged in as user: " . $this->user->name . "</h2>";

		if (!empty($attributes)) {
		    echo '<h2>' . $this->_('User attributes:') . '</h2>';
		    echo '<table><thead><th>' . $this->_('Name') . '</th><th>' . $this->_('Values') . '</th></thead><tbody>';
		    foreach ($attributes as $attributeName => $attributeValues) {
		    	if(array_key_exists($attributeName, self::$urnMapping))
		    		echo '<tr><td>' . htmlentities(self::$urnMapping[$attributeName]['friendly']) . '</td><td><ul>';
		    	else
		        	echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
		        foreach ($attributeValues as $attributeValue) {
		            echo '<li>' . htmlentities($attributeValue) . '</li>';
		        }
		        echo '</ul></td></tr>';
		    }
		    echo '</tbody></table>';
		} else {
		    echo _('No attributes found.');
		}		

		exit;

	}
	
	
	/**
	 * Hookable method for actions based on the SAML2 claims returned by the IdP.
	 *
	 * You could hook into this method to create users on the fly.
	 *
	 * @param Array $userdata Claims as returned by the IdP
	 * @param Array $friendlyUserdata Claims where a URI/URN could be resolved to a human readable name
	 * @return void
	 */
	public function ___processSamlUserdata($userdata, $friendlyUserdata) {
		
		// Nothing yet
		
	}
	
	
	/**
	 * Hookable method that determines the login success redirect URL
	 * for the logged in user.
	 *
	 * @param Page $conf Configuration page
	 * @param User $user SAML2 logged-in user
	 * @return string Redirect URL relative to site root
	 */
	public function ___getLoginRedirectFor($conf, $user) {

		foreach($conf->ps2RoleRedirects->sort('-sort') as $roleSort) {
			if($user->hasRole($roleSort->ps2RedirRole->name)) {
				return $roleSort->ps2RedirUrl;
			}
		}

		if(isset($conf->ps2RedirDefault))
			return rtrim($this->config->urls->root, '/') . $conf->ps2RedirDefault;
		
		return false;
		
	}
	
	
	/**
	 * Hook for extra checks whether a user is allowed to log in
	 *
	 * @param User $user
	 * @return true|string Either boolean true or a string with a rejection reason
	 */
	public function ___canLogin($user) {
		return true;
	}
	
	
	public function logoutHook(HookEvent $event) {
		// Perform logout
		$session = $this->session;
		$post = $this->input->post;
		
		$sp = $event->arguments('sp');
		
		$this->initAuth($sp);
		$auth = $this->auth;

		if($session->LogoutRequestID) {
		    $requestID = $sesion->LogoutRequestID;
		} else {
		    $requestID = null;
		}

		$auth->processSLO(true, $requestID);

		$errors = $auth->getErrors();

		if (empty($errors)) {
		    echo 'Sucessfully logged out';
		} else {
		    echo implode(', ', $errors);
		}		
	}
	
	
	public function getModuleConfigInputfields(InputfieldWrapper $inputfields) {
		
		$modules = $this->wire()->modules;
		
		$myUrl = ($this->config->https ? 'https' : 'http') . '://' . $this->config->httpHost;
		
		$f = $modules->get('InputfieldText');
		$f->attr('name', 'urlBase');
		$f->label = $this->_('Endpoints URL');
		$f->description = sprintf($this->_('All SAML2 endpoints will be reachable under this path, i.e. "%s%s<<Endpoints URL>>/".'), $myUrl, $this->config->urls->root);
		$f->notes = 
			$this->_('Endpoint names must consist of ascii characters, digits and optional slashes, with the following constraints:') . PHP_EOL . PHP_EOL .
			'- ' . $this->_('They must start with an ascii letter') . PHP_EOL .
			'- ' . $this->_('They must not end with a slash') . PHP_EOL .
			'- ' . $this->_('They must not contain consecutive slashes') . PHP_EOL .
			PHP_EOL .
			$this->_('Valid exmaples: "saml2", "SAML2/auth", "a/b/c")') . PHP_EOL .
			$this->_('Invalid examples: "2login", "login/"') . PHP_EOL
		;
		$f->attr('value', $this->urlBase);
		$inputfields->add($f);
		
		return $inputfields;
		
	}
	
	
	public function initAuth($name) {
		
		$settings = $this->buildSettings($name);
		
		if($settings === false)
			throw new WireException('Unable to initialize SAML2 service provider ' . $sp . ': Building settings failed');
		
		$auth = new \OneLogin\Saml2\Auth($settings);
		if(! $auth)
			throw new WireException('Unable to initialize SAML2 service provider ' . $sp);
		
		$this->auth = $auth;
		
		return true;
		
	}
	
	
	public function buildSettings($name) {
		
		$name = $this->sanitizer->pageName($name);
		$conf = $this->pages->get('template=' . self::$templateName . ', ps2Active=1, name=' . $name);
		
		if($conf instanceof NullPage)
			throw new WireException('Unable to initialize SAML2 service provider ' . $name. ': No active configuration with this name');
		
		$this->confPage = $conf;

		$nameIdOptions = $this->modules->get('FieldtypeOptions')->getOptions("ps2NameIdFormat");
		$nameIdFormat = $nameIdOptions->get('id=' . $conf->ps2NameIdFormat)->value;
		
		$myUrl = ($this->config->https ? 'https' : 'http') . '://' . $this->config->httpHost . $this->urls->root . $this->urlBase . '/' . $name;
		
		$settings = [
			'debug'		=>	$this->config->debug,
			'sp'		=>	[
				'entityId'						=>	$conf->ps2OurEntityId,
				'assertionConsumerService'		=>	[
					'url'								=>	$myUrl . '/login',
					'binding'							=>	'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
				],
				'singleLogoutService'			=>	[
					'url'								=>	$myUrl . '/logout',
					'binding'							=>	'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
				],
				'NameIDFormat'					=>	self::$nameIdFormats[$nameIdFormat],
				'x509cert'						=>	$conf->ps2OurX509CertBin,
				'privateKey'					=>	$conf->ps2OurPrivateKeyBin
			],
			'idp'		=>	[
				'entityId'						=>	$conf->ps2IdpEntityId,
				'singleSignOnService'			=>	[
					'url'								=>	$conf->ps2IdpSOSUrl,
					'binding'							=>	'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-REDIRECT'
				],
				'singleLogoutService'			=>	[
					'url'								=>	$conf->ps2IdpLOSUrl,
					'responseUrl'						=>	'',
					'binding'							=>	'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
				],
				'x509cert'						=>	$conf->ps2IdpX509CertBin,
			]
		];
		
		$this->rawSettings = $settings;
		
		return $settings;
		
	}
	
	
}