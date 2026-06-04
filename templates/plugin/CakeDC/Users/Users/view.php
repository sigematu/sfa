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

$Users = ${$tableAlias};
?>

<?php
$this->assign('title', __('User'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Users'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);
?>

<div class="view card card-primary card-outline">
    <div class="card-header d-sm-flex">
        <h2 class="card-title"><?= h($Users->username) ?></h2>
    </div>
    <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
        <tr>
            <th><?= __d('cake_d_c/users', 'Id') ?></th>
            <td><?= h($Users->id) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Username') ?></th>
            <td><?= h($Users->username) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Email') ?></th>
            <td><?= h($Users->email) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Last Name') ?></th>
            <td><?= h($Users->last_name) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'First Name') ?></th>
            <td><?= h($Users->first_name) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Display Name') ?></th>
            <td><?= h($Users->display_name) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Job') ?></th>
            <td>
                <?php if ($Users->job == JOB_SALES): ?>
                    <span class="badge badge-primary"><?= __('Sales Job') ?></span>
                <?php elseif ($Users->job == JOB_ACCOUNTING): ?>
                    <span class="badge badge-warning"><?= __('Accounting Job') ?></span>
                <?php else: ?>
                    <span class="badge badge-secondary"><?= __('Non Sales Job') ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Role') ?></th>
            <td><?php
                $roleLabels = ['user' => __('General User'), 'viewer' => __('Viewer'), 'superuser' => __('Admin')];
                echo h($roleLabels[$Users->role] ?? $Users->role);
            ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Token') ?></th>
            <td><?= h($Users->token) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Api Token') ?></th>
            <td><?= h($Users->api_token) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Active') ?></th>
            <td>
                <?php if ($Users->active == STATUS_ACTIVE): ?>
                    <?= __('Active') ?>
                <?php else: ?>
                    <div class="text-muted"><?= __('Inactive') ?></div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Token Expires') ?></th>
            <td><?= h($Users->token_expires) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Activation Date') ?></th>
            <td><?= h($Users->activation_date) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Tos Date') ?></th>
            <td><?= h($Users->tos_date) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Created') ?></th>
            <td><?= h($Users->created) ?></td>
        </tr>
        <tr>
            <th><?= __d('cake_d_c/users', 'Modified') ?></th>
            <td><?= h($Users->modified) ?></td>
      </tr>
    </table>
    </div>
    <div class="card-footer d-flex">
        <div class="">
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $Users->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $Users->id), 'class' => 'btn btn-danger']
            ) ?>
        </div>
        <div class="ml-auto">
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $Users->id], ['class' => 'btn btn-secondary']) ?>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>
