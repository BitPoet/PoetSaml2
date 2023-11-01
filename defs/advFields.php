<?php namespace ProcessWire;

/**
 * PoetSaml2 profile fields for advanced configuration
 */

return [

	'ps2Advanced'		=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Advanced Configuration',
		'description'			=>	'Check to use advanced configuration settings to control the behavior of your SP. This includes fine grained signing requirements, used algorithms and other, security relevant settings.',
		'tags'					=>	'poetsaml2adv',
	],

	'ps2AdvFs'			=>	[
		'type'					=>	'FieldtypeFieldsetOpen',
		'label'					=>	'Advanced Settings',
		'description'			=>	'Beyond Here Lies Danger!',
		'showIf'				=>	'ps2Advanced=1',
		'tags'					=>	'poetsaml2adv',
	],

	'nameIdEncrypted'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Encrypt NameID',
		'description'			=>	'Indicates that the nameId of the samlp:logoutRequest sent by this SP will be encrypted.',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'authnRequestsSigned'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Sign AuthN Requests',
		'description'			=>	'Indicates whether the samlp:AuthnRequest messages sent by this SP will be signed.  [Metadata of the SP will offer this info]',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'logoutRequestSigned'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Sign Logout Requests',
		'description'			=>	'Indicates whether the samlp:logoutRequest messages sent by this SP will be signed.',
		'defaultValue'			=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'logoutResponseSigned'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Sign Logout Responses',
		'description'			=>	'Indicates whether the samlp:logoutResponse messages sent by this SP will be signed.',
		'defaultValue'			=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'signMetadata'			=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Sign Metadata',
		'description'			=>	'Sign Metadata with SP certificate',
		'value'					=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'wantMessagesSigned'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Want Messages Signed',
		'description'			=>	'Indicates a requirement for the samlp:Response, samlp:LogoutRequest and samlp:LogoutResponse elements received by this SP to be signed.',
		'defaultValue'			=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'wantAssertionsEncrypted'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Want Assertions Encrypted',
		'description'			=>	'Indicates a requirement for the saml:Assertion elements received by this SP to be encrypted.',
		'defaultValue'			=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'wantAssertionsSigned'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Want Assertions Signed',
		'description'			=>	'Indicates a requirement for the saml:Assertion elements received by this SP to be signed. [Metadata of the SP will offer this info]',
		'defaultValue'			=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'wantNameId'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Require NameID',
		'description'			=>	'Indicates a requirement for the NameID element on the SAMLResponse received by this SP to be present.',
		'defaultValue'			=>	1,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'wantNameIdEncrypted'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Require Encrypted NameID',
		'description'			=>	'Indicates a requirement for the NameID received by this SP to be encrypted.',
		'defaultValue'			=>	0,
		'columnWidth'			=>	50,
		'tags'					=>	'poetsaml2adv',
	],

	'requestedAuthnContext'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Request AuthContext',
		'description'			=>	'Authentication context. Uncheck and no AuthContext will be sent in the AuthNRequest. Check and you will get an AuthContext "exact" "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport".',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'wantXMLValidation'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Validate XML',
		'description'			=>	'Indicates if the SP will validate all received xmls. (In order to validate the xml, strict mode must be enabled too. This is the default with php-saml 4.).',
		'defaultValue'			=>	1,
		'tags'					=>	'poetsaml2adv',
	],

	'relaxDestinationValidation'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Relax Destination Validation',
		'description'			=>	'If true, SAMLResponses with an empty value at its Destination attribute will not be rejected for this fact.',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'allowRepeatAttributeName'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Allow Repeated Attribute Names',
		'description'			=>	'If true, the toolkit will not raised an error when the Statement Element contain atribute elements with name duplicated',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'destinationStrictlyMatches'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Strict Destination Match',
		'description'			=>	'If true, Destination URL should strictly match to the address to which the response has been sent. Notice that if "relaxDestinationValidation" is true an empty Destintation will be accepted.',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'rejectUnsolicitedResponsesWithInResponseTo'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Reject Unsolicited Responses',
		'description'			=>	'If true, SAMLResponses with an InResponseTo value will be rejectd if not AuthNRequest ID provided to the validation method.',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],

	'signatureAlgorithm'	=>	[
		'type'					=>	'options',
		'inputfieldClass'		=>	'InputfieldSelect',
		'label'					=>	'Signature Algorithm',
		'description'			=>	'Algorithm that the toolkit will use on signing process. Notice that sha1 is a deprecated algorithm and should not be used.',
		'initValue'				=>	'3',
		'required'				=>	true,
		'requiredIf'			=>	'ps2Advanced=1',
		'columnWidth'			=>	50,
		'export_options'		=>	[
			'default'				=>	"1=http://www.w3.org/2000/09/xmldsig#rsa-sha1|rsa-sha1\n2=http://www.w3.org/2000/09/xmldsig#dsa-sha1|dsa-sha1\n3=http://www.w3.org/2001/04/xmldsig-more#rsa-sha256|rsa-sha256\n4=http://www.w3.org/2001/04/xmldsig-more#rsa-sha384|rsa-sha384\n5=http://www.w3.org/2001/04/xmldsig-more#rsa-sha512|rsa-sha512",
		],
		'tags'					=>	'poetsaml2adv',
	],

	'digestAlgorithm'	=>	[
		'type'					=>	'options',
		'inputfieldClass'		=>	'InputfieldSelect',
		'label'					=>	'Digest Algorithm',
		'description'			=>	'Algorithm that the toolkit will use on digest process. Notice that sha1 is a deprecated algorithm and should not be used.',
		'initValue'				=>	'2',
		'required'				=>	true,
		'requiredIf'			=>	'ps2Advanced=1',
		'columnWidth'			=>	50,
		'export_options'		=>	[
			'default'				=>	"1=http://www.w3.org/2000/09/xmldsig#sha1|sha1\n2=http://www.w3.org/2001/04/xmlenc#sha256|sha256\n3=http://www.w3.org/2001/04/xmldsig-more#sha384|sha384\n4=http://www.w3.org/2001/04/xmlenc#sha512|sha512",
		],
		'tags'					=>	'poetsaml2adv',
	],

	'encryptionAlgorithm'	=>	[
		'type'					=>	'options',
		'inputfieldClass'		=>	'InputfieldSelect',
		'label'					=>	'Encryption Algorithm',
		'description'			=>	'Algorithm that the toolkit will use for encryption process. Notice that aes-cbc are not consider secure anymore so should not be used.',
		'initValue'				=>	'5',
		'required'				=>	true,
		'requiredIf'			=>	'ps2Advanced=1',
		'export_options'		=>	[
			'default'				=>	"1=http://www.w3.org/2001/04/xmlenc#tripledes-cbc|tripledes-cbc\n2=http://www.w3.org/2001/04/xmlenc#aes128-cbc|aes128-cbc\n3=http://www.w3.org/2001/04/xmlenc#aes192-cbc|aes192-cbc\n4=http://www.w3.org/2001/04/xmlenc#aes256-cbc|aes256-cbc\n5=http://www.w3.org/2009/xmlenc11#aes128-gcm|aes128-gcm\n6=http://www.w3.org/2009/xmlenc11#aes192-gcm|aes192-gcm\n7=http://www.w3.org/2009/xmlenc11#aes256-gcm|aes256-gcm",
		],
		'tags'					=>	'poetsaml2adv',
	],

	'lowercaseUrlencoding'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'ADFS: Expect Lowercase URL Encoding',
		'description'			=>	'ADFS URL-Encodes SAML data as lowercase, and the toolkit by default uses uppercase. Turn it True for ADFS compatibility on signature verification',
		'defaultValue'			=>	0,
		'tags'					=>	'poetsaml2adv',
	],
	
	'ps2AdvFs' . FieldtypeFieldsetOpen::fieldsetCloseIdentifier
							=>	[
		'type'					=>	'FieldtypeFieldsetClose',
		'tags'					=>	'poetsaml2adv',
	]

];
