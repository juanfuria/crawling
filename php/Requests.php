<?php

include_once('Constants.php');

/**
 * Make asynchronous requests to different resources as fast as possible and process the results as they are ready.
 */
class Requests
{
    public $handle;

    public function __construct()
    {
        $this->handle = curl_multi_init();
    }

    function process($urls, $callback, $depth)
    {
        if (DEBUG_ACTIVE) {
            echo "<pre>" . print_r($urls, true) . "</pre>";
        }
        // make sure the rolling window isn't greater than the # of urls
        $rolling_window = ROLLING_WINDOW;
        $rolling_window = (sizeof($urls) < $rolling_window) ? sizeof($urls) : $rolling_window;

        $master = curl_multi_init();

        // add additional curl options here
        $std_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5
        );

        $options = $std_options;

        // start the first batch of requests
        for ($i = 0; $i < $rolling_window; $i++) {
            $ch = curl_init();
            $options[CURLOPT_URL] = $urls[$i];
            curl_setopt_array($ch, $options);
            curl_multi_add_handle($master, $ch);
        }

        do {
            while (($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($execrun != CURLM_OK) {
                break;
            }

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($master)) {
                $info = curl_getinfo($done['handle']);

                $info['depth'] = $depth;

                $output = curl_multi_getcontent($done['handle']);

                // request successful.  process output using the callback function.
                $callback($output, $info);

                if (isset($urls[$i + 1])) {
                    // start a new request (it's important to do this before removing the old one)
                    $ch = curl_init();
                    $options[CURLOPT_URL] = $urls[$i++];  // increment i
                    curl_setopt_array($ch, $options);
                    curl_multi_add_handle($master, $ch);
                }

                // remove the curl handle that just completed
                curl_multi_remove_handle($master, $done['handle']);
            }

            usleep(10000); // stop wasting CPU cycles and rest for a couple ms

        } while ($running);

        curl_multi_close($master);
    }

    public function __destruct()
    {
        curl_multi_close($this->handle);
    }
}