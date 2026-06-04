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
<?= __d('cake_d_c/users', 'Your password reset request has been accepted.') ?>

<?= __d(
    'cake_d_c/users',
    "Please copy the following address in your web browser {0}",
    $this->Url->build($activationUrl)
) ?>

<?= __d('cake_d_c/users', 'If you do not recognize this email, please discard it.') ?>

