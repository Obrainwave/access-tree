<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Obrainwave\AccessTree\Models\Role;

class CreateAdminUserCommand extends Command
{
    protected $signature = 'accesstree:create-admin 
                            {--name=Admin : Admin user name}
                            {--email=admin@example.com : Admin user email}
                            {--password=password : Admin user password}';
    
    protected $description = 'Create the first admin user for AccessTree';

    public function handle()
    {
        $this->info('Creating admin user...');

        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_root_user' => true, // Make them root user
        ]);

        // Assign admin role if it exists
        $adminRole = Role::where('slug', 'admin_interface')->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole->id);
            $this->info("✓ Admin role assigned");
        }

        $this->info("✓ Admin user created successfully!");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info("Root User: Yes");
        
        $this->warn("Please change the password after first login!");

        return 0;
    }
}
