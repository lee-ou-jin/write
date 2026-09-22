<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class CreateMigrationCommand extends Command
{
    use NovaPackageCommand;

    protected $name = "amuz-cms:migration";

    protected $description = "Create a new migration";

    public function handle()
    {

        $migration = $this->argument('migration');

        $package = $this->argument('package');

        if(!$this->packageExists($package)){
            $this->error("Package doesn't exists!");
            return;
        }

        $table = Str::snake($migration);
        $template = str_replace(
            ['DummyTable', '{{ table }}', '{{table}}'],
            Str::plural($table), $this->stub('Migration')
        );

        $uniqueFileName = date('Y_m_d')."_".date('his')."_create_".Str::snake($table)."_table";

        $this->createDirIfDoesntExists('src/database/migrations', $this->studly($package));

        file_put_contents($this->dir($this->studly($package))."/src/database/migrations/$uniqueFileName.php", $template);

        $this->line("Migration $uniqueFileName has been created!");

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['migration', InputArgument::REQUIRED, 'Migration name'],
            ['package', InputArgument::REQUIRED, 'Package name'],
        ];
    }

}
