<?php

$steps = array(
    array(
        'title' => "Refresh Permalinks"
        ,'content' => "
<p>If any feed pages do not seem to work (ie. 404), you may need to refresh WordPress's rewrite rules.</p>
<p>Go to Settings &gt; Permalinks or <a href='options-permalink.php' target='_blank'>Click Here</a>, then click Save Changes.</p>
<p>This will refresh WordPress's rewrite rules so new pages created by the CAT Plugin are viewable.</p>
        "
    )
    ,array(
        'title' => "Customize JS & CSS"
        ,'content' => "
<p>The plugin adds some default JS and CSS which should be moved to your template</p>
<p>Review the contents of these files and copy what you need to the active theme's js and css files:</p>
<ul>
    <li><a href='".CAT()->plugin_url."assets/css/template.css' target='_blank'>".CAT()->plugin_path."assets/css/template.css</a></li>
    <li><a href='".CAT()->plugin_url."assets/js/template.js' target='_blank'>".CAT()->plugin_path."assets/js/template.js</a></li>
</ul>
<p>Next, disable the default files from loading by adding this to wp-config.php</p>
<code>define('WP_CAT_DISABLE_TEMPLATE_ASSETS', true);</code>
        "
    )
);

include_once CAT()->plugin_path.'templates/admin/assistant/abstract-steps.php';
