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

use Firebase\JWT\JWT;


class Auth extends REST_Controller
{
	    function __construct()
    {

            
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        } 
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { 
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
            exit(0);
        }





        // Construct the parent class
        parent::__construct();

        $this->load->config('tank_auth', TRUE);

        $this->load->config('jwt', TRUE);

        $this->load->helper('cookie');

        /*Table Name*/
        $this->userTbl = 'tbl_users';

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

    }

    
    /**
     * [save_cookie_post description]
     * @return [type] [description]
     */
    function save_cookie_post()
   {

         $cookie = array(
                        'name'   => 'token_2',
                        'value'  => 123456789,                            
                        'expire' => '300',                                                                        
                        'secure' => TRUE
                        );

               set_cookie($cookie);

               

      # echo "Congragulatio Cookie Set";

       //output the contents of the cookie array variable
            # print_r($_COOKIE); 


             $cookie_name = "token";
             $cookie_value = 123456;

             // 86400 = 1 day
            setcookie($cookie_name, $cookie_value,
            time() + (86400 * 30), "/"); 



   }
    /**
     * [show_cookie_post description]
     * @return [type] [description]
     */
   function show_cookie_post()
   {


$cookie_name = $this->config->item('token_name');

if(!isset($_COOKIE[$cookie_name])) {
    echo "Cookie named '" . $cookie_name . "' is not set!";
} else {
    echo "Cookie '" . $cookie_name . "' is set!<br>";
    echo "Value is: " . $_COOKIE[$cookie_name];
}



  #$cookie= get_cookie('token');  

          #echo $cookie;

   #  var_dump($_COOKIE);

   }



   
    /**
     * [verify_request description]
     * @return [type] [description]
     */
    private function IsAuth()
{



$key = $this->config->item('jwt_key');
$cookie_name = $this->config->item('token_name');
$token = $this->session->userdata($cookie_name);

    // Get all the headers
    $headers = $this->input->request_headers();
    // Extract the token
    $token = $headers['Authorization'];


    // Use try-catch
    // JWT library throws exception if the token is not valid
    try {
        // Validate the token
        // Successfull validation will return the decoded user data else returns false
        
        $data = JWT::decode($token, $key, array('HS256'));


        if ($data === false) {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            $this->response($response, $status);
            exit();
        } else {
            return $data;
        }
    } catch (Exception $e) {
        // Token is invalid
        // Send the unathorized access message
        $status = parent::HTTP_UNAUTHORIZED;
        $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
        $this->response($response, $status);
    }
}



