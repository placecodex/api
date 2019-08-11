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
use Firebase\JWT\JWT;


class App extends REST_Controller {


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

        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);



        // trun off csrf protection
         #$config['csrf_protection'] = FALSE;

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        //$this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        //$this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
       // $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key



    }
   
	
	
   
    /**
     * [verify_request description]
     * @return [type] [description]
     */
    private function IsAuth()
{



$key = $this->config->item('jwt_key');
$cookie_name = $this->config->item('token_name');
//$token = $this->session->userdata($cookie_name);

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
            $this->response($response, $status);
            exit();
        } else {
            return $data;
        }
    } catch (Exception $e) {
        // Token is invalid
        // Send the unathorized access message
        $status = parent::HTTP_UNAUTHORIZED;
        $response = ['status' => false, 'message' => 'Unauthorized Access! '];
        $this->response($response, $status);
    }
}/***/


		function __dashboard_get()
	{
		$this->IsAuth();

		
     

	}



/* Dashboard 
*
*
*/
public function dashboard_post()
{

$auth = $this->IsAuth();


    // Get all the headers
$headers = $this->input->request_headers();
$token_ = $headers['Authorization'];	


$key = $this->config->item('jwt_key');
//$cookie_name = $this->config->item('token_name');
//$token_code = $this->session->userdata($cookie_name);



$decoded = JWT::decode($token_, $key, array('HS256'));
$decoded = (array) $decoded;



 $user_id = $decoded['data']->id;
 $full_name = $decoded['data']->full_name;
 //$user_id = 32;
 #$last_name = $decoded['data']->last_name;

//var_dump($decoded);
//echo $token;



 // data user 
$user = $this->Tbl_users_profile->getUser($user_id);
// data wallet user
//$wallet = $this->Tbl_wallet->CheckBalance($user_id);

  // Convert Array to JSON String
  $someJSON = $someArray;

 $list = 	$this->AjaxListHistory($user_id,3);
 
//$historys = AjaxListHistory($user_id,3);

//$historys = $hisArray;

//echo  json_encode($decoded);

$this->response(array(
	'status' => 'true',
	'id_user' => $user_id,
	'wellcome_message' => 'Bienvenido',
	'money' => '$DOP '.number_format($user->DOP,2),
	'full_name' => $full_name,
	'history' => $list

	 //'id' =>  $name


	));



}//end function dashboard


public function myaccount_post()
{



    // Get all the headers
$headers = $this->input->request_headers();
$token_ = $headers['Authorization'];	
$key = $this->config->item('jwt_key');
// decode data
$decoded = JWT::decode($token_, $key, array('HS256'));
$decoded = (array) $decoded;

 // data decoded
  $id_user = $decoded['data']->id;
  $full_name = $decoded['data']->full_name;
  $email = $decoded['data']->email;
  $username = $decoded['data']->username;

    $this->response(

    	array(

    		'status' => 'true',
    		'full_name' => $full_name,
    		'username' => $username,
    		'email' => $email,
    		'id_user' =>  $id_user

    	));


}

//card section
public function card_post()
{



    // Get all the headers
$headers = $this->input->request_headers();
$token_ = $headers['Authorization'];	
$key = $this->config->item('jwt_key');
// decode data
$decoded = JWT::decode($token_, $key, array('HS256'));
$decoded = (array) $decoded;

 // data decoded
 $id_user = $decoded['data']->id;







   //$list = 	$this->card_list($id_user);

   $card_list    = $this->card_list($id_user);

    $this->response(

    	array('status' => 'true',
    		'card' => $card_list

    	));


}// end function card


/**
 * decode card data
 * @param  [type] $id_user [description]
 * @return [type]          [description]
 */
   public function card_list($id_user)
   {

    #$secret_key = 'nK-6<eN$=D.~bZt{,V#6yW2pu(QT!*-,';
     
     # $query = $this->db->get("cards");
    $query =  $this->Tbl_card->get_data_card($id_user);

    $data = [];
   
      foreach($query->result() as $r) {

           $data[] = array(


       "card_owner"       =>    $this->encrypt->decode($r->card_owner), 
      "card_number"      =>    truncate_card($this->encrypt->decode($r->card_number)), 
      "card_mm"          =>    $this->encrypt->decode($r->card_mm),
      "card_aa"          =>    $this->encrypt->decode($r->card_aa),
      "card_approved"    =>    $this->encrypt->decode($r->card_approved),
      "card_type"        =>    $r->card_type,
      "url_img_card"        =>    card_img_helper($this->encrypt->decode($r->card_type))
           
         
           );
      }


    return  $data; 

      exit();
   

}/*end function*/



