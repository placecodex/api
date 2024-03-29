<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Auth extends REST_Controller
{

	function test_get(){
		$id = $this->session->userdata('id');
		var_dump($id);
		echo $id == false;
		// echo 'Total Results: ' . $query->num_rows();
	}

	function get_get(){
		echo 123;
	}

	function signup_post(){
		$username = $this->post('username');
		$password = $this->post('password');
		$password = md5($password);

		$query = $this->db->query("SELECT username, password FROM users WHERE username='{$username}'");
		if($query->num_rows > 0){
			$this->response(array('error' => 'duplicate'), 200);
		}else{
			$data = array(
				'username' => $username,
				'password' => $password,
			);

			$this->db->insert('user', $data); 
			$query = $this->db->query("SELECT id FROM users WHERE username='{$username}'");
			
			//set session after user signup successfully
			if($query->num_rows == 1){
				$result = $query->result();
				$id = $result[0]->id;
				$this->session->set_userdata('id', $id);
				$this->response(array('status' => 'success', 'id' => $id), 200);
			}

		}

		// echo 'Total Results: ' . $query->num_rows();
	}

	function logout_get(){
		$this->session->sess_destroy();
		return $this->response(array('status' => 'success'), 200);
		// echo 'Total Results: ' . $query->num_rows();
	}

	function login_get(){
		$id = $this->session->userdata('id');
		return $id !== FALSE? $this->response(array('status' => 'true'), 200): $this->response(array('status' => 'false'), 200);
		// echo 'Total Results: ' . $query->num_rows();
	}

    /**
     * { function_description }
     */
	function loginn_get() {
		
		$username = $this->post('username');
		$password = $this->post('password');
		$password = md5($password);

		$query = $this->db->query("SELECT id, username, password FROM user WHERE username='{$username}' and password='{$password}'");

		if($query->num_rows > 0){
			$result = $query->result();
			$id = $result[0]->id;
			$username = $result[0]->username;
			
 			$this->session->set_userdata('id', $id);
			$this->session->set_userdata('username', $username);

			$this->response(array('status' => 'successs'), 200);
		}else{
			$this->response(array('status' => 'fail'), 200);
		}
		// echo 'Total Results: ' . $query->num_rows();
	}

	
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}