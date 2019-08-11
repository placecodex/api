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

class Dashboard extends REST_Controller {


	    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // trun off csrf protection
         $config['csrf_protection'] = FALSE;

    }

	
	function isAuth()
	{
		$id = $this->session->userdata('id');
		return isset($id) && $id > 0;
	}

	function home_post(){
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
}