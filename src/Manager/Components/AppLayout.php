<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public function render()
    {
        return view('squanto::components.app-layout');
    }
}
