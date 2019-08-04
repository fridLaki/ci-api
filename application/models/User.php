<?php

/**
 *
 */
class User extends CI_Model
{

  function __construct() {
  }

  public function save() {
    $data = [
      'email'       => $this->input->post('email'),
      'password'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      'created_at'  => date('Y-m-d h:i:s')
    ];
    if($this->db->insert('users', $data)) {
      return [
        'id' => $this->db->insert_id(),
        'success' => true,
        'message' => 'data berhasil dimasukan'
      ];
    }
  }

  public function get($key=null, $value=null) {
    if ($key!=null) {
      $this->db->select('id, password, email, created_at, updated_at');
      $query = $this->db->get_where('users', array($key => $value));
      return $query->row();
    }
    // $sql = $this->db->select('id, email, created_at, updated_at')->get_compiled_select('users');
    // $query = $this->db->query($sql);
    $this->db->select('id, password, email, created_at, updated_at');
    $query = $this->db->get('users');
    return $query->result();
  }

  public function is_valid() {
    $email    = $this->input->post('email');
    $password = $this->input->post('password');

    $hash = $this->get('email', $email)->password;

    if(password_verify($password, $hash))
        return true;

    return false;
  }

  public function delete($id) {
    $this->db->where('id', $id);
    if ($this->db->delete('users')) {
      return [
              'success' => true,
              'message' => 'data berhasil dihapus'
      ];
    }
  }
}
