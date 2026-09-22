<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateCardCommand extends Command
{
    use NovaPackageCommand;

    protected $name = "amuz-cms:card";

    protected $description = "Create a new card";

    public function handle()
    {

        $card = $this->argument('card');

        $package = $this->argument('package');

        if(!$this->packageExists($package)){
            $this->error("Package doesn't exists!");
            return;
        }

        $template = str_replace([
            '{{card}}',
            '{{namespace}}',
        ],[
            $this->studly($card),
            $this->namespace($package),
        ], $this->stub('Card'));

        $this->createDirIfDoesntExists('src/Nova/Cards', $this->studly($package));
        $this->createDirIfDoesntExists("src/Nova/Cards/{$this->studly($card)}", $this->studly($package));
        $this->createDirIfDoesntExists("src/Nova/Cards/{$this->studly($card)}/js", $this->studly($package));

        $file = $this->dir($this->studly($package))."/src/Nova/Cards/{$this->studly($card)}.php";

        $overwrite = true;

        if(file_exists($file)){
            $overwrite = $this->confirm('File already exists, do you want overwrite it?', false);
        }

        if($overwrite) {

            file_put_contents($file, $template);
            file_put_contents($this->dir($this->studly($package)) . "/src/Nova/Cards/{$this->studly($card)}/js/Card.vue", $this->stub('CardVue'));

            $this->line("Card {$this->studly($card)} has been created!");

        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['card', InputArgument::REQUIRED, 'Card name'],
            ['package', InputArgument::REQUIRED, 'Package name'],
        ];
    }

}