public function dashboard_post()
{

$auth = $this->IsAuth();


$key = $this->config->item('jwt_key');
$cookie_name = $this->config->item('token_name');
$token_code = $this->session->userdata($cookie_name);



$decoded = JWT::decode($token_code, $key, array('HS256'));
$decoded = (array) $decoded;



 #$name = $decoded['data']->name;
 #$last_name = $decoded['data']->last_name;


var_dump($decoded);

   #$this->response(array('status' => 'Register success', 'id' =>  $name), 200);


 // data user 
$user = $this->Tbl_users_profile->getUser($user_id);

$data = array(
                 
           'name'     =>        ucwords($user->name),
           'last_name'      =>  ucwords($user->last_name),
           'phone_number'   =>  $user->phone_number,
           'email'          =>  $user->email,
           'phone_verified' =>  $user->phone_verified,
            );



}

	

    public function token_test_post()
{

$key = $this->config->item('jwt_key');
$cookie_name = $this->config->item('token_name');
$token_code = $this->session->userdata($cookie_name);


$data = JWT::decode($token_code, $key, array('HS256'));



var_dump($data);
#var_dump($token_code);


/*
    // Call the verification method and store the return value in the variable
    $data = $this->verify_request();

    // Send the return data as reponse
    $status = parent::HTTP_OK;
    $response = ['status' => $status, 'data' => $data];
    $this->response($response, $status);
}
    */

	}
   
	function signup_post(){

        //retrieve data from ionic input form
     //   $_POST = json_decode(file_get_contents('php://input'), true);

      $config = [
    [       // Email
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'is_unique[tbl_users.email]|trim|required|xss_clean|valid_email|min_length[6]',

            'errors' => [
                    'required' => 'We need a Email',
                    'min_length' => 'Minimum %s length is 6 characters',
                    'is_unique'     => 'This %s already exists.'
                  #  'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
            ],
    ],

      [      // Identification Number
            'field' => 'identification_number',
            'label' => 'Identification number',
            'rules' => 'is_unique[tbl_users.identification_number]|trim|required|xss_clean|min_length[6]',

            'errors' => [
                    'required' => 'Identification number is required',
                    'min_length' => 'Minimum Identification number length is 6 characters',
                    'is_unique'     => 'This %s already exists in our data base.'
                  #  'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
            ],
    ],


       [    // Username
            'field' => '',
            'label' => 'Username',
            'rules' => 'required|min_length[3]|alpha_dash',
            'errors' => [
                    'required' => 'We need both username and password',
                    'min_length' => 'Minimum Username length is 3 characters',
                    'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
            ],
    ],

    [        // Name
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|min_length[3]',
            'errors' => [
                    'required' => 'You must provide a %s.',
                    'min_length' => 'Minimum %s length is 3 characters',
            ],
    ],

    [        // Last Name
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'required|min_length[3]',
            'errors' => [
                    'required' => 'You must provide a %s.',
                    'min_length' => 'Minimum %s length is 3 characters',
            ],
    ],
    
    [       // Phone Number
            'field' => 'phone_number',
            'label' => 'Phone Number',
            'rules' => 'required|min_length[10]',
            'errors' => [
                    'required' => 'You must provide a Phone Number.',
                    'min_length' => 'Minimum Nombre Phone length is 10 characters',
            ],
    ],

    [       // Password
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash',
            'errors' => [
                    'required' => 'You must provide a Password.',
                    'min_length' => 'Minimum Password length is 6 characters',
            ],
    ],
    [

            // Confirmation Password
            'field' => 'confirm_password',
            'label' => 'Confirm_Password',
            'rules' => 'trim|matches[password]xss_clean|required|min_length[6]',
            'errors' => [
                    'required' => 'Please, confirm the password.',
                    'matches' => 'La clave de confirmacion debe ser igual la clave.',
                    'min_length' => 'Minimum Password length is 6 characters',
            ],
    ],
];

$data = $this->input->post();

$this->form_validation->set_data($data);
$this->form_validation->set_rules($config);

if($this->form_validation->run()==FALSE){
   # print_r($this->form_validation->error_array());

	$errors =  validation_errors();

     $this->response(array('status' => 'false', 'message'=> $errors), REST_Controller::HTTP_BAD_REQUEST);


		}else{
   
        $number = $this->post('phone_number');
        $email = $this->post('email');
        $name = $this->post('name');
        $last_name = $this->post('last_name');
        $username = $this->post('username');
        $password = $this->post('password');
       # $repassword = $this->post('repassword');


        // Hash password using phpass
            $hasher = new PasswordHash(

            $this->config->item('phpass_hash_strength', 'tank_auth'),
            $this->config->item('phpass_hash_portable', 'tank_auth'));

            $password = $hasher->HashPassword($password);

         //check user duplicate number


			$data = array(
                 
                'name' => $name,
                'last_name' => $last_name,
                'email' => $email,
                'activation_code' => 1236,
                'phone_number' => $number,
				'username' => $username,
				'password' => $password
			);

            //insert data after validation input

			$this->db->insert($this->userTbl, $data); 
			$query = $this->db->query("SELECT id FROM $this->userTbl WHERE username='{$username}'");
			
			//set session after user signup successfully
			if($query->num_rows() == 1){
				$result = $query->result();
				$id = $result[0]->id;
				$this->session->set_userdata('id', $id);
                // Register success message
				$this->response(array('status' => 'Register success', 'id' => $id), 200);
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


    function TokenCheck_get(){

       
    // get jwt key
    $key = $this->config->item('jwt_key');
    // Get all the headers
    $headers = $this->input->request_headers();
    // Extract the token
    $token = $headers['Authorization'];


    // Use try-catch
    // JWT library throws exception if the token is not valid
    try {
        // Validate the token
        // Successfull validation will return the decoded user data else returns false
        
        $data = JWT::decode($token, $key, array('HS256'));


        if ($data === false) {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => false, 'message' => 'Unauthorized Access!'];

            $this->response($response);
            exit();
        } else {

            $response = ['status' => 'true', 'message' => 'Access'];

             $this->response($response);

            //return $data;
        }
    } catch (Exception $e) {
        // Token is invalid
        // Send the unathorized access message
        $status = parent::HTTP_UNAUTHORIZED;
        $response = ['status' => false, 'message' => 'Unauthorized Access! '];

        $this->response($response);
    }


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


    /**
     * Login function
     */

	function login_post() {


		//retrieve data from ionic input form
        $_POST = json_decode(file_get_contents('php://input'), true);

        //$_POST['email']
       // $_POST['password']

        $login = xss_clean($this->post('email'));
		$password = xss_clean($this->post('password'));


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


    $time = time();
    $key = $this->config->item('jwt_key');
    $token_timeout = $this->config->item('token_timeout');
    $token_name = $this->config->item('token_name');
    $tokenId    = base64_encode(mcrypt_create_iv(32));
    $issuedAt   = time();
    $notBefore  = $issuedAt + 10;             //Adding 10 seconds
    $expire     = $notBefore + 60;            // Adding 60 seconds
    $serverName = APP_NAME; // Retrieve the server name from config file
    $token = array(

    'iat' => $time, // Tiempo que inició el token
    'exp' => $time + $token_timeout, // config JWT

    'data' => [ // información del usuario
                'username' => $user->username,
                'email' => $user->email,
                'id' => $user->id,
                //'last_name' => $user->last_name,
                //'name' => $user->name,
                'full_name' => ucwords($user->name). " ".ucwords($user->last_name)
    ]
);

   /*json_encode( $userdata = [

    	       'username' => $user->username,
                'email' => $user->email,
                'id' => $user->id,
                'full_name' => $user->last_name,
                'name' => $user->name
]);*/


   

       $jwt = JWT::encode($token, $key);


                        #$cookie = array(
                        #'name'   => $token_name,
                        #'value'  => $jwt,                            
                        #'expire' => $token_timeout,                   
                        #'secure' => TRUE
                        #);

                          
                    $this->session->set_userdata(array(

                               #'iat' => $time, // Tiempo que inició el token
                               #'exp' => $time + $token_timeout, // config JWT
                               $token_name    => $jwt
                               #'id'    => $user->id,
                               #'username'  => $user->username,
                               #'status'    => ($user->activated == 1) ?STATUS_ACTIVATED : STATUS_NOT_ACTIVATED, 
                           )); 
                       
                        // cookie session
                         #set_cookie($cookie);   
                         #$this->input->set_cookie($cookie);

				    $this->response(array('status' => 'true', 'message' => 'Successfully login!',
                     'TokenData' => $token,'token' => $jwt));
 
            
            }else{

            // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                $this->response(array('status' => 'false' , 'message' => 'Wrong email or password.'));

            }

		}else{
			//$this->response(array('status' => 'false', 'message' => 'User Not Found'));
             $this->response(array('status' => 'false' , 'message' => 'Wrong email or password.'));


		}

  }else{

  // Set the response and exit
   $this->response("Provide email and password.", REST_Controller::HTTP_BAD_REQUEST);

  }

	}




