<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Core\LanguageRepository;

class LanguageComposer
{

    protected $language;
    protected $languageRepository;

    /**
     * Same reason as SystemComposer: registered for `frontend.*`, so it ran once
     * per rendered view and re-queried the languages table each time. The list of
     * published languages is identical for every view in a request.
     */
    protected static $languageList = null;

    public function __construct(
        LanguageRepository $languageRepository,
        $language
    ){
        $this->languageRepository = $languageRepository;
    }

    public static function flushCache(): void
    {
        static::$languageList = null;
    }

    public function compose(View $view)
    {
        if (is_null(static::$languageList)) {
            static::$languageList = $this->languageRepository->findByCondition(...$this->agrument());
        }

        $view->with('languages', static::$languageList);
    }

    private function agrument(){
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [],
            'orderBy' => ['current', 'desc']
        ];
    }

}