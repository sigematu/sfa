<?php 

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class PicComponent extends Component
{
  public function getLastName($controller, $class)
  {
    $sq = TableRegistry::getTableLocator()->get('Users')
      ->find()
      ->select(
        ['Users.last_name']
      )
      ->where(
        [$controller . "." . $class . " = Users.id"]
      );
    
    return $sq;
  }

  public function getFirstName($controller, $class)
  {
    $sq = TableRegistry::getTableLocator()->get('Users')
      ->find()
      ->select(
        ['Users.first_name']
      )
      ->where(
        [$controller . "." . $class . " = Users.id"]
      );
    
    return $sq;
  }

  public function getDisplayName($controller, $class)
  {
    $sq = TableRegistry::getTableLocator()->get('Users')
      ->find()
      ->select(
        ['Users.display_name']
      )
      ->where(
        [$controller . "." . $class . " = Users.id"]
      );
    
    return $sq;
  }
}
