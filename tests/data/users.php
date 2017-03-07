<?php
$users = [];

for ($user_number = 1; $user_number <= 10; $user_number++) {
    $users[] = [
        'name' => sprintf('Test User %s', $user_number),
    ];
}

return $users;
