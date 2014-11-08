<?php
class User_model extends CI_Model {

  public $name;
  public $email;
  public $password;
  public $logged_in;

  public function __construct() {
    parent::__construct();
  }

  //checks a users login to see if they exist in the database
  public function login(){
    $encrypted_password = sha1($this->input->post('password'));
    $username = $this->input->post('name');
    $query = $this->db->get_where('users', array('username'=>$username, 'password'=>$encrypted_password));
    $result = $query->result();
    $num = count($result);
    if($num > 0){
      $id=$result[0]->user_Id;
      $this->db->where('user_Id',$id);
      $this->db->update('users', array('logged_In'=>1));
      return $result;
    }else{
      return false;
    }
  }

  //Logout Function
  public function logout($id){
    if ($id){
      $this->db->where('user_Id',$id);
      $this->db->update('users', array('logged_In'=>0));
      return true;
    }else{
      return false;
    }
  }
}
?>