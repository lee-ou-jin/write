<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CreateModelCommand extends Command
{
    use NovaPackageCommand;

    protected $name = "amuz-cms:model";

    protected $description = "Create a new model";

    public function handle()
    {

        $model = $this->argument('model');

        $package = $this->argument('package');

        if(!$this->packageExists($package)){
            $this->error("Package doesn't exists!");
            return;
        }

        $template = str_replace([
            '{{model}}',
            '{{namespace}}',
        ],[
            $this->studly($model),
            $this->namespace($package),
        ], $this->stub('Model'));

        $this->createDirIfDoesntExists('src/Models', $this->studly($package));

        $file = $this->dir($this->studly($package))."/src/Models/{$this->studly($model)}.php";

        $overwrite = true;

        if(file_exists($file)){
            $overwrite = $this->confirm('File already exists, do you want overwrite it?', false);
        }

        if($overwrite) {

            file_put_contents($file, $template);

            $this->line("Model {$this->studly($model)} has been created!");

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
            ['model', InputArgument::REQUIRED, 'Model name'],
            ['package', InputArgument::REQUIRED, 'Package name'],
        ];
    }

}
