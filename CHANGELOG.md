
## unreleased

## 1.0.0 - unreleased
The goal for our next release is to solidify the concept that there are two sources of truth, resp. the translation file and the database. 
They each serve a different purpose for squanto. We consider the application files to be in control of the developer and the database in control of the client / webmaster.  
The translation file is the authority for the *structure* of the translations. The database is the source for the translation values. 

This separation will remove the majority of concerns that arise when trying to sync from or to both sources.
This also implies that, once the translation is in the database, the value will not be overwritten by the application file.
The structure and presentation of the translations should be set and managed by the developer via _metadata_ files.

The following commands are now available:
- squanto:check - gives you an overview of which translations are new and which are obsolete.
- squanto:push - Updates the application structure to the database and in case of new translations, also the values.
- squanto:purge - removes any obsolete database values whose keys are removed from the application files.
- squanto:cache - to refresh the cached files. This is automatically done after every command.

### Proposed changes
- Line management info is the **metadata for each line**: optional label, description, type of field, ... We will remove the line management on database level and maintain metadata via the file system. This allows to keep everything in vc.
- in config there should be a path from where these files can be set. for each translation file there is a corresponding mgmt file.
- move config to thinktomorrow/squanto
- sync command: restructure database, update metadata, repopulate file values, inserts new translations if not already present. It should be save to use the sync in deployment scripts.
- the import command will be removed from the api. Instead the sync command should be used.
- option for sync to only update one source: --to-database or --to-file
- subcommands needed for the syncing: move / reorder keys, add missing keys with their translations, remove keys, *rename* keys
- after the sync command, there should be a clear report on what has happened.
- rename / move lines should be via a command so that this can be tracked as a 'migration'.


### 0.5.5 - 2019-03-01
- Change: empty string in database would not be considered as a valid translation. This is now kept as an intentional value.
