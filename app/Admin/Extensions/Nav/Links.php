<?php

namespace App\Admin\Extensions\Nav;

class Links
{
    public function __toString()
    {
        $appUrl = config('app.url');
        return <<<HTML

            <a class="btn btn-primary" href="{$appUrl}" target="_blank" role="button">
                <span>CZM Public Area</span>
                <i class="icon-hand-pointer"></i>
            </a>


        HTML;
    }
}