public function add_card_post()
{


}

public function delete_card_post()

{


}

public function edit_card_post()
{


}		

   /*
     User exist checker
    */
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


	function send_post()
	{
		//$this->IsAuth();
		

        //retrieve data from ionic input form
$_POST = json_decode(file_get_contents('php://input'), true);

$config = [
    [       // Email
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|trim|xss_clean|valid_email|callback_user_exists',

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

              


    $this->response(array('status' => 'true' , 'message' =>  'user fount'));
      }



	}/*end function*/


	function send_step2_post()
	{


	/*$this->response(array(

	'status' => 'false',
	'id_user' => '32',
	'name_user' => 'Mike Rico',
	'email_user' => 'michael12@hotmail.es', 
	//'message' => $errors

	));*/


		//$this->IsAuth();
		//retrieve data from ionic input form
        $_POST = json_decode(file_get_contents('php://input'), true);


        $config = [
    [       // Email
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'required|trim|xss_clean',

            'errors' => [
                    'required' => 'Debe indicar el monto',
                   // 'min_length' => 'Minimum %s length is 6 characters',
                   // 'user_exists'     => 'This user no exists.'
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

	
     $this->response(array(

	'status' => 'false',
	'id_user' => '32',
	'name_user' => 'Mike Rico',
	'email_user' => 'michael12@hotmail.es', 
	'message' => $errors

	));



}else{

              


    $this->response(array(
    	'status' => 'true' ,
    	 'message' =>  'success'

    	));



      }

        





     

	}// end function


	function send_step3_post()
	{


	/*$this->response(array(

	'status' => 'false',
	'id_user' => '32',
	'name_user' => 'Mike Rico',
	'email_user' => 'michael12@hotmail.es', 
	//'message' => $errors

	));*/


		//$this->IsAuth();
		//retrieve data from ionic input form
        $_POST = json_decode(file_get_contents('php://input'), true);


        $config = [
    [       // Email
            'field' => 'method_payment',
            'label' => 'method_payment',
            'rules' => 'required|trim|xss_clean',

            'errors' => [
                    'required' => 'Debe indicar el metodo de pago',
                   // 'min_length' => 'Minimum %s length is 6 characters',
                   // 'user_exists'     => 'This user no exists.'
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

	
     $this->response(array(

	'status' => 'false',
	'id_user' => '32',
	'name_user' => 'Mike Rico',
	'email_user' => 'michael12@hotmail.es', 
	'message' => $errors

	));



}else{

              


    $this->response(array(
    	'status' => 'true' ,
    	 'message' =>  'success'

    	));



      }

        





     

	}



	function get_get(){
		 if(!$this->isAuth()){
		 	$this->response(array('error' => 'no login'), 200);
		 }else{
			$id = $this->session->userdata('id');
			if (!is_numeric($id)) {
				$this->response(array('error' => 'invalid user id'), 403);
			}
			$query = $this->db->query("SELECT * FROM timezone WHERE user_id={$id}");
			$result = $query->result();
			$data = array();
			foreach($result as $item){
				// $data->title = $item.title;
				// $data->desc = $item.desc;
				// $data->id = $item.id;
				// $data->amount = $item.amount;
				// $item->comments = $this->getComments($item->id);
			}
			$this->response(array('data' => $result), 200);
		 }
	}

	function getComments($id){
		$query = $this->db->query("SELECT * from comment WHERE expenseid='{$id}' ");
		$result = $query->result();
		return $result;
	}

	// function update_post(){
	function update_put(){
		// if (!$this->isAuth()) {
		// 	$this->response(array('error' => 'no login'), 200);
		// } else {
			$id = $this->put('id');
			// $user_id = $this->session->userdata('id');
			$name = $this->put('name');
			$city = $this->put('city');
			$timezone = $this->put('timezone');
			$apikey = $this->put('apikey');


			if (empty($name) || empty($city) || empty($timezone) || empty($id) || empty($apikey) ||
				strlen($name) > 20 || strlen($city) > 20 || !preg_match('/^GMT[\+\-]1?\d$/', $timezone)){
				$this->response(array('status' => 'false', 'error' => 'invalid input'), 403);
			}

			$query = $this->db->query("SELECT * FROM `keys` WHERE `key`='{$apikey}'");
			if ($query->num_rows == 1) {
				$result = $query->result();
				// var_dump($result[0]);
				$user_id = $result[0];
				$user_id = $user_id->user_id;
			} else {
				$this->response(array('status' => 'false', 'error' => 'Wrong API key'), 403);
			}
			$data = array(
				'name' => $this->put('name'),
				'city' => $this->put('city'),
				'timezone' => $this->put('timezone')
			);

			$this->db->where('id', $id);
			$this->db->update('timezone', $data);

			$query = $this->db->query("SELECT * from timezone WHERE id = {$id} ");
			if($query->num_rows == 1){
				$query2 = $this->db->query("SELECT * from timezone WHERE id = {$id} AND user_id = {$user_id}");
				if ($query2->num_rows == 0) {
					$this->response(array('status' => 'no auth', 'error' => 'no auth'), 200);
				}
				$result = $query->result();
				$data = $result[0];
				$this->response(array('status' => 'success', 'data' => $data), 200);
			}else{
				$this->response(array('status' => 'not exists', 'error' => 'not exists'), 200);
			}
		// }
	}

	function add_post() {

		// if (!$this->isAuth()) {
		// 	$this->response(array('error' => 'no login'), 200);
		// } else {
			$name = $this->post('name');
			$city = $this->post('city');
			$timezone = $this->post('timezone');
			$apikey = $this->post('apikey');

			// echo substr($timezone, 0, 3);
			// if (empty($name) || empty($city) || empty($timezone)
			// 	|| strlen($name) > 20 || strlen($city) > 20 || substr($timezone, 0, 3) != 'GMT') {
			// 	header("HTTP/1.1 200 OK");
			// 	echo json_encode(array('status' => 'invalid parameters'));
			// 	return;
			// }

			if (empty($name) || empty($city) || empty($timezone) || empty($apikey) ||
				strlen($name) > 20 || strlen($city) > 20 || !preg_match('/^GMT[\+\-]1?\d$/', $timezone)){
				$this->response(array('status' => 'false', 'error' => 'invalid input'), 403);
			}

			$query = $this->db->query("SELECT * FROM `keys` WHERE `key`='{$apikey}'");
			if ($query->num_rows == 1) {
				$result = $query->result();
				// var_dump($result[0]);
				$user_id = $result[0];
				$user_id = $user_id->user_id;
			} else {
				$this->response(array('status' => 'false', 'error' => 'Wrong API key'), 403);
			}
			$data = array(
				'name' => $this->post('name'),
				'city' => $this->post('city'),
				'timezone' => $this->post('timezone'),
				// 'user_id' => $this->session->userdata('id')
				'user_id' => $user_id
			);

			$this->db->insert('timezone', $data);
			$id = $this->db->insert_id();
			$query = $this->db->query("SELECT * from timezone WHERE id={$id} ");
			if ($query->num_rows == 1) {
				$result = $query->result();
				$data = $result[0];
				$this->response(array('status' => 'success', 'data' => $data), 200);
			}

			$this->response(array('status' => 'fail'), 200);
		// }
	}

	function addComment_post(){
		if(!$this->isAuth()){
			$this->response(array('error' => 'no login'), 200);
		}else{
			$data = array(
				'expenseid' => $this->post('id'),
				'comment' => $this->post('comment'),
			);

			$this->db->insert('comment', $data);
			$id = $this->db->insert_id();
			$query = $this->db->query("SELECT * from comment WHERE id={$id} ");
			if($query->num_rows == 1){
				$result = $query->result();
				$data = $result[0];
				$this->response(array('status' => 'success', 'data' => $data), 200);
			}

			$this->response(array('status' => 'fail'), 200);
		}
	}

	// function delete_post(){
	function delete_post(){
		// if (!$this->isAuth()) {
		// 	$this->response(array('error' => 'no login'), 200);
		// } else {
			$id = $this->get('id');
			$apikey = $this->post('apikey');
// echo 'delete' . $apikey;
			$query = $this->db->query("SELECT * FROM `keys` WHERE `key`='{$apikey}'");
			if ($query->num_rows == 1) {
				$result = $query->result();
				// var_dump($result[0]);
				$user_id = $result[0];
				$user_id = $user_id->user_id;
			} else {
				$this->response(array('status' => 'false', 'error' => 'Wrong API key'), 403);
			}
			$query = $this->db->query("SELECT * from timezone WHERE id='{$id}' AND user_id='{$user_id}'");

			if ($query->num_rows === 0) {		//nothing to delete in the DB
				$this->response(array('status' => 'not exists'), 200);
				return;
			} else {
				$this->db->query("DELETE from timezone where id='{$id}' ");
				$this->response(array('status' => 'success'), 200);
			}
		// }
	}


	/**
    * Create from display on this method.
    *
    * @return Response
   */
   public function _send_post()
   {

   	//$auth = $this->IsAuth();

   	$id_user = 32;


   

}/*end function*/


/**
    * Create from display on this method.
    *
    * @return Response
   */
   public function sendStep2_post()
   {

   	//$auth = $this->IsAuth();

   	$id_user = 32;


   

}/*end function*/


	/**
    * Create from display on this method.
    *
    * @return Response
   */
   public function history_get()
   {

   #	$auth = $this->IsAuth();

   	$id_user = 32;


   $list = 	$this->AjaxListHistory($id_user,3);

    $this->response(

    	array('status' => 'true',
    		'history' => $list

    	));
   

}/*end function*/


	/**
    * Create from display on this method.
    *
    * @return Response
   */
   public function AjaxListHistory($id_user ,$limit = '')
   {


       /*
       tlb_history_transaction_model.php  Line 29
       */
//$query = $this->Tbl_order->get_history_x10($id_user);
       $this->db->order_by('date', 'DESC');

       if (!empty($limit)) {
       	$this->db->limit($limit);
       }
         $query = $this->db->where('id_user', $id_user)->or_where('id_to', $id_user)->get('Tbl_order');


       # $query = $this->db->get();
        $data  = [];

      foreach($query->result() as $r) {


      // table history v1.1
         
           $data[] = array(


           "id_track"  =>$r->id_track,
           "id"        =>$r->id,
           "action"     => action_helper($id_user, $r->id_to, $r->id_from,$r->refund), 

           "refund"     => $r->refund, 

             "action_id"     => action_id_helper($id_user, $r->id_to, $r->id_from,$r->refund), 

            "state"     => state_helper($r->state),
            "dispute"     => $r->dispute,

            "owner"    => owner_helper($id_user, $r->id_to, $r->id_from, $r->name_to, $r->name_from), 

           "method_payment" => method_helper($r->id_paymethod),

           "id_paymethod" => $r->id_paymethod, 

          "card_number" => truncate_card($r->card_number), 
          "card_type" => card_img_helper($r->card_type), 
          #  "sender"     => user_helper($r->id_from),
           # "receiver"   => user_helper($r->id_to),
            "commission"      => number_format($r->commission,2),
            "currency"   => currency_helper($r->id_currency),
            "currency_base"   => $r->currency,
            "money_send" => number_format($r->moneysend,2),
            "money_received" => number_format($r->moneyreceived,2),
            "general_money" => general_money_helper($id_user, $r->id_to, $r->id_from, $r->moneysend , $r->moneyreceived),
            "total_money" => number_format($r->total,2),
            "time"       => date(" h:i a", strtotime($r->time_order)),
            "date"       => date(" d/m/Y",strtotime($r->date_order)),
           "D"          => date(" d", strtotime($r->date_order)),
           "M"          => date("M", strtotime($r->date_order))

           );
      }

      // table history v1

            return $data;

      exit();
   

}/*end function*/






}/*end class*/