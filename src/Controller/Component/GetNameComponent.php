<?php 

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class GetNameComponent extends Component
{
  public function getNameUser($id)
  {
    $user = TableRegistry::getTableLocator()->get('Users')
      ->find()
      ->select(['display_name'])
      ->where(['id' => $id])
      ->first();
    
    return $user ? $user->display_name : null;
  }

  public function getNameClient($id)
  {
    $client = TableRegistry::getTableLocator()->get('Clients')
      ->find()
      ->select(['name'])
      ->where(['id' => $id])
      ->first();
    
    return $client ? $client->name : null;
  }

  public function getNameBp($id)
  {
    $bp = TableRegistry::getTableLocator()->get('Bps')
      ->find()
      ->select(['name'])
      ->where(['id' => $id])
      ->first();
    
    return $bp ? $bp->name : null;
  }

  public function getNameEngineer($id)
  {
    $engineer = TableRegistry::getTableLocator()->get('Engineers')
      ->find()
      ->select(['name'])
      ->where(['id' => $id])
      ->first();
    
    return $engineer ? $engineer->name : null;
  }
}
