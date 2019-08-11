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
require APPPATH . 'libraries/Format.php';
require APPPATH .'libraries/phpass-0.5/PasswordHash.php';


#require_once('phpass-0.5/PasswordHash.php');

class Auth extends REST_Controller
{
	    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Load the user model
        $this->load->model('user');
         $this->load->model('users');

        // trun off csrf protection
         $config['csrf_protection'] = FALSE;
         $this->load->config('tank_auth', TRUE);

        $this->userTbl = 'tbl_users';

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

    }

	

	function test_get(){
		$id = $this->session->userdata('id');
		var_dump($id);
		echo $id == false;
		// echo 'Total Results: ' . $query->num_rows();
	}

	
	function signup_post(){



     
        $number = $this->post('number');
        $email = $this->post('email');
        $name = $this->post('name');
        $last_name = $this->post('last_name');
        $username = $this->post('username');
		$password = $this->post('password');
		$repassword = $this->post('repassword');


// check input 
#if (!isset($username) || !isset($password)|| !isset($name)|| !isset($last_name)) {
	#		header("HTTP/1.1 200 OK");
		#	echo json_encode(array('status' => 'invalid parameters some input empty'));
		#	return;
		#}

// check password match
       if ($password == $repassword) { }else{

			$this->response(array('error' => 'password not match'), 400);
      # return	ejson_encode(array('status' => 'password not match'));

		}


		#$username = $_POST['username'];
		#$password = $_POST['password'];
		if (empty($username) || empty($password) ||  empty($repassword) || empty($name)  || empty($number) || empty($last_name)  ||  strlen($username) > 20 || strlen($password) > 20 || 
			!preg_match('/^[a-zA-Z0-9_]*$/', $username)) {
			header("HTTP/1.1 200 OK");
			echo json_encode(array('status' => 'Por favor complete todos los campos'));
			return;
		}
       


       	// Hash password using phpass
			$hasher = new PasswordHash(
					$this->config->item('phpass_hash_strength', 'tank_auth'),
					$this->config->item('phpass_hash_portable', 'tank_auth'));
			$password = $hasher->HashPassword($password);

		#$password = md5($password);
        


         //check user duplicate number
		$query = $this->db->query("SELECT username, email, 'phone_number' FROM $this->userTbl WHERE 'phone_number'='{$number}' ||  email='{$email}' ");
		if($query->num_rows() >= 1){
			$this->response(array('error' => 'duplicate number or email'), 200);



        //check user duplicate username
		#$query = $this->db->query("SELECT username, email FROM users_rest WHERE username='{$username}' ||  email='{$email}' ");
	#	if($query->num_rows() >= 1){
		#	$this->response(array('error' => 'duplicate username or email'), 200);
		}else{

			$data = array(
                 
                'name' => $name,
                'last_name' => $last_name,
                'email' => $email,
                'activation_code' => 1236,
                'phone_number' => $number,
				'username' => $username,
				'password' => $password
			);

			$this->db->insert($this->userTbl, $data); 
			$query = $this->db->query("SELECT id FROM $this->userTbl WHERE username='{$username}'");
			
			//set session after user signup successfully
			if($query->num_rows() == 1){
				$result = $query->result();
				$id = $result[0]->id;
				$this->session->set_userdata('id', $id);
				$this->response(array('status' => 'success', 'id' => $id), 200);
			}

          //Mail activation
         // Send email with data transaction to user receiver
         # $this->_send_email($type, $email, $tda);


		}

		// echo 'Total Results: ' . $query->num_rows();
	}

	function logout_get(){
		$this->session->sess_destroy();
		return $this->response(array('status' => 'Logout success'), 200);
		// echo 'Total Results: ' . $query->num_rows();
	}
    

    /**
     * Login session test
     *
     * @return     boolean  ( description_of_the_return_value )
     */
	function login_state_get(){
		$id = $this->session->userdata('id');

         if (!empty($id)) {

         return 	$this->response(array('status' => 'Login true'), 200);
         }else {

            return $this->response(array('status' => 'Login false'), 200);
         }
	}


	public function login_post() {
        // Get the post data
        $email = $this->post('email');
        $password = $this->post('password');

 

                    $hasher = new PasswordHash(

					$this->config->item('phpass_hash_strength', 'tank_auth'),
					$this->config->item('phpass_hash_portable', 'tank_auth'));

			        $password = $hasher->HashPassword($password);


        
        // Validate the post data
        if(!empty($email) && !empty($password)){
            
            // Check if any user exists with the given credentials
            $con['returnType'] = 'single';
            $con['conditions'] = array(
                'email' => $email,
                'password' => $password,
                'status' => 1
            );
            $user = $this->user->getRows($con);
            
            if($user){
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'User login successful.',
                    'data' => $user
                ], REST_Controller::HTTP_OK);
            }else{
                // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                $this->response("Wrong email or password.", REST_Controller::HTTP_BAD_REQUEST);
            }
        }else{
            // Set the response and exit
            $this->response("Provide email and password.", REST_Controller::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Login function
     */
	function loginn_post() {

      #    $username = $_POST['username'];
		#$password = $_POST['password'];

        $login = xss_clean($this->post('email'));
		$password = xss_clean($this->post('password'));




	#	if ((strlen($login) > 0) AND (strlen($password) > 0)) {


		/*if (empty($email) || empty($password) || strlen($email) > 20 || strlen($password) > 20 || 
			!preg_match('/^[a-zA-Z0-9_]*$/', $email)) {
			#header("HTTP/1.1 200 OK");
			$this->response(array('empty parameters' => 'Fail login'), 400);
			return;
		}*/

		#$password = md5($password);     

           // Hash password using phpass
			/*$hasher = new PasswordHash(
					$this->config->item('phpass_hash_strength', 'tank_auth'),
					$this->config->item('phpass_hash_portable', 'tank_auth'));

			$password = $hasher->HashPassword($password);*/


			if ((strlen($login) > 0) AND (strlen($password) > 0)) {

			// Which function to use to login (based on config)
			if ($login_by_username AND $login_by_email) {
				$get_user_func = 'get_user_by_login';
			} else if ($login_by_username) {
				$get_user_func = 'get_user_by_username';
			} else {
				$get_user_func = 'get_user_by_email';
			}

			if (!is_null($user = $this->users->$get_user_func($login))) {	
			// login ok
         
				// Does password match hash in database?
				$hasher = new PasswordHash(
						$this->config->item('phpass_hash_strength', 'tank_auth'),
						$this->config->item('phpass_hash_portable', 'tank_auth'));
				if ($hasher->CheckPassword($password, $user->password)) {		
				// password ok

					$this->session->set_userdata(array(
								'user_id'	=> $user->id,
								'username'	=> $user->username,
								'status'	=> ($user->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED, ));    

				        	$this->response(array('status' => 'Login successs'), 200);
            
            }else{
           // login fail

           $this->response(array('status' => 'data incorrect'), 200);

            }

         


		$query = $this->db->query("SELECT id, email, password FROM $this->userTbl WHERE email='{$email}' and password='{$password}'");

		if($query->num_rows() == 1){

			$result = $query->result();
			$id = $result[0]->id;
			$email = $result[0]->email;
			
 			$this->session->set_userdata('id', $id);
			$this->session->set_userdata('email', $email);

			$this->response(array('status' => 'Login successs'), 200);

   // Send email with data transaction to user receiver
   #   $this->_send_email($type, $email, $tda);


		}else{
			$this->response(array('status' => 'data incorrect'), 200);
		}
		// echo 'Total Results: ' . $query->num_rows();

  }

	}
}






  /**
   * Send email message of given type (activate, forgot_password, etc.)
   *
   * @param string
   * @param string
   * @param array
   * @return  void
   */
  function _send_email($type, $email, &$data)
  {



    $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
    $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
    $this->email->to( $email );
    $this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
    $this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
    $this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
    $this->email->send();
  }/*end function*/








	
}