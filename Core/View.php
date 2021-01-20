<?php


namespace Core;


class View
{
    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    /**
     * Get the content of view template using Twig
     *
     * @param $template
     * @param array $args
     * @return string
     */

    public static function getTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig =  new \Twig\Environment($loader);
            $twig->addGlobal('is_logged_in', \App\Auth::isLoggedIn());
        }

        return $twig->render($template, $args);
    }

}