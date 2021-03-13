<?php


namespace App\Helpers;


class Render
{
    /**
     * Render 'layouts.shared.validationError' for automating
     * showing validation error to user in bootstrap 4 way
     * @param $key
     * @return string
     * @throws \Throwable
     */
    public static function errMsg($key)
    {
        return view('layouts.shared.validationError', ['key' => $key])->render();
    }

    /**
     * return string bootstrap class ('is-invalid') if there are any error
     * in input tags as bootstrap 4 way
     * @param $key
     * @return string
     */
    public static function isInvalid($key): string
    {
        $key = str_replace(['\'', '"'], '', $key);
        $e = session()->get('errors') ?: new \Illuminate\Support\ViewErrorBag;

        return $e->first($key) ? 'is-invalid' : '';
    }

    /**
     * Check current route name with given parameter if it match returns 'active'
     * and 'active' is a bootstrap class for hold current menu active
     *
     * @param mixed ...$routeNames
     * @return string
     */
    public static function navActive(...$routeNames): string
    {
        foreach ($routeNames as $routeName) {
            if (\Route::currentRouteName() === $routeName) return 'active';
        }

        return '';
    }

    /**
     * Check current route name starts given parameter if it match returns 'active'
     * and 'active' is a bootstrap class for hold current menu active
     *
     * @param mixed ...$routeNames
     * @return string
     */
    public static function navTreeActive(...$routeNames): string
    {
        foreach ($routeNames as $routeName) {
            if (self::routeNameStartsWith($routeName)) return 'active';
        }

        return '';
    }

    /**
     * Check current route name starts given parameter if it match returns 'menu-open'
     * and 'menu-open' is a bootstrap class for keep menu open
     *
     * @param mixed ...$routeNames
     * @return string
     */
    public static function navTreeOpen(...$routeNames): string
    {
        foreach ($routeNames as $routeName) {
            if (self::routeNameStartsWith($routeName)) return 'menu-open';
        }

        return '';
    }

    /**
     * Check whether route name starts with given parameter
     *
     * @param $routeName
     * @return mixed
     */
    public static function routeNameStartsWith($routeName)
    {
        return \Str::StartsWith(\Route::currentRouteName(), $routeName);
    }

    public static function badgeRoleColor($role)
    {
        switch ($role['slug']) {
            case('admin'):
                return 'bg-gradient-danger';
                break;
            case('user'):
                return 'bg-gradient-primary';
                break;
            default:
                return 'bg-danger';
        }
    }

    public static function profitOrLoseFormatter($percent, $amount)
    {
        if (!is_numeric($percent)) return $percent;

        if ($percent > 0) {
            $html = '<small class="text-success mr-1">';
            $html .= '<i class="fas fa-arrow-up"></i> ';
            $html .= $percent . '%';
            $html .= '</small>';
            $html .= $amount . ' CAD' ;
            return $html;
        }

        if ($percent < 0) {
            $html = '<small class="text-danger mr-1">';
            $html .= '<i class="fas fa-arrow-down"></i> ';
            $html .= $percent . '%';
            $html .= '</small>';
            $html .= $amount . ' CAD' ;
            return $html;
        }

        if ($percent == 0) {
            $html = '<small class="text-
warning mr-1">';
            $html .= '<i class="fas fa-arrow-right"></i> ';
            $html .= $percent . '%';
            $html .= '</small>';
            $html .= $amount . ' CAD' ;
            return $html;
        }
    }

    public static function pushMenu()
    {
        if (isset($_COOKIE['ui_sc'])) {
            $cookie = $_COOKIE['ui_sc'];
            if ($cookie == 1) {
                return '';
            } elseif ($cookie == 0) {
                return 'sidebar-collapse';
            }
        } else {
            return 'sidebar-collapse';
        }
    }

}
