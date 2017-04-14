<?php

// namespace App\Helper\Auth;
// use Slim\App;
// use \Firebase\JWT\JWT;

// /**
//  * This class is used to authenticate a token using JWT
//  */
// class AuthHelper {

// 	private $c;

// 	/**
// 	 * Authenticate token with JWT secret key
// 	 * @return decoded string
// 	 */
// 	public static function authenticateToken($token) {

// 		$this->c = App\Helper\Container\ContainerHelper::getContainer();
// 		$settings = $c->get('settings');

// 		$secretKey = $settings["jwtSecretKey"];

// 		$jwt = JWT::decode($token, $secretKey, 'HS256');

// 		return json_encode($jwt);
// 	}
}