/**
 * @return [type]
 */
function phone_verification_post()
{


$id = $this->session->userdata('id');


 /* TRANSACTION DATA ARRAY */
$temp_data =  array(

'card_number'   =>  $card_number,
'card_type'    =>  $card_type,
'id_card'      =>  $card_id,
'id_paymethod' => $payment_method 

); 



/**** SAVE TEMP DATA ****/
#$#$this->session->set_userdata($data_transaction);
 $this->session->set_tempdata($temp_data, 500);



	if (empty($id)) {

      $this->response(array('status' => 'No Loged user'), 400);
         }else{


 //check user duplicate number
$query = $this->db->query("SELECT id, phone_number FROM $this->userTbl WHERE 'id'='{$id}' ");
	
//set session after user signup successfully
			if($query->num_rows() == 1){
				$result = $query->result();
				$phone_number = $result[0]->phone_number;
				#$this->session->set_userdata('id', $id);
				$this->response(array('status' => 'success', 'phone_number' => $phone_number), 200);
			}

$this->response(array('status' => 'phone no found', 'phone_number' => $phone_number), 400);

 #$phone = $this->post('phone');
 $code_auth = $this->post('code_auth');

 $codegen = 12345;


}




/**
 * @return [type]
 */
function OTP_verification_post()
{

//retrieve data from ionic input form
 $_POST = json_decode(file_get_contents('php://input'), true);

$config = [
    [       // Email
            'field' => 'code',
            'label' => 'Code',
            'rules' => 'required|trim|xss_clean|valid_email|min_length[11]',

            'errors' => [
                    'required' => 'Please enter the pin sended to you movil',
                    'min_length' => 'Minimum %s length is 11 characters',
                   # 'user_exists'     => 'This user no exists.'
                  #  'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
            ],
    ],

];

$data = $this->input->post();

$this->form_validation->set_data($data);
$this->form_validation->set_rules($config);

if($this->form_validation->run()==FALSE){
	//trigger

   

$this->response(array('status' => $this->form_validation->error_array()), REST_Controller::HTTP_BAD_REQUEST);

}else{


                // validation ok
                if (!is_null($data = $this->tank_auth->forgot_password(
                        $this->form_validation->set_value('email')))) {

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with password activation link
                    #$this->_send_email('auth/forgot_password/password_reset', $data['email'], $data);

                    


$email = xss_clean($this->post('code'));

    #echo "SUCCESS!!";
    $this->response(array('status' => 'Retrieval mail sent, please check your inbox', 'email' => $email), 200);
}





}

}


}/*end function*/



