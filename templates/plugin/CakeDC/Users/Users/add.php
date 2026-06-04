<?php
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2018, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php
$this->assign('title', __('Add User'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('Users'), 'url' => ['action' => 'index']],
    ['title' => __('Add User')],
]);
?>
<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create(${$tableAlias}); ?>
    <fieldset>
        <legend><?= __d('cake_d_c/users', 'Add User') ?></legend>
        <?php
            echo $this->Form->control('username', ['label' => __d('cake_d_c/users', 'Username')]);
            echo $this->Form->control('email', ['label' => __d('cake_d_c/users', 'Email')]);
            echo $this->Form->control('password', ['label' => __d('cake_d_c/users', 'Password')]);
            echo $this->Form->control('last_name', ['label' => __d('cake_d_c/users', 'Last name')]);
            echo $this->Form->control('first_name', ['label' => __d('cake_d_c/users', 'First name')]);
            echo $this->Form->control('display_name', ['label' => __d('cake_d_c/users', 'Display name')]);
            $jobs = [JOB_SALES => __('Sales Job'), JOB_NON_SALES => __('Non Sales Job'), JOB_ACCOUNTING => __('Accounting Job')];
            echo $this->Form->control('job', ['type' => 'select', 'label' => __d('cake_d_c/users', 'Job'), 'options' => $jobs, 'empty' => true]);
            $roles = ['user' => __('General User'), 'viewer' => __('Viewer'), 'superuser' => __('Admin')];
            echo $this->Form->control('role', ['type' => 'select', 'label' => __d('cake_d_c/users', 'Role'), 'options' => $roles]);
            echo $this->Form->control('active', [
                'type' => 'checkbox',
                'label' => __d('cake_d_c/users', 'Active')
            ]);
        ?>
    </fieldset>
    <?= $this->Form->button(__d('cake_d_c/users', 'Submit')) ?>
    <?= $this->Form->end() ?>
</div>
