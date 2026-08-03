<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Core\SystemRepository;

class SystemComposer
{

    protected $language;
    protected $systemRepository;

    public function __construct(
        SystemRepository $systemRepository,
        $language
    ){
        $this->systemRepository = $systemRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $system = $this->systemRepository->findByCondition(
            [
                ['language_id', '=', $this->language]
            ],
            TRUE
        );
        $systemArray = convert_array($system, 'keyword', 'content');
        $view->with('system', $systemArray);
    }
}