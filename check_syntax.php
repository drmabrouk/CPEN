<?php
$files = [
    'control/control.php',
    'control/includes/class-ajax.php',
    'control/includes/class-audit.php',
    'control/includes/class-auth.php',
    'control/includes/class-database.php',
    'control/includes/class-notifications.php',
    'control/includes/class-pwa.php',
    'control/includes/class-shortcode.php',
    'control/includes/class-users.php'
];

foreach ($files as $file) {
    $output = [];
    $return_var = 0;
    exec("php -l $file", $output, $return_var);
    if ($return_var !== 0) {
        echo "Syntax error in $file\n";
        echo implode("\n", $output) . "\n";
    }
}
