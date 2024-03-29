<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH."libraries/JWT.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class UserController extends CI_Controller {
	private $secret = 'this is key secret';

	public function __construct() {
		parent::__construct();
		$this->load->model('user');
	}

	public function get_all() {
		if($this->user->get()) {
			return $this->response($this->user->get());
		}
	}

	public function get($id) {
		if($this->user->get('id', $id)) {
			return $this->response($this->user->get('id', $id));
		}
		return null;
	}

	public function response($data){
		$this->output
					->set_content_type('application/json')
					->set_status_header(200)
					->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
					->_display();
					exit;
	}

	public function register() {
		return $this->response($this->user->save());
	}

	public function login() {
		$date = new DateTime();

		if (!$this->user->is_valid()) {
			return $this->response([
				'success' => false,
				'message' => 'email atau password salah'
			]);
		}

		$user = $this->user->get('email', $this->input->post('email'));

		$payload['id'] = $user->id;
		// $payload['email'] = $user->email;
		$payload['iat'] = $date->getTimestamp();
		$payload['exp'] = $date->getTimestamp() + 60*60*2;

		$output['id_token'] = JWT::encode($payload, $this->secret);
		$this->response($output);
	}

	public function check_token() {
		$jwt = $this->input->get_request_header('Authorization');

		try {
			$decode = JWT::decode($jwt, $this->secret, array('HS256'));
			// var_dump($decode);
			return $decode->id;
		} catch (\Exception $e) {
			return $this->response([
				'success' => false,
				'message' => 'gagal, error token'
			]);
		}

	}

	public function delete($id) {
		if($id_from_token = $this->check_token()) {
			if ($id_from_token==$id) {
				return $this->response($this->user->delete($id));
			} else {
				return $this->response([
					'success' => false,
					'message' => 'user yang login berbeda'
				]);
			}

		}
	}

}
