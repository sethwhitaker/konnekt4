<?php
class Game_model extends CI_Model {

  private $table;

  public function __construct() {
    parent::__construct();

    $this->table = 'game';
  }

  public function newGame($user_id, $challenged_id){
    $time=time();
    $this->db->insert($this->table, array(
      'whose_turn' => 0,
      'active'=>1,
      'last_updated' => $time
    ));
    $insert_id = $this->db->insert_id();
    $this->db->insert('game_user', array(
      'game_id'=> $insert_id,
      'user_id'=> $user_id,
      'challenge_type_id'=>1
    ));
    $this->db->insert('game_user', array(
      'game_id'=> $insert_id,
      'user_id'=> $challenged_id,
      'challenge_type_id'=>2
    ));
  }

  public function start($game_Id){
    $data = array(
      "player0_pieceId"=> null,
      "player0_boardR" => null,
      "player0_boardC" => null,
      "player1_pieceId"=> null,
      "player1_boardR" => null,
      "player1_boardC" => null
    );
    $query = $this->db->where("game_Id", $game_Id)->update('game', $data);

    return $this->getMove($game_Id);
  }

  public function getGameData($game_Id){
    $query = $this->db->select('u1.username AS player0_name, u1.user_Id AS player0_Id, u2.username AS player1_name, u2.user_Id player1_Id, g.game_Id, g.whoseTurn')
    ->from('game g')->where("g.game_Id", $game_Id)
    ->join('users u1','g.player0_Id=u1.user_Id','inner')
    ->join('users u2','g.player1_Id=u2.user_Id','inner')->get();
    if($query->num_rows() > 0){
      $cur = $this->session->userdata('user_Id');
      $row = $query->row();
      $r['id'] = $row->id;
      $r['whose_turn'] = $row->whose_turn;

      if($row->player0_Id === $cur){
        //Flat Format
        $r['current_player']['id'] = $row->player0_Id;
        $r['current_player']['playerId'] = 0;
        $r['current_player']['username'] = $row->player0_name;
        $r['current_player']['name'] = ucfirst($row->player0_name);
        $r['opponent_player']['id'] = $row->player1_Id;
        $r['opponent_player']['playerId'] = 1;
        $r['opponent_player']['username'] = $row->player1_name;
        $r['opponent_player']['name'] = ucfirst($row->player1_name);
        //Array Format
        $r['players'][0]['id'] = $row->player0_Id;
        $r['players'][0]['playerId'] = 0;
        $r['players'][0]['username'] = $row->player0_name;
        $r['players'][0]['name'] = ucfirst($row->player0_name);
        $r['players'][0]['current'] = TRUE;
        $r['players'][1]['id'] = $row->player1_Id;
        $r['players'][1]['playerId'] = 1;
        $r['players'][1]['username'] = $row->player1_name;
        $r['players'][1]['name'] = ucfirst($row->player1_name);
        $r['players'][1]['current'] = FALSE;
      }else{
        //Flat Format
        $r['current_player']['id'] = $row->player1_Id;
        $r['current_player']['playerId'] = 1;
        $r['current_player']['username'] = $row->player1_name;
        $r['current_player']['name'] = ucfirst($row->player1_name);
        $r['opponent_player']['id'] = $row->player0_Id;
        $r['opponent_player']['playerId'] = 0;
        $r['opponent_player']['username'] = $row->player0_name;
        $r['opponent_player']['name'] = ucfirst($row->player0_name);
        //Array Format
        $r['players'][0]['id'] = $row->player1_Id;
        $r['players'][0]['playerId'] = 1;
        $r['players'][0]['username'] = $row->player1_name;
        $r['players'][0]['name'] = ucfirst($row->player1_name);
        $r['players'][0]['current'] = TRUE;
        $r['players'][1]['id'] = $row->player0_Id;
        $r['players'][1]['playerId'] = 0;
        $r['players'][1]['username'] = $row->player0_name;
        $r['players'][1]['name'] = ucfirst($row->player0_name);
        $r['players'][1]['current'] = FALSE;
      }

      return $r;
    }
 }

  public function getChallenges($user_id){
    $games = $this->db->select('game_id')->from('game_user')
      ->where('user_id', $user_id)
      ->where('challenge_type_id', 1)
      ->get();

    if($games->num_rows() > 0){
      $result = []; $i = 0;
      foreach ($games->result() as $game){
        $game_id = $game->game_id;
        $query = $this->db->select('u.username, gu.game_id')
          ->from('game_user gu')
          ->join('user u','gu.user_id=u.id', 'inner')
          ->where('gu.game_id',$game_id)
          ->where_not_in('gu.user_id', $user_id)
          ->get();
        if($query->num_rows() > 0){
          foreach($query->result() as $challenge){
            $result[$i]['username'] = $challenge->username;
            $result[$i]['game_id'] = $challenge->game_id;
            $i++;
          }
        }
      }
      return $result;
    }
  }

  public function getChallengers($user_id){
    $games = $this->db->select('game_id')->from('game_user')
      ->where('user_id', $user_id)
      ->where('challenge_type_id',2)
      ->get();

    if($games->num_rows() > 0){
      $result = []; $i = 0;
      foreach ($games->result() as $game){
        $game_id = $game->game_id;
        $query = $this->db->select('u.username, gu.game_id')
          ->from('game_user gu')
          ->join('user u','gu.user_id=u.id', 'inner')
          ->where('gu.game_id',$game_id)
          ->where_not_in('gu.user_id', $user_id)
          ->get();
        if($query->num_rows() > 0){
          foreach($query->result() as $challenge){
            $result[$i]['username'] = $challenge->username;
            $result[$i]['game_id'] = $challenge->game_id;
            $i++;
          }
        }
      }
      return $result;
    }
  }

  public function getTurn($id){
    $query = $this->db->select('whose_turn')->where('id', $id)->get('game');
    if($query->num_rows() > 0){
      return $query->result();
    }
  }

  public function changeTurn($id){
    // $query = $this->db->where('id', $id)->update('game', array('whose_turn'=>ABS('whose_turn-1')));

    $stmt = "UPDATE game SET whose_turn=ABS(whose_turn-1) WHERE id=?";
    $this->db->query($stmt, array($id));
  }

  public function changeBoard($game_Id, $playerId, $pieceId, $r, $c){
    $data = array(
      "player".$playerId."_pieceId"=> $pieceId,
      "player".$playerId."_boardR" => $r,
      "player".$playerId."_boardC" => $c
    );
    $query = $this->db->where('game_Id', $game_Id)->update('game', $data);
  }

  public function getMove($id){
    $query = $this->db->get_where($this->table, array('id'=> $id));
    if($query->num_rows() > 0){
      $results =  $query->result();
      return $results;
    }
  }
}
?>