<?php
$users = [];

for ($user_number = 1; $user_number <= 10; $user_number++) {
    $users[] = [
        'name' => sprintf('Test User %s', $user_number),
        'first_name' => sprintf('Test User First Name %s', $user_number),
        'last_name' => sprintf('Test User Last Name %s', $user_number),
    ];
}

return $users;
