<?php

namespace Thinktomorrow\Squanto\Manager\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Manager\Pages\LineViewModel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Thinktomorrow\Squanto\Manager\Pages\PagesRepository;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\Query\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Database\Application\UpdateDatabaseLine;

class ManagerController extends Controller
{
    use ValidatesRequests;

    /** @var PagesRepository */
    private PagesRepository $pagesRepository;

    /** @var DatabaseLinesRepository */
    private DatabaseLinesRepository $databaseLinesRepository;

    /** @var UpdateDatabaseLine */
    private UpdateDatabaseLine $updateDatabaseLine;

    /** @var CacheDatabaseLines */
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

        return view('squanto::index', [
            'pages' => $pages->all(),
        ]);
    }

    public function edit($pageSlug)
    {
        $lines = $this->databaseLinesRepository->modelsStartingWith($pageSlug);

        $viewModels = [];
        $lines->each(function($lineModel) use(&$viewModels){
            $viewModels[] = new LineViewModel($lineModel);
        });

        return view('squanto::edit', [
            'locales' => config('thinktomorrow.squanto.locales'),
            'lines' => $viewModels,
            'page' => $this->pagesRepository->findBySlug($pageSlug),
        ]);
    }

    public function update(Request $request, $pageSlug)
    {
        foreach($request->input('squanto', []) as $key => $values) {
            $this->updateDatabaseLine->handle(Line::fromRaw($key, $values));
        }

        $this->cacheDatabaseLines->handle();

        $page = $this->pagesRepository->findBySlug($pageSlug);

        return redirect()->route('squanto.index')->with('messages.success', $page->label() .' translations have been updated');
    }

//    private function saveValueTranslations(array $translations)
//    {
//        collect($translations)->map(function ($translation, $locale) {
//            collect($translation)->map(function ($value, $id) use ($locale) {
//
//                $line = DatabaseLine::find($id);
//
//                $value = squantoCleanupHTML($value);
//
//                if(false == config('thinktomorrow.squanto.paragraphize') && !$line->areParagraphsAllowed())
//                {
//                    $value = $this->replaceParagraphsByLinebreaks($value);
//                }
//
//                // If line value is not meant to contain tags, we should strip them
//                if (!$line->editInEditor()) {
//                    $value = squantoCleanupString($value);
//                }
//
//                if (null === $value) {
//                    $line->removeValue($locale);
//                } else {
//                    $line->saveValue($locale, $value);
//                }
//            });
//        });
//    }

    /**
     * @param $page
     * @return \Illuminate\Support\Collection
     */
    protected function groupLinesByKey($page)
    {
        $groupedLines = collect(['general' => []]);
        $groups = [];

        foreach ($page->lines as $line) {
            $keysegment = $this->getFirstSegmentOfKey($line);

            if (!isset($groups[$keysegment])) {
                $groups[$keysegment] = [];
            }
            $groups[$keysegment][] = $line;
        }

        // If firstkey occurs more than once, we will group it
        foreach ($groups as $group => $lines) {
            if (count($lines) < 2) {
                $groupedLines['general'] = array_merge($groupedLines['general'], $lines);
            } else {
                $groupedLines[$group] = $lines;
            }
        }

        return $groupedLines;
    }

    /**
     * Get suggestion for a label based on the key
     * e.g. foo.bar.title return bar
     * @return string
     */
    private function getFirstSegmentOfKey(DatabaseLine $line)
    {
        // Remove first part since that part equals the page
        $key = substr($line->key, strpos($line->key, '.')+1);

        $length = strpos($key, '.')?: strlen($key);
        $key = substr($key, 0, $length);

        return $key;
    }

    private function replaceParagraphsByLinebreaks($value)
    {
        $value = preg_replace('/<p[^>]*?>/', '', $value);

        // Last paragraph is just removed, not a linebreak
        if (substr($value, -mb_strlen('</p>')) === '</p>') {
            $value = substr($value, 0, -mb_strlen('</p>'));
        }

        $value = str_replace('</p>', '<br>', $value);

        return $value;
    }
}
