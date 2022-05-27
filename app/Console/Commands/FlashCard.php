<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\ConsoleHelperTrait;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Console\Command;
use function Symfony\Component\String\match;

class FlashCard extends Command
{
    use ConsoleHelperTrait;

    public function __construct(private AuthService $authService, private User $user)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flashcard:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This app will help you to add some questions with their correct answers, and start to practice them.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Authentication Part
        $authMenu = [
            1 => "Register",
            2 => "Login",
            3 => "Exit",
        ];

        $switch = 'Register';

        do {
            $this->newLine(4);
            $this->alert("Welcome to Flashcard App - With this App you can learn & practice anytime!");
            $switch = $this->choice("Select an option please!", $authMenu);

            match ($switch) {
            'Register' => [
                $name = $this->Input("Enter the username"), // Get username
                $email = $this->Input("Enter the email"), // Get email
                $password = $this->Input("Enter the password"), // Get password
                $this->authService->register([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]),

                $this->alert('User created successfully, login please'),
            ],
            'Login' => [
                $email = $this->Input("Enter the email"), // Get email
                $password = $this->Input("Enter the password"), // Get password
                $this->user = $this->authService->login([
                    'email' => $email,
                    'password' => $password,
                ]),

                $this->alert('Logged successfully!'),

                


            ],
            };


        } while ($switch !== "Exit");

        return Command::SUCCESS;
    }

}
