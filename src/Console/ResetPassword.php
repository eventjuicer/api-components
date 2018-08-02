<?php

namespace Eventjuicer\Console;

use Illuminate\Console\Command;

use Eventjuicer\Models\User;


class ResetPassword extends Command
{

    protected $user;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:password {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        parent::__construct();

        $this->user = $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $email          = $this->argument('email');
        $password       = $this->argument("password");

        $user           = $this->user->where("email", $email)->firstOrFail(); 

        $password = strlen($password) > 5 ? $password : str_random(10);
       
        $user->password = bcrypt($password);
        $user->save();

        $this->info("Password set to: " . $password);

    }
}