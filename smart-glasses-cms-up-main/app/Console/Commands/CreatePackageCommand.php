<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class CreatePackageCommand extends Command
{
    use NovaPackageCommand;

    protected string $vendor;
    protected string $package;
    protected $name = "amuz-cms:package";
    protected $description = "Create new Package";

    public function handle()
    {
        $this->package = Str::kebab($this->ask('Enter Package Name'));
        $this->vendor = Str::kebab($this->anticipate('What is your name?', ['AmuzCorp']));

        $this->generatePackageDirectories();
        $this->generateRouteFile();
        $this->generateAssets();
        $this->generateComposer();
        $this->generateConfigFile();

        //make service provider
        Artisan::call('amuz-cms:service-provider', [
            'package' => $this->package,
            '--main' => true
        ]);


        $template = str_replace([
            '{{vendor}}',
            '{{package}}',
            '{{studly}}'
        ],[
            $this->vendor,
            $this->package,
            $this->studly($this->package),
        ], $this->stub('README'));
        file_put_contents($this->dir($this->studly($this->package))."/README.md", $template);

        $this->alert('Complete Package Create');
        $this->warn('생성된 패키지는 아래 명령을 통해 Git 저장소를 등록해야합니다.');
        $this->comment("https://github.com/organizations/amuzcorp/repositories/new");
        $this->comment("위 주소로 접속하여 {$this->package} 이름으로 새 저장소를 생성합니다.");
        $this->comment('생성이 완료되면 아래 명령을 통해 저장소와 연결할 수 있습니다.');
        $this->info("cd amuz-packages/{$this->package} && git init && git add . && git commit -m 'first commit' && git branch -M main && git remote add origin https://github.com/amuzcorp/{$this->package}.git && git push -u origin main && cd ../../");
        $this->comment("이후에는 패키지에 포함된 README.md를 참고하여 적절히 활용할 수 있습니다.");
    }

    /**
     * Create package directories
     * @return void
     */
    protected function generatePackageDirectories(): void
    {
        if(!is_dir($this->dir())){
            mkdir($this->dir());
        }

        mkdir($this->dir($this->studly($this->package)));

        $directories = [
            'src' => false,
            'src/Console' => false,
            'src/Console/Commands' => true,
            'src/Exceptions' => true,
            'src/Http' => false,
            'src/Http/Controllers' => true,
            'src/Http/Middleware' => true,
            'src/Nova' => false,
            'src/Nova/Actions' => true,
            'src/Nova/Fields' => true,
            'src/Nova/Filters' => true,
            'src/Nova/Cards' => true,
            'src/Nova/Resources' => true,
            'src/Nova/ResourceTools' => true,
            'src/Nova/Tools' => true,
            'src/Models' => true,
            'src/config' => false,
            'src/database' => false,
            'src/database/migrations' => true,
            'src/database/factories' => true,
            'src/database/seeders' => true,
            'src/resources' => false,
            'src/resources/assets' => false,
            'src/resources/assets/css' => true,
            'src/resources/assets/js' => true,
            'src/resources/lang' => true,
            'src/resources/views' => true,
            'src/routes' => true,
        ];

        foreach ($directories as $directory => $keep){
            $this->createDirIfDoesntExists($directory, $this->studly($this->package));
            if($keep) file_put_contents($this->dir($this->studly($this->package))."/" . $directory . "/.gitkeep", $this->stub('GitKeep'));
        }

        $this->line('Directories have been created');
    }

    /**
     * Generate api routes file
     * @return void
     */
    protected function generateRouteFile()
    {

        $uriKey = Str::kebab($this->package);

        $template = str_replace([
            '{{uriKey}}'
        ],[
            $uriKey
        ], $this->stub('ApiRoutes'));

        file_put_contents($this->dir($this->studly($this->package))."/src/routes/api.php", $template);

        $this->line("Api routes file has been created!");

    }

    /**
     * Generate composer file
     * @return void
     */
    protected function generateComposer()
    {
        $name = Str::kebab($this->package);
        $vendor = $this->vendor;

        $template = str_replace([
            '{{package}}',
            '{{studly}}',
            '{{serviceProvider}}',
            '{{vendor}}'
        ],[
            $name,
            $this->studly($name),
            "AmuzPackages\\\\{$this->studly($name)}\\\\{$this->studly($name)}ServiceProvider",
            $vendor
        ], $this->stub('Composer'));

        file_put_contents($this->dir($this->studly($name))."/composer.json", $template);

        $this->line("Composer has been created!");
    }

    /**
     * Generate assets files
     * @return void
     */
    protected function generateAssets(): void
    {
        $name = $this->package;
        $vendor = $this->vendor;

        $template = str_replace([
            '{{uriKey}}',
            '{{vendor}}'
        ], [
            Str::kebab($name),
            Str::kebab($vendor)
        ], $this->stub('Mix'));

        $uriKey = Str::kebab($name);

        file_put_contents($this->dir($this->studly($name))."/src/Nova/$uriKey.js", $this->stub('Js'));
        file_put_contents($this->dir($this->studly($name))."/src/Nova/$uriKey.scss", $this->stub('Css'));
        file_put_contents($this->dir($this->studly($name)) . "/webpack.mix.js", $template);
        file_put_contents($this->dir($this->studly($name)) . "/nova.mix.js", str_replace([
            '{{vendor}}'
        ], [
            "$vendor/$uriKey"
        ], $this->stub('NovaMix')));

        file_put_contents($this->dir($this->studly($name)) . "/package.json", $this->stub('Package'));
        file_put_contents($this->dir($this->studly($name)) . "/postcss.config.js", $this->stub('PostCss'));
        file_put_contents($this->dir($this->studly($name))."/.gitignore", $this->stub('Git'));

        $this->line("Assets have been created!");
    }


    protected function generateConfigFile()
    {

        $uriKey = Str::kebab($this->package);

        $template = str_replace([
            '{{uriKey}}'
        ],[
            $uriKey
        ], $this->stub('Config'));

        file_put_contents($this->dir($this->studly($this->package))."/src/config/".$uriKey.".php", $template);

        $this->line("config file generated");
    }
}
