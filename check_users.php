<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$users = \App\Models\User::all();
echo "Total users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
}
echo "\nTry login with:\n";
echo "  admin@qa.com / password\n";
echo "  tester@qa.com / password\n";
echo "  dev@qa.com / password\n";
?>
