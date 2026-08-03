<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Menu\MenuCatalogueRepository;

class MenuComposer
{

    protected $menuCatalogueRepository;
    protected $language;
    
    protected static $menuData = [];

    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        $language
    ){
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {

        $dataKey = 'menu_lang_'.$this->language;
        if(!isset(static::$menuData[$dataKey])){
            static::$menuData[$dataKey] = $this->loadMenuData();
        }
        $view->with('menu', static::$menuData[$dataKey]);

    }

    private function loadMenuData(){
        
        $agrument = $this->agrument($this->language);
        $menuCatalogue = $this->menuCatalogueRepository->findByCondition(...$agrument);
        $menus = [];
        $htmlType = ['main-menu'];
         if(count($menuCatalogue)){
            foreach($menuCatalogue as $key => $val){
                $type = (in_array($val->keyword, $htmlType)) ? 'html' : 'array';
                $recursiveMenus = recursive($val->menus);
                
                if($type == 'html'){
                    $menus['mobile'] = $recursiveMenus;
                    
                    $menus[$val->keyword] = frontend_recursive_menu($recursiveMenus, 0, 2, 'html');
                    $menus['mobile-menu-html'] = frontend_recursive_mobile_menu($recursiveMenus, 0, 2);
                    
                    $menus[$val->keyword . '_array'] = frontend_recursive_menu($recursiveMenus, 0, 2, 'array');
                } else {
                    $menus[$val->keyword] = frontend_recursive_menu($recursiveMenus, 0, 2, 'array');
                }
            }
        }
        return $menus;
    }


    private function agrument($language){

        
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [
                'menus' => function($query) use ($language) {
                    $query->orderBy('order', 'desc');
                    $query->with([
                        'languages' => function($query) use ($language){
                            $query->where('language_id', $language);
                        }
                    ]);
                }
            ]
        ];
    }
}