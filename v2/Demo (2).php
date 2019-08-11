<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */


require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Demo extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // trun off csrf protection
         #$config['csrf_protection'] = FALSE;

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

    }

    


    function isAuth()
  {
    $id = $this->session->userdata('id');

    return isset($id) && $id > 0;
  }




/**
 * { function_description }
 */

 public function login_get() {
    
    $username = $this->post('username');
    $password = $this->post('password');
    $password = md5($password);


    $query = $this->db->query("SELECT id, username, password FROM users_rest WHERE username='{$username}' and password='{$password}'");

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




 public function signup_post(){
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





/**
 * { function_description }
 */
public function register_get() {
    

    $username = $this->post('username');
    $password = $this->post('password');
    $username = $this->post('username');
    $password = $this->post('password');
    $password = md5($password);


    $query = $this->db->query("SELECT id, username, password FROM users_rest WHERE username='{$username}' and password='{$password}'");

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














/**
 * { function_description }
 */
  public function logsin_post() {
    // $rawpostdata = file_get_contents("php://input");
    // $post = json_decode($rawpostdata, true);
    // $username = $post['username'];
    // $password = $post['password'];



  }



/**
 * get data user from db
 * @return [type] [description]
 */
public function user_get(){



//auth user 
  if (!$this->isAuth()) {
      $this->response(array('error' => 'no login'), 200);
     } else {


// check api key
#$query = $this->db->query("SELECT * FROM `keys` WHERE `key`='{$apikey}'");
    #  if ($query->num_rows == 1) {
      #  $result = $query->result();
        // var_dump($result[0]);
        #$user_id = $result[0];
        #$user_id = $user_id->user_id;
    #  } else {

     #   $this->response(array('status' => 'false', 'error' => 'Wrong API key'), 403);
     # }


     
     $name = $this->post('name');
    # $name = $this->get('name');


       if (empty($name)) {

        // if empty var name, show all user

         $this->db->select('id, name,last_name,username'); 
          # $this->db->where('name', $name);
           $result = $this->db->get('users')->result();
            print_r(json_encode($result));


        }else{

            //else var name no empty search in db
      
           $this->db->select('id, name,last_name,username'); 
           $this->db->where('name', $name);
           $result = $this->db->get('users')->result();

        if ( empty($result)) {
            //if user do not found in db show message

                  $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code

           }
            // if user do found in db print user
           else{   print_r(json_encode($result));    }
            

   }

  }
      
    }











/**
    * Create from display on this method.
    *
    * @return Response
   */
   public function his_get()
   {
     # $draw = intval($this->input->get("draw"));
      #$start = intval($this->input->get("start"));
     # $length = intval($this->input->get("length"));
   
     # $id_user    = $this->tank_auth->get_user_id();

     # $this->db->where('id_user', $id_user);
     # $this->db->order_by("date  asc");
      $query = $this->db->get("tbl_history_transaction ");


      $data = [];


      foreach($query->result() as $r) {
           $data[] = array(
           "id_track"   =>   $r->id_track, 
           "Payment"    =>    method_helper($r->id_paymethod), 
           "sender"     =>    user_helper($r->id_from),
           "receiver"   =>    user_helper($r->id_to),
           "comit"      =>    number_format($r->comit,2),
           "currency"   =>    currency_helper($r->id_currency),
           "money_send" =>    number_format($r->moneysend,2),
           "time"       =>    date(" h:i a", strtotime($r->date)),
           "date"       =>    date(" d/m/Y",strtotime($r->date))
           );
      }




 return $this->response($data, 200);


      exit();
   

}









/**
    * Create from display on this method.
    *
    * @return Response
   */
   public function history_user_get()
   {
     # $draw = intval($this->input->get("draw"));
      #$start = intval($this->input->get("start"));
     # $length = intval($this->input->get("length"));
   
     # $id_user    = $this->tank_auth->get_user_id();

     # $this->db->where('id_user', $id_user);
     # $this->db->order_by("date  asc");
      $query = $this->db->get("tlb_history_transaction ");


      $data = [];


      foreach($query->result() as $r) {

      $id_user   =   $r->id_user;
      $id_from   =   $r->id_from;
      $id_to     =   $r->id_to; 

     if ($id_user == $id_to) {
        # code...


           $data[] = array(
           "action"     =>    $r->id_action, 
           "user"       =>    $id_user, 
           "Payment"    =>    method_helper($r->id_paymethod), 
           "sender"     =>    user_helper($id_from),
           "receiver"   =>    user_helper($id_to),
           "comit"      =>    number_format($r->comit,2),
           "currency"   =>    currency_helper($r->id_currency),
           "money_send" =>    number_format($r->moneysend,2),
           "time"       =>    date(" h:i a", strtotime($r->date)),
           "date"       =>    date(" d/m/Y",strtotime($r->date))

           );

     }else {




           $data[] = array(
           "action"      =>    $r->id_action, 
           "user"       =>    $id_user, 
           "Payment"    =>    method_helper($r->id_paymethod), 
           "sender"     =>    user_helper($id_from),
         #  "receiver"   =>    user_helper($r->id_to),
         #  "comit"      =>    number_format($r->comit,2),
           "currency"   =>    currency_helper($r->id_currency),
           "money_send" =>    number_format($r->moneysend,2),
           "time"       =>    date(" h:i a", strtotime($r->date)),
           "date"       =>    date(" d/m/Y",strtotime($r->date))

           );




   }




      }


           #  $result = array(
                # "draw" => $draw,
                # "recordsTotal" => $query->num_rows(),
                # "recordsFiltered" => $query->num_rows(),
                # "data" => $data
           #);


      echo json_encode($data);

      exit();
   

}


















/**
 * get card user
 * @return [type] [description]
 */
public function card_get()
    {
     

 
   $id_user = $this->get('id');


       if (empty($id_user)) {
        
        // if empty var name, show all user

        $this->db->select('id_card,id_user,card_owner,card_number,card_mm,card_aa'); 
        #   $this->db->where('id_user', $id_user);
           $result = $this->db->get('cards')->result();
            print_r(json_encode($result));


        }else{

            //else var name no empty search in db
      
          $this->db->select('id_card,id_user,card_owner,card_number,card_mm,card_aa'); 
           $this->db->where('id_user', $id_user);
           $result = $this->db->get('cards')->result();

        if ( empty($result)) {
            //if user do not found in db show message

                  $this->set_response([
                'status' => FALSE,
                'message' => 'card  could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code

           }
            // if user do found in db print user
           else{   print_r(json_encode($result));    }
            

   }










      
    }











/**
 * get user history
 * @return [type] [description]
 */
public function histori3(){


#$id=  $this->get('id');
        
            
#$result = $this->db->get('tlb_history_transaction')->result_array();
#print_r(json_encode($result));



 $this->db->select('*'); 
          # $this->db->where('name', $name);
           $result = $this->db->get('cards')->result();
            print_r(json_encode($result));




}









    public function users_post()
    {
        // $this->some_model->update_user( ... );
        $message = [
            'id' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function users_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

}
