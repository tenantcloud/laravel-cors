<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Config profiles
	|--------------------------------------------------------------------------
	|
	| Here you may specify simple CORS profiles that use the config as their
	| configuration source. 'default' will always be used if no arguments
	| are provided to the middleware and a key from this config will
	| be checked and used (if exists) if given.
	|
	| Example:
	|  - 'cors' => default profile used
	|  - 'cors:default' => default profile used
	|  - 'cors:nesto' => nesto profile used
	|  - 'cors:doesnt_exist' => exception thrown
	|
	*/

	'profiles' => [
		'default' => [
			/*
			 * Value of 'Access-Control-Allow-Credentials' header.
			 */
			'allow_credentials' => false,

			/**
			 * Value of 'Access-Control-Allow-Origin' header.
			 */
			'allow_origins' => [
				// '*',
			],

			/**
			 * Value of 'Access-Control-Allow-Methods' header.
			 */
			'allow_methods' => [
				'POST',
				'GET',
				'OPTIONS',
				'PUT',
				'PATCH',
				'DELETE',
			],

			/**
			 * Value of 'Access-Control-Allow-Headers' header.
			 */
			'allow_headers' => [
				'Content-Type',
				'X-Auth-Token',
				'Origin',
				'Authorization',
			],

			/**
			 * Value of 'Access-Control-Expose-Headers' header.
			 */
			'expose_headers' => [
				'Cache-Control',
				'Content-Language',
				'Content-Type',
				'Expires',
				'Last-Modified',
				'Pragma',
			],

			/*
			 * Value of 'Access-Control-Max-Age' header.
			 */
			'max_age' => 60 * 60 * 24,
		]
	],
];
