<?php

namespace Thinktomorrow\Squanto\Manager;

trait ManagesSquanto
{
    public function isSquantoDeveloper()
    {
        return false !== strpos($this->email,'@thinktomorrow.be');
    }
}