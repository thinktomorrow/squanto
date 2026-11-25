<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Manager;

use Illuminate\Support\Facades\File;
use Thinktomorrow\SquantoTests\TestCase;

final class ManagerViewsCompileTest extends TestCase
{
    public function test_all_manager_views_compile_without_host_components(): void
    {
        $views = File::allFiles(__DIR__.'/../../../src/Manager/views');

        $this->assertNotEmpty($views);

        foreach ($views as $view) {
            $compiled = app('blade.compiler')->compileString($view->getContents());

            $this->assertIsString($compiled, $view->getRelativePathname());
        }
    }
}
