<?php

if (!function_exists('userRoute')) {
    /**
     * Generate route name based on user's role
     *
     * @param string $routeName
     * @return string
     */
    function userRoute($routeName)
    {
        $user = auth()->user();
        if (!$user) {
            return 'admin.' . $routeName;
        }

        $prefix = match($user->role) {
            'Pka' => 'pka.',
            'Admin' => 'admin.',
            default => 'admin.'
        };

        return $prefix . $routeName;
    }
}
