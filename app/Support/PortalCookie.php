<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\Cookie;

class PortalCookie
{
    /**
     * Issue the HttpOnly portal_token cookie. Secure flag follows the
     * environment so local http:// development still works while
     * production traffic is forced over https://.
     */
    public static function issue(string $token): Cookie
    {
        return cookie(
            'portal_token',
            $token,
            60 * 24, // 1 day
            null,
            null,
            app()->environment('production'), // secure
            true // httpOnly
        );
    }
}
