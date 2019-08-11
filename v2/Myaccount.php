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

class Myaccount extends REST_Controller
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

    

/**
 * @return [type]
 */
function home()
{


$id = $this->session->userdata('id');

	if (empty($id)) {

      $this->response(array('status' => 'No Loged user'), 400);

         }else{



}



}


function change_password()
{


}


function history()
{


}


function profile()
{


}



	
}