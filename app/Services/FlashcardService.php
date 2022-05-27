<?php

namespace App\Services;

use App\Models\Flashcard;

class FlashcardService extends BaseService
{
   public function __construct(Flashcard $flashcard) {
       $this->model = $flashcard;
   }

    /**
     * Create new flashcard
     *
     * @param string $question
     * @param string $answer
     * @return void
     *
     */
    public function create(string $question, string $answer): void
    {
        $this->model->create([
            'question' => trim($question),
            'answer' => trim($answer)
        ]);
    }

    /**
     * Fetch all flashcards
     *
     * @return array
     */
    public function fetch(): array
    {
        return Flashcard::all("id", "question", "answer")->toArray();
    }
}