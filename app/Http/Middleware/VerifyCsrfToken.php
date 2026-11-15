<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'staff/portfolio',
        'staff/portfolio/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        // Detect if uploaded data exceeded PHP post_max_size (which causes PHP to empty $_POST)
        if ($request->isMethod('POST') && empty($request->all()) && empty($request->allFiles())) {
            $contentLength = (int) $request->server('CONTENT_LENGTH', 0);
            if ($contentLength > 0) {
                $postMaxSize = ini_get('post_max_size');
                $sizeMb = round($contentLength / (1024 * 1024), 1);
                $msg = "The uploaded file batch ({$sizeMb}MB) exceeds the server's maximum upload limit ({$postMaxSize}). Please upload fewer or smaller images.";

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $msg, 'errors' => ['files' => [$msg]]], 413);
                }

                return redirect()->back()->with('error', $msg);
            }
        }

        return parent::handle($request, $next);
    }
}
