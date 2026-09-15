<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocController extends Controller
{
    public function show(string $page = 'index')
    {
        // Sanitize page parameter to prevent directory traversal
        $page = basename($page);
        $path = base_path('docs/'.$page.'.md');

        if (! File::exists($path)) {
            $path = base_path('docs/index.md');
            $page = 'index';
        }

        $markdown = File::get($path);
        $htmlContent = Str::markdown($markdown);

        // Scan docs directory for navigation links
        $files = File::files(base_path('docs'));
        $navItems = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilenameWithoutExtension();
                $title = Str::title(str_replace(['-', '_'], ' ', $filename));
                if ($filename === 'index') {
                    $title = 'Introduction & Overview';
                }
                $navItems[] = [
                    'slug' => $filename,
                    'title' => $title,
                    'active' => $filename === $page,
                ];
            }
        }

        // Sort nav items so index is first
        usort($navItems, function ($a, $b) {
            if ($a['slug'] === 'index') {
                return -1;
            }
            if ($b['slug'] === 'index') {
                return 1;
            }

            return strcmp($a['title'], $b['title']);
        });

        return view('docs.show', [
            'currentPage' => $page,
            'content' => $htmlContent,
            'navItems' => $navItems,
        ]);
    }
}
