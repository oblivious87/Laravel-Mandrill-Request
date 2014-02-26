<?php namespace oblivious87\LaravelMandrillRequestLocal;

/**
 * An L4 package wrapper of Michael Teeuw's Laravel 3 mandrill API wrapper written by Doxxon 
 * I added Local environment support for the SSL connection.
 *
 * @link 		https://github.com/oblivious87/laravel-mandrill
 * @author     	oblivious87 <jstark@gmail.com>
 * @license    	MIT License
 */

class MandrillRequest
{
	public static function request($method, $arguments = array())
	{

		// load api key
		$api_key = \Config::get('laravel-mandrill-request::api_key');
		
		// determine endpoint
		$endpoint = 'https://mandrillapp.com/api/1.0/'.$method.'.json';
		
		// build payload
		$arguments['key'] = $api_key;

		// setup curl request
		$ch = curl_init();
		
		if (\App::environment('local'))
		{
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // this has to be at the end of the array?
		}
		else
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // this has to be at the end of the array?
		}

		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arguments));
		
		$response = curl_exec($ch);

		// catch errors
		if (curl_errno($ch))
		{
			//$errors = curl_error($ch);
			curl_close($ch);
			
			// return false
			return false;
		}
		else
		{
			curl_close($ch);
			
			// return array
			return json_decode($response);
		}

	}
}