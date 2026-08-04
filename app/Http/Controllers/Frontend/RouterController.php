<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Core\RouterRepository;

class RouterController extends FrontendController
{
    protected $language;
    protected $routerRepository;
    protected $router;

    public function __construct(
        RouterRepository $routerRepository,
    ) {
        $this->routerRepository = $routerRepository;
        parent::__construct();
    }


    public function index(string $canonical = '', Request $request)
    {
        $this->getRouter($canonical);
        if (is_null($this->router) || empty($this->router)) {
            abort(404);
        }

        // Return the result instead of echoing it, so the markup lands in the
        // response body rather than straight on PHP's output stream.
        return app($this->router->controllers)->index($this->router->module_id, $request);
    }

    public function page(string $canonical = '', $page = 1, Request $request)
    {
        $this->getRouter($canonical);
        $request->merge(['page' => $page]);
        $page = (!isset($page)) ? 1 : $page;
        if (is_null($this->router) || empty($this->router)) {
            abort(404);
        }

        return app($this->router->controllers)->index($this->router->module_id, $request, $page);
    }

    public function getRouter($canonical)
    {
        $this->router = $this->routerRepository->findByCondition(
            [
                ['canonical', '=', $canonical],
                ['language_id', '=', $this->language]
            ]
        );
    }

}