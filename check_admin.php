<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@example.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "Found user: {$user->email}\n";
    echo "Current user_type: [{$user->user_type}]\n";
    
    if ($user->user_type === 'admin') {
        echo "✅ This user IS an admin in the database.\n";
    } else {
        echo "❌ This user is NOT an admin.\n";
        // Force update again just in case
        $user->user_type = 'admin';
        $user->save();
        echo "🔄 Forced update to admin. New status: [{$user->user_type}]\n";
    }
} else {
    echo "❌ User with email {$email} not found.\n";
    // Check first user
    $first = User::first();
    if ($first) {
        echo "First user in DB is: {$first->email} (Type: {$first->user_type})\n";
    }
}
