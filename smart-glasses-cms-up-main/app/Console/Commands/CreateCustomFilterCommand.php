<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateCustomFilterCommand extends Command
{
    use NovaPackageCommand;

    protected $name = "amuz-cms:custom-filter";

    protected $description = "Create a new custom filter";

    public function handle()
    {

        $customFilter = $this->argument('custom-filter');

        $package = $this->argument('package');

        if(!$this->packageExists($package)){
            $this->error("Package doesn't exists!");
            return;
        }

        $template = str_replace([
            '{{customFilter}}',
            '{{namespace}}',
            '{{uriKey}}'
        ],[
            $this->studly($customFilter),
            $this->namespace($package),
            Str::kebab($customFilter)
        ], $this->stub('CustomFilter'));

        $this->createDirIfDoesntExists('src/Nova/Filters', $this->studly($package));
        $this->createDirIfDoesntExists("src/Nova/Filters/{$this->studly($customFilter)}", $this->studly($package));
        $this->createDirIfDoesntExists("src/Nova/Filters/{$this->studly($customFilter)}/js", $this->studly($package));

        $file = $this->dir($this->studly($package))."/src/Nova/Filters/{$this->studly($customFilter)}.php";

        $overwrite = true;

        if(file_exists($file)){
            $overwrite = $this->confirm('File already exists, do you want overwrite it?', false);
        }

        if($overwrite) {

            file_put_contents($file, $template);
            file_put_contents($this->dir($this->studly($package)) . "/src/Nova/Filters/{$this->studly($customFilter)}/js/Filter.vue", $this->stub('FilterVue'));

            $this->line("Custom filter {$this->studly($customFilter)} has been created!");
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
            ['custom-filter', InputArgument::REQUIRED, 'Custom filter name'],
            ['package', InputArgument::REQUIRED, 'Package name'],
        ];
    }

}
