<?php namespace ProcessWire;

return [

	'fields'			=>	[

		'ps2RedirRole'			=>	[
			'type'					=>	'page',
			'InputfieldType'		=>	'InputfieldSelect',
			'label'					=>	'Role',
			'template_id'			=>	'Role',
			'columnWidth'			=>	'50',
			'tags'					=>	'PoetSaml2'
		],
		
		'ps2RedirUrl'			=>	[
			'type'					=>	'text',
			'label'					=>	'Redirect for Successful Login',
			'description'			=>	'At the end of a successful login, users with the selected role will be redirected here.',
			'notes'					=>	'Put a path relative to your site URL here.'
			'tags'					=>	'PoetSaml2'
		],
		
		'ps2RoleRedirects'		=>	[
			'type'					=>	'repeater',
			'label'					=>	'Role Basic Redirects',
			'repeaterTitle'			=>	'{ps2RedirRole.title}',
			'repeaterFields'		=>	['ps2RedirRole', 'ps2RedirUrl'],
			'fieldContexts'			=>	['ps2RedirRole', 'ps2RedirUrl'],
			'tags'					=>	'PoetSaml2'
		]

	],
	
	'after'						=>	['ps2RoleRedirects', 'ps2RedirDefault']

];