<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RehashPasswords extends Command
{
    protected $signature = 'rehash:passwords';
    protected $description = 'Convert all passwords to Bcrypt hash';

    // app/Console/Commands/RehashPasswords.php
    public function handle()
    {
        User::chunk(200, function ($users) {
            foreach ($users as $user) {
                if (!preg_match('/^\$2y\$/', $user->password)) {
                    $user->password = \Hash::make($user->password);
                    $user->save();
                }
            }
        });
        $this->info('Passwords rehashed successfully');
    }
}