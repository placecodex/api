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


class Auth extends REST_Controller
{
	    function __construct()
    {

       // Construct the parent class
        parent::__construct();


        /*Table Name*/
        $this->userTbl = 'users';



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


    }

    


    function signup_post(){

     
        $number = $this->post('number');
        $email = $this->post('email');
        $name = $this->post('name');
        $last_name = $this->post('last_name');
        $username = $this->post('username');
        $password = $this->post('password');
        $repassword = $this->post('repassword');


// check password match
       if (!$password == $repassword) {
            $this->response(array('error' => 'password not match'), 400);
      # return  ejson_encode(array('status' => 'password not match'));

        }

        if (empty($username) || empty($password) ||  empty($repassword) || empty($name)  || empty($number) || empty($last_name)  ||  strlen($username) > 20 || strlen($password) > 20 || 
            !preg_match('/^[a-zA-Z0-9_]*$/', $username)) {
            header("HTTP/1.1 200 OK");
            echo json_encode(array('status' => 'Por favor complete todos los campos'));
            return;
        }
       
            $password = md5($password);

         //check user duplicate number
        $query = $this->db->query("SELECT username, email, 'phone_number' FROM $this->userTbl WHERE 'phone_number'='{$number}' ||  email='{$email}' ");
        if($query->num_rows() >= 1){
            $this->response(array('status' => 'false', 'message' => 'Your email or username is used by other user, try with different email or username!'), 400);

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

           $insert = $this->db->insert($this->userTbl, $data); 

            if ($insert) {
            	$query = $this->db->query("SELECT id FROM $this->userTbl WHERE email='{$email}'");

                $result = $query->result();
                $id = $result[0]->id;
                $this->session->set_userdata('id', $id);
                $this->response(array('status' => 'success', 'id' => $id), 200);
            }else{

            $this->response(array('status' => 'false', 'message' => 'Sorry, try again later!'), 400);

            }

           

          //Mail activation
         // Send email with data transaction to user receiver
         # $this->_send_email($type, $email, $tda);


        }

        // echo 'Total Results: ' . $query->num_rows();
    }


	function logout_post(){
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


	   /**
     * { function_description }
     */
    function login_post() {
       


    	#$_POST = json_decode(file_get_contents('php://input'), true);

    #$email = $_POST['email'];
       # $password = $_POST['password'];

   $email = xss_clean($this->post('email'));
    $password = xss_clean($this->post('password'));
        

        // check input 
if (!isset($email) || !isset($password)) {
          $message = json_encode(array('status' => 'false', 'message' => 'invalid parameters some input empty!'));
            echo $message;
            REST_Controller::HTTP_NOT_FOUND;

     
        }
        
       # $password = md5($password);


        $query = $this->db->query("SELECT user_id, email, password FROM $this->userTbl WHERE email='{$email}' and password='{$password}'");

        if($query->num_rows > 0){
            $result = $query->result();
             $result2 = $query->result_array();

            $id = $result[0]->user_id;
            $email = $result[0]->email;
            
            $this->session->set_userdata('id', $id);
            $this->session->set_userdata('email', $email);

            // header('Content-Type: application/json');
            $message = json_encode(array('status' => 'true', 'message' => 'Successfully login!', 'userdata' => $result2 ));
            echo $message;
            REST_Controller::HTTP_OK;

        }else{

              //header('Content-Type: application/json');
            $message = json_encode(array('status' => 'false', 'message' => 'Incorrect email or password!'));
            echo $message;
            REST_Controller::HTTP_NOT_FOUND;
        }
	
	
    }

    



function forgot_password_post()
{

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

$this->response(array('status' => $this->form_validation->error_array()), REST_Controller::HTTP_BAD_REQUEST);
}else{

                // validation ok
                if (!is_null($data = $this->tank_auth->forgot_password(
                        $this->form_validation->set_value('email')))) {

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with password activation link
                    #$this->_send_email('auth/forgot_password/password_reset', $data['email'], $data);

                    $this->_send_email('forgot_password', $data['email'], $data);


$email = xss_clean($this->post('email'));

    #echo "SUCCESS!!";
    $this->response(array('status' => 'Retrieval mail sent, please check your inbox', 'email' => $email), 200);
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