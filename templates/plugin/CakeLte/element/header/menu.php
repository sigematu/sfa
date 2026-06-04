<li class="nav-item d-none d-sm-inline-block dropdown">
  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fas fa-bolt mr-1"></i><?= __('Actions') ?></a>
  <div class="dropdown-menu">
    <?= $this->Html->link('<i class="fas fa-envelope-open-text mr-2"></i>' . __('Client Proposal'), '/client-proposals/', ['class' => 'dropdown-item', 'escape' => false]) ?>
  </div>
</li>
<li class="nav-item d-none d-sm-inline-block dropdown">
  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fas fa-building mr-1"></i><?= __('Client') ?></a>
  <div class="dropdown-menu">
    <?= $this->Html->link('<i class="fas fa-building mr-2"></i>' . __('Client'), '/clients/', ['class' => 'dropdown-item', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-address-book mr-2"></i>' . __('Client Contact'), '/client-contacts/', ['class' => 'dropdown-item', 'escape' => false]) ?>
  </div>
</li>
<li class="nav-item d-none d-sm-inline-block dropdown">
  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fas fa-handshake mr-1"></i><?= __('Bp') ?></a>
  <div class="dropdown-menu">
    <?= $this->Html->link('<i class="fas fa-handshake mr-2"></i>' . __('Bp'), '/bps/', ['class' => 'dropdown-item', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-address-card mr-2"></i>' . __('Bp Contact'), '/bp-contacts/', ['class' => 'dropdown-item', 'escape' => false]) ?>
  </div>
</li>
<li class="nav-item d-none d-sm-inline-block dropdown">
  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user-tie mr-1"></i><?= __('Engineer') ?></a>
  <div class="dropdown-menu">
    <?= $this->Html->link('<i class="fas fa-user-tie mr-2"></i>' . __('Engineer'), '/engineers/', ['class' => 'dropdown-item', 'escape' => false]) ?>
  </div>
</li>
<?php
  if ($user['role'] === 'superuser') {
    echo $this->element('header/menu_admin');
  }
?>
