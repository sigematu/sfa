<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\Controller\Component\GetNameComponent;
use Cake\Controller\ComponentRegistry;

class AppHelper extends Helper
{
    public function getNameUser($id)
    {
        $this->GetName = new GetNameComponent(new ComponentRegistry());
        $userName = $this->GetName->getNameUser($id);

        return $userName;
    }

    public function getNameClient($id)
    {
        $this->GetName = new GetNameComponent(new ComponentRegistry());
        $clientName = $this->GetName->getNameClient($id);

        return $clientName;
    }

    public function getNameBp($id)
    {
        $this->GetName = new GetNameComponent(new ComponentRegistry());
        $bpName = $this->GetName->getNameBp($id);

        return $bpName;
    }

    public function getNameEngineer($id)
    {
        $this->GetName = new GetNameComponent(new ComponentRegistry());
        $engineerName = $this->GetName->getNameEngineer($id);

        return $engineerName;
    }
}
