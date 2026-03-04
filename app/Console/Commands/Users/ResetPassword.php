<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Hash;
use Illuminate\Console\Command;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password for given user.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return static::FAILURE;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error('User not found.');
            return static::FAILURE;
        }

        if (!$this->confirm("Are you sure you want to reset password for user with email: $email?")) {
            return static::FAILURE;
        }


        $passwordConfirmation = $this->ask('Confirm new password');

        if ($passwordConfirmation !== $password) {
            $this->error('Password does not match. Retry.');
        }

        if ($user->update(['password' => Hash::make($password)])) {
            $this->info('Password reset successful.');
            return static::SUCCESS;
        }
        
        $this->error('Something went wrong.');
        return static::FAILURE;

    }
}
