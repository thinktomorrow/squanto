<?php

namespace Thinktomorrow\Squanto\Manager\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Manager\Pages\LineViewModel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Thinktomorrow\Squanto\Manager\Pages\PagesRepository;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Database\Application\UpdateDatabaseLine;

class ManagerController extends Controller
{
    use ValidatesRequests;

    /**
     * @var PagesRepository
     */
    private PagesRepository $pagesRepository;

    /**
     * @var DatabaseLinesRepository
     */
    private DatabaseLinesRepository $databaseLinesRepository;

    /**
     * @var UpdateDatabaseLine
     */
    private UpdateDatabaseLine $updateDatabaseLine;

    /**
     * @var CacheDatabaseLines
     */
    private CacheDatabaseLines $cacheDatabaseLines;

    public function __construct(PagesRepository $pagesRepository, DatabaseLinesRepository $databaseLinesRepository, UpdateDatabaseLine $updateDatabaseLine, CacheDatabaseLines $cacheDatabaseLines)
    {
        $this->pagesRepository = $pagesRepository;
        $this->databaseLinesRepository = $databaseLinesRepository;
        $this->updateDatabaseLine = $updateDatabaseLine;
        $this->cacheDatabaseLines = $cacheDatabaseLines;
    }

    public function index(Request $request)
    {
        $pages = $this->pagesRepository->all();

        return view(
            'squanto::index', [
                'pages' => $pages->all(),
            ]
        );
    }

    public function edit($pageSlug)
    {
        $lines = $this->databaseLinesRepository->modelsStartingWith($pageSlug);

        $viewModels = [];
        $lines->each(function ($lineModel) use (&$viewModels) {
            $viewModels[] = new LineViewModel($lineModel);
        });

        return view('squanto::edit', [
            'locales' => config('squanto.locales'),
            'lines'   => $viewModels,
            'page'    => $this->pagesRepository->findBySlug($pageSlug),
        ]);
    }

    public function update(Request $request, $pageSlug)
    {
        $request = $this->convertNullToEmptyString($request);

        foreach ($request->input('squanto', []) as $key => $values) {
            $this->updateDatabaseLine->handle(Line::fromRaw($key, $values));
        }

        $this->cacheDatabaseLines->handle();

        return redirect()->route('squanto.index');
    }

    /**
     * By default laravel converts empty strings to null values. Here we ensure that empty strings are persisted.
     * Null values are not kept by squanto and will cascade to the original translation file value, which is not
     * what we want. Instead we keep an empty string to explicitly mark this value as empty.
     *
     * @param Request $request
     * @return Request
     */
    private function convertNullToEmptyString(Request $request): Request
    {
        $request = $request->merge([
            'squanto' => collect($request->input('squanto', []))->map(function ($translations, $key) {
                return array_map(function ($value) {
                    if (null === $value) {
                        $value = '';
                    }

                    return $value;
                }, $translations);
            })->toArray(),
        ]);

        return $request;
    }
}
