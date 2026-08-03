<?php  
namespace App\Traits;
use Illuminate\Http\Request;

trait HasQueryBuilder{

    public function build(){

    }

    private function buildFilter(Request $request, array $filter = []): array{
        $conditions = [];
        if(count($filter)){
            foreach($filter as $key => $val){
                if($request->has($val)){
                    $conditions[$val] = $request->{$val};
                }
            }
        }
        return $conditions;
    }

    protected function specifications(Request $request): array {
        return [
            'type' => $request->input('type') === 'all',
            'sort' => $request->filled('sort')
                ? explode(',', $request->input('sort'))
                : $this->defaultSort,
            'perpage' => $this->perpage ?? 20,
            'take' => $request->input('take') ?? null,
            'with' => $this->with,
            'filter' => [
                'keyword' => [
                    'q' => $request->input('keyword') ?? null,
                    'searchFields' => $this->searchField ?? []
                ],
                'simple' => $this->buildFilter($request, $this->simpleFilters), 
                'complex' => $this->buildFilter($request, $this->complexFilters)
            ]
        ];
    }


}