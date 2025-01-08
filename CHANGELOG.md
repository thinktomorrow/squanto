
## unreleased

### 4.0.0 - 2025-01-08
- Changed: php minimum version is now 8.3
- Changed: laravel minimum version is now 11.0

### 3.0.3 - 2024-01-08
- Fixed: issue where key would be fetched as collection when in db multiple records starting with this key exists. This should only be so if the keys have a dot '.'. to indicate they are nested. 

### 0.5.5 - 2019-03-01
- Change: empty string in database would not be considered as a valid translation. This is now kept as an intentional value.
