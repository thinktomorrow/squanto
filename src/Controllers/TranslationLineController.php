<?php

namespace Thinktomorrow\Squanto\Controllers;

use App\Http\Controllers\Controller;
use Thinktomorrow\Squanto\Domain\Trans;
use Thinktomorrow\Squanto\Domain\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TranslationLineController extends Controller
{
    public function create($slug)
    {
        $available_locales = config('translatable.locales');
        $group = Page::findBySlug($slug);
        $trans = new Trans;

        return view('admin.trans.lines.create', compact('available_locales', 'group', 'trans'));
    }

    public function store(Request $request, $slug)
    {
        $group = Page::findBySlug($slug);

        $trans = Trans::make(
            Str::slug($request->get('key')),
            $group->id,
            $request->get('label'),
            $request->get('description'),
            $request->has('paragraph') ? 'paragraph' : 'sentence'
        );

        $this->saveValueTranslations($trans, $request->get('trans'));

        return redirect()->route('admin.trans.edit', $group->slug)->with('messages.success', $trans->key. ' translation line created!');
    }

    private function saveValueTranslations(Trans $trans, array $translations)
    {
        collect($translations)->map(function ($value, $locale) use ($trans) {

            $value = cleanupHTML($value);

            if (is_null($value) || "" === $value) {
                $trans->removeTranslation($locale);
            } else {
                $trans->saveTranslation($locale, 'value', $value);
            }
        });
    }
}
