<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\HasGenerate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCommonModule extends Command
{

    use HasGenerate;

    private $module;
    private $namespace;
    private $moduleName;
    private $table;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {module} {--namespace=:} {--table=:} {--moduleName=: The name of module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    private function setModule($module): static{
        $this->module = $module;
        return $this;
    }

    private function setNamespace($namespace): static {
        $this->namespace = $namespace;
        return $this;
    }

    private function setModuleName($name): static {
        $this->moduleName = $name;
        return $this;
    }

    private function setTable($table): static {
        $this->table = $table;
        return $this;
    }

    public function handle()
    {
        try {
            $this->setModule($this->argument('module'))
            ->setNamespace($this->option('namespace'))
            ->setModuleName($this->option('moduleName'))
            ->setTable($this->option('table'))
            ->generateController('common/controller')
            ->generateRequest()
            ->generateModel()
            ->generateService()
            ->generateRepository()
            ->generateView()
            ->generateMigration()
            ->insertPermission();

            $this->line("🎉 Đã tạo thành công các file cho module: {$this->module}");
            $this->line("📁 Namespace: {$this->namespace}");

        } catch (\Throwable $th) {
            $this->error("❌ Có lỗi xảy ra: " . $th->getMessage());
            return Command::FAILURE; 
        }
    }

    protected function generateRequest(): static{

        $target = app_path("Http/Requests/{$this->namespace}/{$this->module}");
        $table = Str::snake($this->module) . 's'; 
        File::ensureDirectoryExists($target);
        $stubs = [
            'StoreRequest' => 'store-request',
            'UpdateRequest' => 'update-request',
        ];

        foreach($stubs as $key => $filename){
            $stub = $this->getStub("common/$filename");
            $destination = "{$target}/{$key}.php";
            $content = $this->createContent($stub);
            $this->put($destination, $content);
        }


        return $this;
    }

    protected function generateModel(): static {
        $filename =  'common/model';
        $stub = $this->getStub($filename);
        $target = app_path("Models");
        $destination = "{$target}/{$this->module}.php";
        File::ensureDirectoryExists($target);
        
        $snakeModule = Str::snake($this->module);
        $content = $this->createContent($stub);
        $this->put($destination, $content);
        return $this;
    }

    protected function generateService(): static {
        $filename =  'common/service';
        $stub = $this->getStub($filename);
        $target = app_path("Services/V2/Impl/{$this->namespace}");
        $destination = "{$target}/{$this->module}Service.php";
        File::ensureDirectoryExists($target);
        $snakeModule = Str::snake($this->module);
        $content = $this->createContent($stub);
        $this->put($destination, $content);
        return $this;
    }

    protected function generateView(): static{
        $snakeModule = Str::snake($this->module);
        $camelModule = Str::camel($this->module);
        $snakeNamespace = Str::snake($this->namespace);
        // $target = app_path("Http/Requests/{$this->namespace}/{$this->module}");
        $target = resource_path("views/backend/$snakeNamespace/$snakeModule");
        $table = Str::snake($this->module) . 's'; 
        File::ensureDirectoryExists($target);
        $stubs = [
            'store', 'delete', 'index'
        ];
        $extends = [
            [
                '{{camel_module}}',
                '{{snake_module}}',
                '{{snake_namespace}}'
            ],
            [
                $camelModule,
                $snakeModule,
                $snakeNamespace
            ]
        ];

        foreach($stubs as $key => $filename){
            $stub = $this->getStub("common/view/$filename");
            $destination = "{$target}/{$filename}.blade.php";
            $content = $this->createContent($stub, $extends);
            $this->put($destination, $content);
        }

        return $this;

    }

    protected function generateMigration(): static{
        $filename = 'common/migration';
        $stub = $this->getStub($filename);
        $timestamp = date('Y_m_d_His');
        $migrationName = "create_{$this->table}_table";
        $migrationFileName = "{$timestamp}_{$migrationName}.php";
        $target = database_path('migrations');
        $destination = "{$target}/{$migrationFileName}";
        File::ensureDirectoryExists($target);
        $extends = [
            [
                '{{table}}',
            ],
            [
                $this->table,
            ]
        ];
        $content = $this->createContent($stub, $extends);
        $this->put($destination, $content);
        return $this;
    }

    
}
