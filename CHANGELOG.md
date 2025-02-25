
## unreleased

### 5.0.0 - 2025-02-25
- Changed: minimum PHP version required is 8.3.
- Changed: `thinktomorrow/dynamic-attributes` dependency to `^2.0`.

### 3.0.3 - 2024-01-08
- Fixed: issue where key would be fetched as collection when in db multiple records starting with this key exists. This should only be so if the keys have a dot '.'. to indicate they are nested. 

### 0.5.5 - 2019-03-01
- Change: empty string in database would not be considered as a valid translation. This is now kept as an intentional value.
