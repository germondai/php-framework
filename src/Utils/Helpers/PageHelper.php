<?php

declare(strict_types=1);

namespace Utils\Helpers;

class PageHelper
{
    private static string $lang = 'en';
    private static string $charset = 'UTF-8';
    private static string $projectName;
    private static string $icon;
    private static string $title;
    private static array $metas = [
        'viewport' => 'width=device-width, initial-scale=1.0'
    ];
    private static array $styles = [];
    private static array $scripts = [];
    private static array $assets = [];


    public static function setLang(string $lang): void
    {
        self::$lang = $lang;
    }

    public static function getLang(): string
    {
        return self::$lang;
    }

    public static function setCharset(string $charset): void
    {
        self::$charset = $charset;
    }

    public static function renderCharset(): void
    {
        echo '<meta charset="' . self::$charset . '">';
    }

    public static function setProjectName(string $projectName): void
    {
        self::$projectName ??= $projectName;
    }

    public static function setTitle(string $title): void
    {
        self::$title ??= $title;
    }

    public static function renderTitle(): void
    {
        $t = self::$title ?? false;
        $pN = self::$projectName ?? false;

        echo
            '<title>' .
            ($t
                ? ($pN
                    ? $t . " | " . $pN
                    : $t
                )
                : ($pN
                    ? $pN
                    : ''
                )
            )
            . '</title>'
        ;
    }

    public static function setIcon(string $icon): void
    {
        self::$icon ??= $icon;
    }

    public static function renderIcon(): void
    {
        echo '<link rel="icon" href="' . self::$icon . '" type="image/x-icon"/>';
    }

    public static function setMetas(array $metas): void
    {
        foreach ($metas as $name => $content) {
            self::$metas[$name] ??= $content;
        }
    }

    public static function renderMetas(): void
    {
        foreach (self::$metas ?? [] as $name => $content) {
            echo '<meta ' . (str_starts_with($name, 'og:') ? 'property' : 'name') . '="' . $name . '" content="' . $content . '">';
        }
    }

    public static function setStyles(array $styles): void
    {
        foreach ($styles as $css) {
            self::$styles[] = Helper::formatLink($css);
        }
    }

    public static function renderStyles(): void
    {
        foreach (self::$styles ?? [] as $css) {
            echo '<link href="' . $css . '" rel="stylesheet">';
        }
    }

    public static function setScripts(array $scripts): void
    {
        foreach ($scripts as $js) {
            self::$scripts[] = Helper::formatLink($js);
        }
    }

    public static function renderScripts(): void
    {
        foreach (self::$scripts ?? [] as $js) {
            echo '<script src="' . $js . '"></script>';
        }
    }

    public static function setAssets(array $assets): void
    {
        $vite = $_ENV['VITE'] ?? 'http://localhost:5173';
        $solved = [];

        $client = $vite . '/@vite/client';
        if (Helper::isDev())
            $assets = [$client, ...$assets];

        $manifest = Helper::getBasePath() . 'public/dist/.vite/manifest.json';
        $fgc = @file_get_contents($manifest);
        $manifest = json_decode($fgc ? $fgc : '', true);

        foreach ($assets as $a) {
            $isV = str_contains($a, $vite);
            $a = $isV ? $a : 'src/assets/' . $a;

            try {
                if (!Helper::isDev())
                    throw new \Exception('You are currently NOT in development mode');

                $viteA = $isV ? $a : $vite . '/' . $a;
                $headers = @get_headers($viteA);
                if (!str_contains($headers[0] ?? '404', '200'))
                    throw new \Exception('Failed to load asset from vite server');
                $solved[$viteA] = $viteA;
            } catch (\Throwable $e) {
                if (!$isV && !empty($manifest[$a])) {
                    $file = $manifest[$a]['file'];
                    $solved[$file] = $file;

                    $imports = $manifest[$a]['imports'] ?? [];
                    if (!empty($imports))
                        foreach ($imports as $i)
                            $solved[$i] = $manifest[$i]['file'];
                }
            }
        }

        foreach ($solved as $a) {
            if (!str_starts_with($a, 'http://') && !str_starts_with($a, 'https://'))
                $a = 'dist/' . $a;

            self::$assets[] = Helper::formatLink($a);
        }
    }

    public static function renderAssets(): void
    {
        foreach (self::$assets as $a) {
            $ext = pathinfo($a, PATHINFO_EXTENSION);

            if (in_array($ext, ['js', 'ts'], true) || str_ends_with($a, '/@vite/client'))
                echo '<script src="' . $a . '" type="module"></script>';
            elseif (in_array($ext, ['css', 'scss'], true))
                echo '<link href="' . $a . '" rel="stylesheet">';
        }
    }
}
