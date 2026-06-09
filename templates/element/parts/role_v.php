<?php
    $roles = CLIENT_CONTACT_ROLE_LABELS;

    $roleValue = null;
    if (isset($clientContact)) {
        $roleValue = $clientContact->role;
    } elseif (isset($role)) {
        $roleValue = $role;
    }

    echo isset($roles[$roleValue]) ? $roles[$roleValue] : h((string)$roleValue);
?>