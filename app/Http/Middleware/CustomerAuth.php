<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /** Session key carrying the reason for the redirect to the login page. */
    public const NOTICE_KEY = 'customer_auth_notice';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('customer')->check()) {
            return $next($request);
        }

        // An ajax caller wants a status it can act on, not an HTML login page.
        if ($request->expectsJson()) {
            return response()->json([
                'code' => 401,
                'messages' => 'Vui lòng đăng nhập để tiếp tục',
                'redirect' => route('customer.login'),
            ], 401);
        }

        // Remember where they were headed so login can send them back, instead of
        // dumping them on the homepage - someone who clicked "giỏ hàng" should
        // land in the cart after signing in, not have to find it again.
        if ($request->isMethod('GET')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        // Deliberately a plain put(), not flash()/->with(). Flash data written from
        // this middleware does not survive to the next request in this app: the
        // bookkeeping keeps the key (_flash.old contains it) while the value is
        // dropped, so the notice never reached the login page. A normal session
        // key does survive, and AuthController::index() pull()s it so it is still
        // shown exactly once.
        $request->session()->put(self::NOTICE_KEY, 'Vui lòng đăng nhập để truy cập trang này');

        return redirect()->route('customer.login');
    }
}
