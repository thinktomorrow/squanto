<?php

namespace Thinktomorrow\Squanto\Controllers;

use App\Http\Controllers\Controller;
use Thinktomorrow\Squanto\TranslatableController;
use Thinktomorrow\Squanto\Domain\Trans;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Handlers\ClearCacheTranslations;
use Thinktomorrow\Squanto\Handlers\SaveTranslationsToDisk;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    use TranslatableController;

    public function create($slug)
    {
        $group = Page::findBySlug($slug);

        return view('admin.trans.create', compact('group'));
    }


    public function edit($slug)
    {
        $available_locales = config('translatable.locales');

        $group = Page::findBySlug($slug);

        $lines = Trans::getByGroup(null, $group->id);

        return view('admin.trans.edit', compact('group', 'lines', 'available_locales'));
    }

    public function update(Request $request, $group_id)
    {
        $group = Page::find($group_id);

        $this->saveValueTranslations($request->get('trans'));

        // Resave our cached translation
        app(SaveTranslationsToDisk::class)->clear()->handle();

        return redirect()->route('admin.trans.edit', $group->slug)->with('messages.success', $group->label .' translations have been updated');
    }

    private function saveValueTranslations(array $translations)
    {
        collect($translations)->map(function ($translation, $locale) {
            collect($translation)->map(function ($value, $id) use ($locale) {

                $value = cleanupHTML($value);

                if (is_null($value) || "" === $value) {
                    Trans::find($id)->removeTranslation($locale);
                } else {
                    Trans::find($id)->saveTranslation($locale, 'value', $value);
                }
            });
        });
    }
}
