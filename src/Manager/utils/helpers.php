<?php

/**
 * --------------------------------------------------------------------------
 * Helper: squantoCleanupString
 * --------------------------------------------------------------------------
 *
 * Takes an input and cleans up a regular string from unwanted input
 *
 * @param 	string 	$value
 * @return 	string
 */
if(!function_exists('squantoCleanupString'))
{
    function squantoCleanupString( $value )
    {
        $value = strip_tags($value);

        return trim($value);
    }
}

/**
 * --------------------------------------------------------------------------
 * Helper: squantoCleanupHTML
 * --------------------------------------------------------------------------
 *
 * Takes an input and cleans up unwanted / malicious HTML
 *
 * @param 	string 	$value
 * @param 	string 	$whitelist - if false no tagstripping will occur - other than htmLawed
 * @return 	string
 */
if(!function_exists('squantoCleanupHTML'))
{
    function squantoCleanupHTML( $value, $whitelist = null )
    {
        if(!function_exists('htmLawed'))
        {
            require_once __DIR__ . '/vendors/htmlLawed.php';
        }

        if(is_null($whitelist))
        {
            $whitelist = '<code><span><div><label><a><br><p><b><i><del><strike><u><img><video><audio><iframe><object><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><code><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>';
        }
        // Strip entire blocks of malicious code
        $value = preg_replace(array(
            '@<script[^>]*?>.*?</script>@si',
            '@onclick=[^ ].*? @si'
        ),'',$value);
        // strip unwanted tags via whitelist...
        if(false !== $whitelist) $value = strip_tags($value, $whitelist);
        // cleanup HTML and any unwanted attributes
        $value = htmLawed($value);

        $value  = str_replace('&amp;', '&', $value);

        return $value;
    }
}