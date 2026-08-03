<?php  
namespace App\Traits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait HasGenerate {

    public function getStub(string $filePath = ''){
        return File::get(resource_path("stub/{$filePath}.stub"));
    }

    private function put($destination, $content){
        if(!File::exists($destination)){
            File::put("{$destination}", $content);
        }
    }

    private function createContent($content, ?array $extends = []){
        $newContent = str_replace(
            [
                '{{namespace}}',
                '{{module}}',
                ...(isset($extends[0]) ? $extends[0] : [])
            ],
            [
                $this->namespace,
                $this->module,
                ...(isset($extends[1]) ? $extends[1] : [])
            ],
            $content
        );
        return $newContent;
    }

    private function generateController(string $stubPath = ''): self {
        $stub = $this->getStub($stubPath);
        $target = app_path("Http/Controllers/Backend/V2/{$this->namespace}");
        $destination = "{$target}/{$this->module}Controller.php";
        File::ensureDirectoryExists($target); 
        $snakeModule = Str::snake($this->module);
        $camelModule = Str::camel($this->module);
        $snakeNameSpace = Str::snake($this->namespace);
        
        
        $extends = [
            [
                '{{camelModule}}',
                '{{snake_module}}',
                '{{snake_namespace}}',
                '{{moduleName}}',
                '{{module_name}}',
            ],
            [
                $camelModule,
                $snakeModule,
                $snakeNameSpace,
                $this->moduleName,
                $this->moduleName,
            ]
        ];
            
        $content = $this->createContent($stub, $extends);
        // dd($content);
        $this->put($destination, $content);
        return $this;
    }

   protected function generateRepository(): self {
        $stub = $this->getStub('repository');
        $target = app_path("Repositories/{$this->namespace}");
        $destination = "{$target}/{$this->module}Repo.php";
        File::ensureDirectoryExists($target);
        $content = $this->createContent($stub);
        $this->put($destination, $content);
        return $this;
    }

    protected function insertPermission(): static {
        try {
            DB::beginTransaction();
            $snakeModule = Str::snake($this->module);
            $displayName = $this->moduleName ?: $snakeModule;


            $permissions = [
                [
                    'name' => "Xem danh sách {$displayName}",
                    'canonical' => "{$snakeModule}.index"
                ],
                [
                    'name' => "Tạo mới {$displayName}",
                    'canonical' => "{$snakeModule}.create"
                ],
                [
                    'name' => "Cập nhật {$displayName}",
                    'canonical' => "{$snakeModule}.update"
                ],
                [
                    'name' => "Xóa {$displayName}",
                    'canonical' => "{$snakeModule}.destroy"
                ],
                [
                    'name' => "Xem tất cả {$displayName}",
                    'canonical' => "{$snakeModule}.all"
                ]
            ];

            $insertData = [];
            $now = now();
            foreach($permissions as $permission){
                $insertData[] = [
                    'name' => $permission['name'],
                    'canonical' => $permission['canonical'],
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            DB::table('permissions')->insertOrIgnore($insertData);
            DB::commit();
        } catch (\Throwable $th) {
            $this->error("❌ Error creating permissions: " . $th->getMessage());
            DB::rollBack();
        }

        return $this;
    }

}