<?php

namespace Telegraf\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TelegrafUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}