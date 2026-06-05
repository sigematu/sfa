                        <?php
                            // Determine status from available variables
                            $currentStatus = $status ?? null;
                            if ($currentStatus === null) {
                                if (isset($client)) $currentStatus = $client->status;
                                elseif (isset($clientContact)) $currentStatus = $clientContact->status;
                                elseif (isset($bpContact)) $currentStatus = $bpContact->status;
                                elseif (isset($bp)) $currentStatus = $bp->status;
                                elseif (isset($engineer)) $currentStatus = $engineer->status;
                                elseif (isset($user)) $currentStatus = $user->active;
                            }
                        ?>
                        <?php if ($currentStatus == STATUS_ACTIVE): ?>
                            <span class="badge badge-success"><?= __('Active') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= __('Inactive') ?></span>
                        <?php endif; ?>
