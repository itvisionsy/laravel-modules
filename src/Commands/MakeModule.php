<?php

namespace ItvisionSy\Laravel\Modules\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Filesystem\Filesystem;

class MakeModule extends \Illuminate\Console\Command implements SelfHandling {

    protected $signature = 'make:module
                                {id : the ID of the module. Should be unique across modules}
                                {name : the display name of the module}
                                {--url= : the URL/route-names part for the module}
                                ';
    protected $description = 'Makes a new module';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * Create a new command instance.
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem) {
        parent::__construct();
        $this->fileSystem = $fileSystem;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle() {
        //input
        $id = $this->argument('id');
        $name = $this->argument('name');
        $url = $this->option('url') ?: str_slug($id);

        //subs
        $path = rtrim(config('modules.directory'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
        $className = config('modules.class_name');
        $ds = DIRECTORY_SEPARATOR;

        //stub data
        $stubData = [
            "id" => $id,
            "name" => $name,
            "namespace" => trim(config("modules.namespace"), "\\"),
            "class" => $className,
            "url_name" => $url,
        ];

        $this->makeDirectory($path);
        $this->makeDirectory("{$path}Views");
        $this->makeDirectory("{$path}Migrations");
        $this->makeDirectory("{$path}Models");
        $this->makeDirectory("{$path}Http{$ds}Controllers");
        $this->copyStub("Module.php.stub", "{$path}{$className}.php", $stubData + []);
        $this->copyStub("routes.php.stub", "{$path}Http{$ds}routes.php", $stubData + []);
        $this->copyStub("Controller.php.stub", "{$path}Http{$ds}Controllers{$ds}WelcomeController.php", $stubData + []);
        $this->copyStub("index.blade.php.stub", "{$path}Views{$ds}index.blade.php", $stubData + []);

        $this->info("Module {$id} has been created in {$path}");
    }

    protected function makeDirectory($path, $mode = 0777) {
        if (!$this->fileSystem->isDirectory($path)) {
            $this->fileSystem->makeDirectory($path, $mode, true, true);
        }
    }

    protected function copyStub($stubName, $filePath, array $values) {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'MakeModule' . DIRECTORY_SEPARATOR . $stubName;
        $content = preg_replace_callback("/\{\{([a-zA-Z_\-]+)\}\}/", function ($matches) use ($values) {
            return $values[$matches[1]];
        }, file_get_contents($path));
        if (!$this->fileSystem->exists($filePath)) {
            $this->fileSystem->put($filePath, $content);
        }
    }

}
