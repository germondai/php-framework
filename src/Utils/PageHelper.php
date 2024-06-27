<?php

declare(strict_types=1);

namespace Utils;

use Utils\Helper;

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
    private static array $styles;
    private static array $scripts;


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
}