function user_exists($key)
{
    $this->db->where('email',$key);
    $query = $this->db->get($this->userTbl);
    if ($query->num_rows() > 0){
        return true;
    }
    else{
        return false;
    }
}



function forgot_password_post()
{

//retrieve data from ionic input form
$_POST = json_decode(file_get_contents('php://input'), true);

$config = [
    [       // Email
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|trim|xss_clean|valid_email|callback_user_exists|min_length[6]',

            'errors' => [
                    'required' => 'We need a Email',
                    'min_length' => 'Minimum %s length is 6 characters',
                    'user_exists'     => 'This user no exists.'
                  #  'alpha_dash' => 'You can only use a-z 0-9 _ . – characters for input',
            ],
    ],

];

$data = $this->input->post();

$this->form_validation->set_data($data);
$this->form_validation->set_rules($config);

if($this->form_validation->run()==FALSE){

    #print_r($this->form_validation->error_array());
    #echo "ERROR!!";

   $errors =  validation_errors();

$this->response(array('status' => 'false' , 'message' => $errors));


}else{

                // validation ok
                if (!is_null($data = $this->tank_auth->forgot_password(
                        $this->form_validation->set_value('email')))) {

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with password activation link
                    #$this->_send_email('auth/forgot_password/password_reset', $data['email'], $data);

                    $this->_send_email('forgot_password', $data['email'], $data);


//$email = xss_clean($this->post('email'));

    #echo "SUCCESS!!";
    //$this->response(array('status' => 'false' , 'message' => $this->form_validation->error_array()));


    $this->response(array('status' => 'true' , 'message' =>  'Retrieval mail sent, please check your inbox'));
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