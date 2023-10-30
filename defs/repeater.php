<?php namespace ProcessWire;

return [

	'fields'			=>	[

		'ps2RedirRole'			=>	[
			'type'					=>	'page',
			'InputfieldType'		=>	'InputfieldSelect',
			'label'					=>	'Role',
			'template_id'			=>	'role',
			'inputfield'			=>	'InputfieldAsmSelect',
			'labelFieldName'		=>	'title',
			'columnWidth'			=>	'50',
			'tags'					=>	'PoetSaml2'
		],
		
		'ps2RedirUrl'			=>	[
			'type'					=>	'text',
			'label'					=>	'Redirect for Successful Login',
			'description'			=>	'At the end of a successful login, users with the selected role will be redirected here.',
			'notes'					=>	'Put a path relative to your site URL here.',
			'tags'					=>	'PoetSaml2'
		],
		
	],
	
	'after'						=>	'ps2RedirDefault',
	
	'this'						=>	[
		'name'					=>	'ps2RoleRedirects',
		'label'					=>	'Role Basic Redirects',
		'repeaterTitle'			=>	'{ps2RedirRole.title}',
		'repeaterFields'		=>	['ps2RedirRole', 'ps2RedirUrl'],
		'tags'					=>	'PoetSaml2'
	]

];
