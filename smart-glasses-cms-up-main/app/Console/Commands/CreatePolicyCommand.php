<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CreatePolicyCommand extends Command
{
    use NovaPackageCommand;

    protected $name = "amuz-cms:policy";

    protected $description = "Create a new policy";

    public function handle()
    {

        $policy = $this->argument('policy');

        $package = $this->argument('package');

        if(!$this->packageExists($package)){
            $this->error("Package doesn't exists!");
            return;
        }

        $template = str_replace([
            '{{policy}}',
            '{{namespace}}',
        ],[
            $this->studly($policy),
            $this->namespace($package),
        ], $this->stub('Policy'));

        $this->createDirIfDoesntExists('src/Policies', $this->studly($package));

        $file = $this->dir($this->studly($package))."/src/Policies/{$this->studly($policy)}.php";

        $overwrite = true;

        if(file_exists($file)){
            $overwrite = $this->confirm('File already exists, do you want overwrite it?', false);
        }

        if($overwrite) {

            file_put_contents($file, $template);

            $this->line("Policy {$this->studly($policy)} has been created!");

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
            ['policy', InputArgument::REQUIRED, 'Policy name'],
            ['package', InputArgument::REQUIRED, 'Package name'],
        ];
    }

}
