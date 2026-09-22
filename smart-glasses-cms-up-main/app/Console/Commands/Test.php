<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->alert("TEST alert");

        $name = $this->choice(
            'What is your name?',
            ['Taylor', 'Dayle', 'Other'],
            0
        );

        if($name == 'Other'){
            $name = $this->ask("please enter User Name");
        }
        sleep(1);
        $this->info("Good day {$name}, this is TEST info");

        sleep(1);
        $this->comment("Good day {$name}, this is TEST comment");

        sleep(1);
        $this->warn("Good day {$name}, this is TEST Warn");

        sleep(1);
        $this->question("Good day {$name}, this is TEST question");

        sleep(1);
        $this->error("Good day {$name}, this is TEST error");

        sleep(1);
        if ($this->confirm('Do you wish to continue? (try progress Bar)', true)) {
            $this->withProgressBar([1, 2, 3, 4, 5, 6, 7], function ($step, ProgressBar $progressBar) {

                $progressBar->setMessage("this step is {$step}");
                $progressBar->setFormat("%message%\n\n %current%/%max% [%bar%] %percent:3s%");
                sleep(1);

            });
        }
    }
}
