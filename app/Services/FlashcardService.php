<?php

namespace App\Services;

use App\Models\Flashcard;

class FlashcardService extends BaseService
{
    public function __construct(Flashcard $flashcard)
    {
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

    public function fetchStats($user_id): array
    {
        // Get stats from DB for the given user
        $flashcards = Flashcard::leftJoin("flashcard_user", function ($join) use ($user_id) {
            $join->on("flashcards.id", "=", "flashcard_user.flashcard_id");
            $join->where("flashcard_user.user_id", $user_id);
        })
            ->select("flashcards.id", "question", "is_correct")
            ->get()
            ->toArray();
        // $questions = Question::with('answers')->get()->toArray();
        foreach ($flashcards as $key => $flashcard) {
            if ($flashcard["is_correct"] === null) {
                $flashcards[$key]["is_correct"] = "Not answered";
            } else {
                $flashcards[$key]["is_correct"] = $flashcard["is_correct"]
                    ? "Correct"
                    : "Incorrect";
            }
        }

        return $flashcards;
    }

    /**
     * Display flash card with status
     *
     * @param  array $flashcards
     * @return void
     */

    public function showFlashcardsWithStats(array $flashcards): array
    {
        $response = "";
        // print_r($flashcards);
        if (count($flashcards)) {
            $total = count($flashcards);
            $correct = 0;
            $incorrect = 0;

            foreach ($flashcards as $flashcard) {
                if ($flashcard["is_correct"] === "Correct") {
                    $correct++;
                } elseif ($flashcard["is_correct"] === "Incorrect") {
                    $incorrect++;
                }
            }

            $title = "Total: " . $total;
            $title .= ", Correct: " . $correct;
            $title .= ", Inorrect: " . $incorrect;

            $response = [
                'flashcards' => array_keys($flashcards[0]),
                'flashcard' => $flashcards,
                'style' => 'box',
                'title' => $title,
                'message' => round(($correct * 100) / $total, 2) . "% completed."
            ];
        } else {
            $response = "No available flashcards";
        }

        return $response;
    }

    /**
     * Checks if all flashcards is correcy
     *
     * @param  array $flashcards
     * @return bool
     */
    public function isCorrect(array $flashcards): bool
    {
        foreach ($flashcards as $flashcard) {
            if ($flashcard["is_correct"] !== "Correct") {
                return false;
            }
        }

        return true;
    }

    /**
     * get question by id
     *
     * @param  array $flashcards
     * @param  int $flashcardId
     * @return array  $flashcard
     */
    public function fetchFlashcardById(array $flashcards, int $flashcardId): array
    {
        foreach ($flashcards as $flashcard) {
            if ($flashcard["id"] === $flashcardId) {
                return $flashcard;
            }
        }

        return [];
    }

    /**
     * check flashcard answer and update answer status
     *
     * @param  int $questionId
     * @param  string $answer
     * @return bool
     */
    public function validateAnswer(int $flashcard_id, string $answer, $user_id): bool
    {
        $flashcard = Flashcard::find($flashcard_id);
        $is_correct = 0;

        if ($flashcard) {

            $given_answer = strtolower(trim($answer));
            $is_correct = strtolower($flashcard->answer) === $given_answer;

            // update flashcard answer is_correct (true|false)
            $flashcard->users()->syncWithPivotValues([$user_id], ['is_correct' => $is_correct, 'answer' => $given_answer]);
        }

        return $is_correct;
    }

    /**
     * Fetch flashcard entity by Id
     *
     * @param  int $id
     * @return array
     */
    public function fetchById($id): array
    {
        return Flashcard::find($id)->toArray();
    }


    /**
     * Reset all answers of current active user
     *
     * @return void
     */
    public function reset($user): void
    {
        $user->flashcards()->sync([]);
    }

}