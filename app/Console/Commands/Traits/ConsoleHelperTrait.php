<?php

namespace App\Console\Commands\Traits;

trait ConsoleHelperTrait
{
    /**
     * Get any input from user
     *
     * @param  string  $message
     * @return string  $input
     */
    public  function Input(string $message): string
    {
        do {
            $input = trim($this->ask($message));
        } while ($input === "");

        return $input;
    }
}