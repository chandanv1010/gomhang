<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\HasGenerate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateModule extends Command
{

    use HasGenerate;

    private $module;
    private $namespace;
    private $fields;
    private $moduleName;
    private $tag;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate {module} {--namespace=: The namespace for the CRUD structure} {--tag=} {--moduleName=: The name of module}  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    protected function setModule($module): static{
        $this->module = $module;
        return $this;
    }

    protected function setNamespace($namespace): static{
        $this->namespace = $namespace;
        return $this;
    }

    protected function setTag($tag): static{
        $this->tag = $tag;
        return $this;
    }

    protected function setModuleName($moduleName): static {
        $this->moduleName = $moduleName;
        return $this;
    }

    public function handle()
    {
        
        try {
            $this->setModule($this->argument('module'))
            ->setNamespace($this->option('namespace'))
            ->setTag($this->option('tag'))
            ->setModuleName($this->option('moduleName'))
            ->generateController("multiple/controller")
            ->generateRequest()
            ->generateRepository()
            ->generateModel()
            ->generateService()
            ->generateView()
            ->insertPermission();
            $this->line("🎉 Đã tạo thành công các file cho module: {$this->module}");
            $this->line("📁 Namespace: {$this->namespace}");
            $this->line("🏷️  Tag: {$this->tag}");
        
            return Command::SUCCESS; 
        } catch (\Throwable $th) {
            $this->error("❌ Có lỗi xảy ra: " . $th->getMessage());
            return Command::FAILURE; 
        }
    }

    
 
    protected function generateModel(): static {
        $filepath = 'multiple';
        $filename = $filepath . '/' . 'model';
        if($this->tag === 'catalogue'){
            $filename = $filepath . '/' . 'model-catalogue';
        }
        $stub = $this->getStub($filename);
        $target = app_path("Models");
        $destination = "{$target}/{$this->module}.php";
        File::ensureDirectoryExists($target);
        
        $snakeModule = Str::snake($this->module);
        $extends = [
            [
                '{{snake_module}}',
            ],
            [
                $snakeModule,
            ]
        ];
        $content = $this->createContent($stub, $extends);

        $this->put($destination, $content);
        return $this;
    }

    protected function generateService(): static {
        $filepath = 'multiple';
        $filename = $filepath. '/' . 'service';
        if($this->tag === 'catalogue'){
            $filename = $filepath . '/' . 'service-catalogue';
        }
        $stub = $this->getStub($filename);
        $target = app_path("Services/V2/Impl/{$this->namespace}");
        $destination = "{$target}/{$this->module}Service.php";
        File::ensureDirectoryExists($target);
        $snakeModule = Str::snake($this->module);
        $extends = [
            [
                '{{snake_module}}',
            ],
            [
                $snakeModule,
            ]
        ];
        $content = $this->createContent($stub, $extends);
        $this->put($destination, $content);
        return $this;
    }

    protected function generateRequest(): static{

        $target = app_path("Http/Requests/{$this->namespace}/{$this->module}");
        $table = Str::snake($this->module) . 's'; 
        File::ensureDirectoryExists($target);
        $stubs = [
            'StoreRequest' => 'store-request',
            'UpdateRequest' => 'update-request',
        ];
        $extends = [
            [
                '{{table}}',
            ],
            [
                $table
            ]
        ];

        foreach($stubs as $key => $filename){
            $stub = $this->getStub("multiple/$filename");
            $destination = "{$target}/{$key}.php";
            $content = $this->createContent($stub, $extends);
            $this->put($destination, $content);
        }


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
            'index', 'delete' 
        ];
        $extends = [
            [
                '{{camel_module}}',
                '{{snake_module}}',
            ],
            [
                $camelModule,
                $snakeModule
            ]
        ];

        foreach($stubs as $key => $filename){
            $stub = $this->getStub("multiple/view/$filename");
            $destination = "{$target}/{$filename}.blade.php";
            
            $content = $this->createContent($stub, $extends);
            $this->put($destination, $content);
        }

        $filepath = 'multiple/view';
        $filename = $filepath . '/' . 'store';
        if($this->tag === 'catalogue'){
            $filename = $filepath . '/' . 'store-catalogue';
        }
        $stub = $this->getStub($filename);
        $destination =  "{$target}/store.blade.php";
        $content = $this->createContent($stub, $extends);
        $this->put($destination, $content);

        return $this;

    }

}
