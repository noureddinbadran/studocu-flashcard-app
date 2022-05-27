<?php

namespace App\Console\Commands\Traits;

use Symfony\Component\Console\Helper\Table;

trait TableGenerator
{



    /**
     * Show table console traits
     *
     * @param  array  $headers
     * @param  array  $rows
     * @param  string $style
     * @param  string $footer
     * @return void
     */
    public function generateTable(
        array $headers,
        array $rows,
        string $style = "default",
        string $footer = null
    ): void {
        $table = new Table($this->output);

        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->setFooterTitle($footer)
            ->setStyle($style);

        $table->render();
    }
}
