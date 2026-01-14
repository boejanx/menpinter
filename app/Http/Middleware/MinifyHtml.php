<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya minify HTML biasa (jangan JSON, file, dsb)
        if (
            $response instanceof Response &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            $output = $response->getContent();

            $output = $this->minify($output);

            $response->setContent($output);
        }

        return $response;
    }

    protected function minify(string $html): string
    {
        // Hapus komentar HTML
        $html = preg_replace('/<!--(?!\[if).*?-->/', '', $html);
        // Hapus spasi antar tag dan tab
        $html = preg_replace('/>\s+</', '><', $html);
        // Hapus spasi ekstra
        $html = preg_replace('/\s{2,}/', ' ', $html);

        return trim($html);
    }
}
