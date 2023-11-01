<?php namespace ProcessWire;

return [

	'ps2UDataFs'			=>	[
		'type'					=>	'FieldtypeFieldsetOpen',
		'label'					=>	'User Profile Data Update',
		'collapsed'				=>	Inputfield::collapsedYes,
		'tags'					=>	'poetsaml2'
	],

	'ps2MapUserData'		=>	[
		'type'					=>	'checkbox',
		'label'					=>	'Update User Fields from Claim',
		'description'			=>	'Check to update User fields from Claims provided by IdP',
		'tags'					=>	'poetsaml2'
	],
	
	'ps2DataMapping'		=>	[
		'type'					=>	'listlinks',
		'label'					=>	'User Field Mapping',
		'description'			=>	'Map SAML2 Claims to corresponding User fields for updating',
		'leftLabel'				=>	'SAML2 Claim',
		'rightLabel'			=>	'User Field',
		'buttonText'			=>	'Assign Claim to User Field'
	],
	
	'ps2UDataFs' . FieldtypeFieldsetOpen::fieldsetCloseIdentifier
							=>	[
		'type'					=>	'FieldtypeFieldsetClose',
		'tags'					=>	'poetsaml2'
	]
	
];
	

