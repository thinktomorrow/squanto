# Basics
- TDD
- V hierarchy: first our trans than default trans files. Never touch the original translation files under /resources
- V extend translation class from laravel
- V save a translation
- V remove a translation
- V use cached version to reduce database calls: observe changes
- V save cached files in /storage/frameworks/cache/trans the same way as lang structured files

- remove dimsav dependency

# TODO
- allow html
- compare mode:  between langs (column mode vs stack mode)
- only dev can delete a entire key
- exclude groups from UI (webmasters)
- different management for devs - client
- check for missing translations
- provide CLI interface for developers
- scan for unused tags
- export / import from csv
- import / export as translation files with overwrite security (confirm overwrites)
- dev: change key and sync with usages in application

- squanto:doctor = (should be using the @msaid langman package)
 - check if keys are translated in all locales
 - check if there are unused translation keys
 - check if keys are used but without translation
- squanto sync option: update the translation file with the database content
- use table helper 3.1 for setting column widths: now we just linebreak after every 100 chars or so
- use table separator
- suggest type on first line / value creation
- restrict redactor options based on column 'allowed_html'
- how to allow for a 'view online' / 'preview online' option? Page can perhaps contain reference to a route/url?
