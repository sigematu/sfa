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

use Cake\Core\Configure;

$Users = ${$tableAlias};
$this->assign('title', __('Edit User'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('Users'), 'url' => ['action' => 'index']],
    ['title' => __('Edit User')],
]);
?>
<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create($Users); ?>
    <fieldset>
        <legend><?= __d('cake_d_c/users', 'Edit User') ?></legend>
        <?php
        echo $this->Form->control('username', ['label' => __d('cake_d_c/users', 'Username')]);
        echo $this->Form->control('email', ['label' => __d('cake_d_c/users', 'Email')]);
        echo $this->Form->control('first_name', ['label' => __d('cake_d_c/users', 'First name')]);
        echo $this->Form->control('last_name', ['label' => __d('cake_d_c/users', 'Last name')]);
        echo $this->Form->control('display_name', ['label' => __d('cake_d_c/users', 'Display name')]);
        $jobs = [JOB_SALES => __('Sales Job'), JOB_NON_SALES => __('Non Sales Job'), JOB_ACCOUNTING => __('Accounting Job')];
        echo $this->Form->control('job', ['type' => 'select', 'label' => __d('cake_d_c/users', 'Job'), 'options' => $jobs]);
        $roles = ['user' => __('General User'), 'viewer' => __('Viewer'), 'superuser' => __('Admin')];
        echo $this->Form->control('role', ['type' => 'select', 'label' => __d('cake_d_c/users', 'Role'), 'options' => $roles]);
        echo $this->Form->control('token', ['label' => __d('cake_d_c/users', 'Token')]);
        echo $this->Form->control('token_expires', [
            'label' => __d('cake_d_c/users', 'Token expires')
        ]);
        echo $this->Form->control('api_token', [
            'label' => __d('cake_d_c/users', 'API token')
        ]);
        echo $this->Form->control('activation_date', [
            'label' => __d('cake_d_c/users', 'Activation date')
        ]);
        echo $this->Form->control('tos_date', [
            'label' => __d('cake_d_c/users', 'TOS date')
        ]);
        echo $this->Form->control('active', [
            'label' => __d('cake_d_c/users', 'Active')
        ]);
        ?>
    </fieldset>
    <?= $this->Form->button(__d('cake_d_c/users', 'Submit')) ?>
    <?= $this->Form->end() ?>
    <?php if (Configure::read('OneTimePasswordAuthenticator.login')) : ?>
        <fieldset>
            <legend>Reset Google Authenticator</legend>
            <?= $this->Form->postLink(
                __d('cake_d_c/users', 'Reset Google Authenticator Token'), [
                'plugin' => 'CakeDC/Users',
                'controller' => 'Users',
                'action' => 'resetOneTimePasswordAuthenticator', $Users->id
            ], [
                'class' => 'btn btn-danger',
                'confirm' => __d(
                    'cake_d_c/users',
                    'Are you sure you want to reset token for user "{0}"?', $Users->username
                )
            ]);
            ?>
        </fieldset>
    <?php endif; ?>
</div>
