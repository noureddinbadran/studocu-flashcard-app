<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\ConsoleHelperTrait;
use App\Console\Commands\Traits\TableGenerator;
use App\Services\{AuthService, FlashcardService};
use App\Models\User;
use Illuminate\Console\Command;
use function Symfony\Component\String\match;

class FlashCard extends Command
{
    use ConsoleHelperTrait, TableGenerator;

    public function __construct(private AuthService $authService, private User $user, private FlashcardService $flashcardService)
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

        $option = null;

//        do {
            $this->newLine(4);
            $this->alert("Welcome to Flashcard App - With this App you can learn & practice anytime!");
            $option = $this->choice("Select an option please!", $authMenu);

            match ($option) {
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


                // start the app
                $this->startFlashCard(),
            ],
            };


//        } while ($option !== "Exit");

        return Command::SUCCESS;
    }

    private function startFlashCard()
    {
        // Main menu for flashcard game
        $mainMenu = [
            1 => "Create a flashcard",
            2 => "List all flashcards",
            3 => "Practice",
            4 => "Stats",
            5 => "Reset",
            6 => "Exit",
        ];

        do {
            $this->newLine(2);
            $this->alert("Welcome {" . $this->user->name . "} to Flashcard App <3");
            $option = $this->choice("Please select an option from the list below", $mainMenu);

            match ($option) {
                "Create a flashcard" => [
                    $question = $this->Input("Enter flashcard question"), // Get question
                    $answer = $this->Input("Answer"), // Get answer

                    // create a new flashcard
                    $this->flashcardService->create($question, $answer),
                    ],
                "List all flashcards" => $this->getAllFlashcards(),
            };
        } while ($option !== "Exit");
    }

    private function getAllFlashcards()
    {
        $flashcards = $this->flashcardService->fetch();

        // check if flashcards are empty then restart process
        if (empty($flashcards)) {
            $this->info(
                "No available flashcard"
            );
            return;
        }

        // Display the flashcards in a table format
        $this->generateTable(
            array_keys($flashcards[0]),
            $flashcards,
            "box"
        );
    }



}
