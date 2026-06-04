<li class="nav-item d-none d-sm-inline-block">
    <?= $this->Html->link('<i class="fas fa-user-cog mr-1"></i>' . __('User'), '/users/index', ['class' => 'nav-link', 'escape' => false]) ?>
</li>
<li class="nav-item d-none d-sm-inline-block dropdown">
  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fas fa-tools mr-1"></i><?= __('Dev Tools') ?></a>
  <div class="dropdown-menu">
    <?= $this->Html->link('<i class="fas fa-palette mr-2"></i>' . __('Theme'), '/cake_lte/AdminLTE/index.html', ['class' => 'dropdown-item', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-bug mr-2"></i>' . __('Debug'), '/cake_lte/debug', ['class' => 'dropdown-item', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-home mr-2"></i>' . __('Home'), '/', ['class' => 'dropdown-item', 'escape' => false]) ?>
  </div>
</li>
