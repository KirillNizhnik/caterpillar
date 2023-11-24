<?php
/**
 * Plugin Name: Form Testing
 * Plugin URI: https://webfx.com
 * Description: When testing mail for a form 'Contact Form 7' or 'Gravity Forms', fill in any field with "FXTEST_youremail_FXTEST". Replace 'youremail' with the address to receive the test (or multiple comma-separated addresses).  That e-mail ONLY will receive the message, along with debugging information to show the intended recipients.  Replace 'youremail' with DIE to die and output the full notification information and send no e-mail at all - eg. "FXTEST_DIE_FXTEST".
 * Version: 1.2
 * Author: The WebFX Team
 * Author URI: https://webfx.com/
 *
 * Text Domain: webfx
 */

final class FX_Form_Testing
{

    public function __construct()
    {
        add_action( 'gform_notification', array($this, 'gform_notification'), PHP_INT_MAX, 3);
        add_action( 'wpcf7_mail_components', array($this, 'wpcf7_mail_components'), PHP_INT_MAX, 2);
    }

    /**
     * Return debug message showing test_string and original data
     */
    protected function debug_message($test_string, $originals=array())
    {
        $debug_message = "<b>Test mode was triggered by '$test_string'</b><br>\n";
        foreach ($originals as $key => $value)
        {
            $debug_message .= "<b>ORIGINAL ".strtoupper($key).":</b> $value<br>\n";
        }
        $debug_message .= "<hr><br>";
        return $debug_message;
    }

    /**
     * Check miscellaneous data for testing indicator
     * - This will check every field recursively, looking for any value that contains the trigger pattern (/FXTEST_(\S*)_FXTEST/)
     */
    protected function parse($data)
    {
        // Get data in the ideal format - flat array
        if (!is_array($data))
        {
            // Convert the data to an array if it's an object
            $data = (array) $data;
        }
        // Convert to numeric keys
        $data = array_values($data);

        // Loop through each item in the data array
        foreach ($data as $datum)
        {
            if (is_array($datum) or is_object($datum))
            {
                // If this is an array or ojbect, then we recursively parse it
                $return = $this->parse($datum);

                // if return[0] is true, then a trigger pattern has been found,
                // so we are in testing mode - all systems go!
                if ($return[0])
                {
                    return $return;
                }
            }
            // String? Check for the trigger pattern
            elseif(is_string($datum))
            {
                if (preg_match('/FXTEST_(\S*)_FXTEST/', $datum, $match))
                {
                    return array(
                        true,// testing mode is triggered!
                        $match[0], // full test string for reference
                        $match[1], // test e-mail or instruction (eg. die)
                    );
                }
            }
        }
    
        return array(
            false, // testing mode
            '',    // full test string
            '',    // test e-mail
        );
    }

    /**
     * Gravity Form Filter
     */
    function gform_notification($notification, $form, $entry)
    {
        $testing = false;

        list($testing, $test_string, $test_email) = $this->parse($entry);

        if ($testing)
        {
            $debug_message = $this->debug_message($test_string, array(
                'toType' => $notification['toType'],
                'to' => $notification['to'],
                'bcc' => $notification['bcc'],
            ));

            $notification['message'] = $debug_message . $notification['message'];
            
            $notification['to'] = $test_email;
            $notification['toType'] = 'email';
            $notification['bcc'] = '';

            if (strtolower($test_email) == 'die')
            {
                die("<pre>".print_r($notification,true)."</pre>");
            }
        }

        return $notification;
    }

    /**
     * Contact Form 7 Filter
     */
    function wpcf7_mail_components($components, $form)
    {
        $testing = false;

        list($testing, $test_string, $test_email) = $this->parse($_POST);

        if ($testing)
        {
            $original_headers = $components['additional_headers'];
            $debug_message = $this->debug_message($test_string, array(
                'recipients' => $components['recipient'],
                'headers' => $original_headers,
            ));

            // Remove CC/BCC lines from headers
            $split_headers = explode("\n", $original_headers); 
            $new_headers = "";
            foreach ($split_headers as $header)
            {
                if (!preg_match('/^b?cc:/i', $header))
                {
                    $new_headers.= "\n" . $header;
                }
            }
            
            $components['additional_headers'] = $new_headers;
            $components['body'] = $debug_message . $components['body'];
            $components['recipient'] = $test_email;

            if (strtolower($test_email) == 'die')
            {
                die("<pre>".print_r($components,true)."</pre>");
            }
        }

        return $components;
    }

}
new FX_Form_Testing();
