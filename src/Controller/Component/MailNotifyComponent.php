<?php 

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Mailer;

class MailNotifyComponent extends Component
{
  public function mailSend($savedData, $controller, $action)
  {
      $session = $this->getController()->getRequest()->getSession();

      $mailer = new Mailer();
      $mailer->setViewVars([
          'id' => $savedData->id ?? null,
          'name' => $savedData->name ?? null,
          'project' => $savedData->project ?? null,
          'displayName' => $session->read('Auth.display_name'),
          'controller' => strtolower($controller),
          'action' => $action
      ]);

      $mailer->setTransport('default')
          ->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME)
          ->setTo(MAIL_TO)
          ->setSubject(MAIL_SUBJECT)
          ->setEmailFormat('text')
          ->viewBuilder()
            ->setTemplate('mailnotify/' . $action)
            ->setLayout('default');

      try {
          $mailer->deliver();
      } catch (\Exception $e) {
          \Cake\Log\Log::error('Email delivery failed: ' . $e->getMessage());
      }
  }
}
