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
$this->assign('title', __('Users'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('Users')],
]);
?>
<div class="card card-primary card-outline">
    <div class="card-header d-sm-flex">
        <h2 class="card-title"><?= __('Users') ?></h2>
        <div class="ml-auto">
            <?= $this->Html->link(__('New User'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('username', __d('cake_d_c/users', 'Username')) ?></th>
                <th><?= $this->Paginator->sort('email', __d('cake_d_c/users', 'Email')) ?></th>
                <th><?= $this->Paginator->sort('last_name', __d('cake_d_c/users', 'Last name')) ?></th>
                <th><?= $this->Paginator->sort('first_name', __d('cake_d_c/users', 'First name')) ?></th>
                <th><?= $this->Paginator->sort('display_name', __d('cake_d_c/users', 'Display name')) ?></th>
                <th><?= $this->Paginator->sort('job', __d('cake_d_c/users', 'Job')) ?></th>
                <th><?= $this->Paginator->sort('role', __('Role')) ?></th>
                <th><?= $this->Paginator->sort('active', __d('cake_d_c/users', 'Status')) ?></th>
                <th class="actions"><?= __d('cake_d_c/users', 'Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (${$tableAlias} as $user) : ?>
                <tr>
                    <td><?= $this->Html->link(h($user->username), ['action' => 'view', $user->id]) ?></td>
                    <td><?= h($user->email) ?></td>
                    <td><?= h($user->last_name) ?></td>
                    <td><?= h($user->first_name) ?></td>
                    <td><?= h($user->display_name) ?></td>
                    <td>
                        <?php if ($user->job == JOB_SALES): ?>
                            <span class="badge badge-primary"><?= __('Sales Job') ?></span>
                        <?php elseif ($user->job == JOB_ACCOUNTING): ?>
                            <span class="badge badge-warning"><?= __('Accounting Job') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= __('Non Sales Job') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $roleLabels = ['user' => __('General User'), 'viewer' => __('Viewer'), 'superuser' => __('Admin')];
                        $roleClass = ['superuser' => 'badge badge-danger', 'viewer' => 'badge badge-info'];
                        $label = h($roleLabels[$user->role] ?? $user->role);
                        $class = $roleClass[$user->role] ?? '';
                        echo $class ? "<span class=\"{$class}\">{$label}</span>" : $label;
                        ?>
                    </td>
                    <td><?= $this->element('parts/status_v', ['user' => $user]) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__d('cake_d_c/users', 'Change password'), ['action' => 'changePassword', $user->id], ['class' => 'btn btn-xs btn-outline-primary']) ?>
                        <?= $this->Html->link(__d('cake_d_c/users', 'Edit'), ['action' => 'edit', $user->id], ['class' => 'btn btn-xs btn-outline-primary']) ?>
                        <?= $this->Form->postLink(__d('cake_d_c/users', 'Delete'), ['action' => 'delete', $user->id], ['class' => 'btn btn-xs btn-outline-danger', 'confirm' => __d('cake_d_c/users', 'Are you sure you want to delete # {0}?', $user->id)]) ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
            <ul class="pagination">
                <?= $this->Paginator->prev('< ' . __d('cake_d_c/users', 'previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__d('cake_d_c/users', 'next') . ' >') ?>
            </ul>
            <p><?= $this->Paginator->counter() ?></p>
        </div>
    </div>
</div>
