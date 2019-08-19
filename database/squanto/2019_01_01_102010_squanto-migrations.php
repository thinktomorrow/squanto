<?php

/**
 * This array represents the sequence of alterations for the squanto database values.
 * These migrations will synchronise the structure to the language files to that
 * in the database. Via migrations, lines can be moved, renamed, or removed.
 * Note that values are never changed via these migrations expect when the line is removed.
 *
 * generate changes with squanto:make-migration -> creates migration file...
 */
return [
    // This will move or rename the line ...
    'first-page.first-key' => 'first-page.new-key',

    // This will remove the line and its values ...
    'second-page.first-key' => null,

];