<?php namespace ProcessWire;

return [

	'ps2Active'				=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Active',
		'description'			=>	'Check this box to active this profile. Make sure you have filled out all necessary fields before you do that.',
		'tags'					=>	'poetsaml2'
	],

	'ps2BackendButton'		=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Add Backend Login Button',
		'description'			=>	'Adds a "Log in with ..." button to the ProcessWire Backend login form',
		'tags'					=>	'poetsaml2'
	],

	'ps2OurFs'				=>	[
		'type'					=>	'FieldtypeFieldsetOpen',
		'label'					=>	'SP Configuration',
		'tags'					=>	'poetsaml2'
	],

	'ps2OurEntityId'		=>	[
		'type'					=>	'text',
		'label'					=>	'Our Entity Id (Usually URI notation!)',
		'notes'					=>	'This is a fictional URL (sometimes just a name) used to identify your SP in the IdP. This is usually https://your-domain-name/sp.',
		'tags'					=>	'poetsaml2'
	],

	'ps2NameIdFormat'		=>	[
		'type'					=>	'options',
		'inputfieldClass'		=>	'InputfieldSelect',
		'label'					=>	'Our NameIdFormat',
		'export_options'		=>	[
			'default'				=>	"1=NAMEID_EMAIL_ADDRESS|NAMEID_EMAIL_ADDRESS\n2=NAMEID_X509_SUBJECT_NAME|NAMEID_X509_SUBJECT_NAME\n3=NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME|NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME\n4=NAMEID_UNSPECIFIED|NAMEID_UNSPECIFIED\n5=NAMEID_KERBEROS|NAMEID_KERBEROS\n6=NAMEID_ENTITY|NAMEID_ENTITY\n7=NAMEID_TRANSIENT|NAMEID_TRANSIENT\n8=NAMEID_PERSISTENT|NAMEID_PERSISTENT\n9=NAMEID_ENCRYPTED|NAMEID_ENCRYPTED"
		],
		'initValue'				=>	'1',
		'required'				=>	'1',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2CreateSelfSignedCert'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Create Self-Signed Certificate and Private Key',
		'description'			=>	'WARNING: This will overwrite any certificate and private key already entered! You will have to re-upload / enter your metadata at your identity provider!',
		'notes'					=>	'Creates a self-signed certificate valid for 395 days, using a 4096bit RSA key with SHA256 digest',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2OurContry'			=>	[
		'type'					=>	'text',
		'label'					=>	'2 Letter Country Code',
		'description'			=>	'Necessary for certificate request creation',
		'showIf'				=>	'ps2CreateSelfSignedCert=1',
		'required'				=>	true,
		'requiredIf'			=>	'ps2CreateSelfSignedCert=1',
		'pattern'				=>	'[A-Z]{2}',
		'size'					=>	'2',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2OurX509Cert'		=>	[
		'type'					=>	'textarea',
		'label'					=>	'Our X509 Certificate',
		'collapsed'				=>	Inputfield::collapsedBlank,
		'tags'					=>	'poetsaml2',
		'note'					=>	'Paste the certificate in PEM format, not binary! Certficiate can be pasted with or without header and footer ("-----BEGIN CERTIFICATE-----" / "-----END CERTIFICATE-----")'
	],
	
	'ps2OurX509CertBin'		=>	[
		'type'					=>	'text',
		'label'					=>	'Our X509 Certificate (Binary)',
		'tags'					=>	'poetsaml2',
		'note'					=>	'Filled from ps2OurX509cert on page save',
		'collapsed'				=>	Inputfield::collapsedHidden
	],
	
	'ps2OurPrivateKey'		=>	[
		'type'					=>	'textarea',
		'label'					=>	'Private Key for our Certificate (PEM). With or without header and footer ("-----BEGIN PRIVATE KEY-----" / "-----END PRIVATE KEY-----"',
		'collapsed'				=>	Inputfield::collapsedBlank,
		'tags'					=>	'poetsaml2'
	],
	
	'ps2OurPrivateKeyBin'		=>	[
		'type'					=>	'text',
		'label'					=>	'Private Key for our Certificate (Binary)',
		'tags'					=>	'poetsaml2',
		'note'					=>	'Filled from ps2OurPrivateKey on page save',
		'collapsed'				=>	Inputfield::collapsedHidden
	],
	
	'ps2OurFs' . FieldtypeFieldsetOpen::fieldsetCloseIdentifier
							=>	[
		'type'					=>	'FieldtypeFieldsetClose',
		'tags'					=>	'poetsaml2'
	],

	'ps2IdpFs'				=>	[
		'type'					=>	'FieldtypeFieldsetOpen',
		'label'					=>	'IdP Configuration',
		'collapsed'				=>	Inputfield::collapsedYes,
		'tags'					=>	'poetsaml2'
	],

	'ps2IdpImportXmlFile'	=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Import Metadata from File',
		//'collapsed'				=>	Inputfield::collapsedYes,
		'columnWidth'			=>	50,
		'showIf'				=>	'ps2IdpImportUrl!=1',
		'tags'					=>	'poetsaml2'
	],

	'ps2IdpImportUrl'		=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Import Metadata from URL',
		//'collapsed'				=>	Inputfield::collapsedYes,
		'columnWidth'			=>	50,
		'showIf'				=>	'ps2IdpImportXmlFile!=1',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2IdpXmlFile'			=>	[
		'type'					=>	'file',
		'maxFiles'				=>	1,
		'extensions'			=>	'xml',
		'descriptionRows'		=>	'0',
		'showIf'				=>	'ps2IdpImportXmlFile=1',
		'tags'					=>	'poetsaml2'
	],

	'ps2IdPUrl'				=>	[
		'type'					=>	'url',
		'label'					=>	'Metadata URL',
		'showIf'				=>	'ps2IdpImportUrl=1',
		'tags'					=>	'poetsaml2'
	],

	'ps2IdpEntityId'		=>	[
		'type'					=>	'text',
		'tags'					=>	'poetsaml2',
		'required'				=>	true,
		'requiredIf'			=>	'ps2Active=1',
		'label'					=>	'IdP Entity Id (Usually URI notation!)'
	],
	
	'ps2IdpSOSUrl'			=>	[
		'type'					=>	'text',
		'tags'					=>	'poetsaml2',
		'required'				=>	true,
		'requiredIf'			=>	'ps2Active=1',
		'label'					=>	'IdP Single Sign-On Service URL'
	],
	
	'ps2IdpSLSUrl'			=>	[
		'type'					=>	'text',
		'tags'					=>	'poetsaml2',
		'label'					=>	'IdP Single Logout Service URL'
	],
	
	'ps2IdpX509Cert'		=>	[
		'type'					=>	'textarea',
		'label'					=>	'IdP X509 Certificate',
		'collapsed'				=>	Inputfield::collapsedBlank,
		'required'				=>	true,
		'requiredIf'			=>	'ps2Active=1',
		'tags'					=>	'poetsaml2',
		'note'					=>	'Paste the certificate in PEM format, not binary!  Certficiate can be pasted with or without header and footer ("-----BEGIN CERTIFICATE-----" / "-----END CERTIFICATE-----")'
	],

	'ps2IdpX509CertBin'		=>	[
		'type'					=>	'text',
		'label'					=>	'IdP X509 Certificate (Binary)',
		'tags'					=>	'poetsaml2',
		'note'					=>	'Filled from ps2IdpX509Cert on page save',
		'collapsed'				=>	Inputfield::collapsedHidden
	],

	'ps2IdpFs' . FieldtypeFieldsetOpen::fieldsetCloseIdentifier
							=>	[
		'type'					=>	'FieldtypeFieldsetClose',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2RedirFs'			=>	[
		'type'					=>	'FieldtypeFieldsetOpen',
		'label'					=>	'Redirect Target for IdP Initiated Login',
		'collapsed'				=>	Inputfield::collapsedYes,
		'tags'					=>	'poetsaml2'
	],

	'ps2RedirDefault'		=>	[
		'type'					=>	'text',
		'label'					=>	'Default Redirect for Successful Login',
		'description'			=>	'At the end of a successful login, users will be redirected here, unless a role-based redirect has been configured.',
		'notes'					=>	'Put a path relative to your site URL here.',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2RedirFs' . FieldtypeFieldsetOpen::fieldsetCloseIdentifier
							=>	[
		'type'					=>	'FieldtypeFieldsetClose',
		'tags'					=>	'poetsaml2'
	],


];
