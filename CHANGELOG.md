# Changelog

## ideas

#### health
// check if sources are out of sync

#### scan (health scan)
// checks filesystem for missing translations, unused translation keys

Importing translations from file to database takes care of the structure, key removals or reorderings and providing documentation for the admin user.
It will not change existing values.

Default syncing both sources will thus restructure the database layer and populate the files with the proper values.

#### export
Backup database values to csv where each language is a column. Lines are divided by their page.
`php artisan squanto:export`
options: --src=file/database, --dest=/path/for/csv

#### import
Import translations to the translation files. It prompts a preview of the additions / changes
`php artisan squanto:import /path/source/csv`

## unreleased

## 1.0.0 - unreleased
### Sources of truth
The goal for our stable release is to solidify the concept that there are two sources of truth, resp. the translation file and the database, and that they each serve a different purpose for squanto. The translation file is responsible for the *structure* of the translations.
The database is the source for the translation values themselves. This separation will remove the majority of concerns that arise
when trying to sync from or to both sources. There are a couple of actions that support this philosophy:

### Proposed changes
- Line management info is the metadata for each line: optional label, description, type of field, ... We will remove the line management on database level and maintain metadata via the file system. This allows to keep everything in vc.
- in config there should be a path from where these files can be set. for each translation file there is a corresponding mgmt file.
- move config to thinktomorrow/squanto
- sync command: restructure database, update metadata, repopulate file values, inserts new translations if not already present. It should be save to use the sync in deployment scripts.
- the import command will be removed from the api. Instead the sync command should be used.
- option for sync to only update one source: --only-database or --only-file
- subcommands needed for the syncing: move / reorder keys, add missing keys with their translations, remove keys, *rename* keys
- after the sync command, there should be a clear report on what has happened.
- rename / move lines should be via a command so that this can be tracked as a 'migration'.

squanto:export -> exports current lines as csv, markdown.

squanto:assist -> assists in changing translation files + adds these to migration file
banners.title is used but not set yet.
// pages.show
dfqdf qdf dfdf qdmskfj qmd kmqfj d{{ trans('banners.title') }}

1. create translation line (provide default translation)
2. replace with <key>
3. skip for now

banners.title is set but not used yet.
1. use it for replacing <key> occurrences
2. remove the key in language files
3. skip for now

### Changes
- Change: minimum requirement is laravel 5.7
- Change: Publish config to `config/thinktomorrow/squanto.php` instead of `config/squanto.php` to conform with other think tomorrow packages.
- Change: `LaravelTranslationsReader::read()` has been changed to `LaravelTranslationsReader::readAll()`.

### 0.5.5 - 2019-03-01
- Change: empty string in database would not be considered as a valid translation. This is now kept as an intentional value.
