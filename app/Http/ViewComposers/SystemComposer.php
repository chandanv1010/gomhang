<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Core\SystemRepository;

class SystemComposer
{

    protected $language;
    protected $systemRepository;

    /**
     * The composer is registered for `frontend.*`, which matches every view a
     * page renders - layout, header, footer, content and each component - so this
     * ran seven or eight times per request and re-queried the systems table each
     * time. Settings do not change mid-request, so they are memoised per language
     * (the same approach MenuComposer already used).
     */
    protected static $systemData = [];

    public function __construct(
        SystemRepository $systemRepository,
        $language
    ){
        $this->systemRepository = $systemRepository;
        $this->language = $language;
    }

    public static function flushCache(): void
    {
        static::$systemData = [];
    }

    public function compose(View $view)
    {
        $key = 'system_lang_' . $this->language;

        if (!isset(static::$systemData[$key])) {
            $system = $this->systemRepository->findByCondition(
                [
                    ['language_id', '=', $this->language]
                ],
                TRUE
            );
            static::$systemData[$key] = convert_array($system, 'keyword', 'content');
        }

        $view->with('system', static::$systemData[$key]);
    }
}