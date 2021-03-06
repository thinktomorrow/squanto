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
 * @param 	string 	$whitelist - if false no tagstripping will occur - other than htmlpurifier
 * @return 	string
 */
if(!function_exists('squantoCleanupHTML'))
{
    function squantoCleanupHTML( $value, $whitelist = null )
    {
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
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', config('squanto.htmlPurifierCache'));

        $purifier = new \HTMLPurifier($config);
        $value = $purifier->purify( $value );

        /**
         * htmlPurifier converts characters to their encode equivalents. This is something
         * that we need to reverse after the htmlPurifier cleanup.
         */
        $value = str_replace('&amp;', '&', $value);
        $value = str_replace('%3A', ':', $value);

        return $value;
    }
}
