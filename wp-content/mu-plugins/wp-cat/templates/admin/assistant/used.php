<?php

$steps = array(
    array(
        'title' => "Configure Settings"
        ,'content' => "
<p>Go to Settings &gt; CAT &gt; Feeds or <a href='options-general.php?page=cat-settings&tab=feeds#cat-used' target='_blank'>Click Here</a> and configure the Used Feed settings.  Specifically:</p>
<dl>
    <dt><strong>DSF-Data URL</strong></dt>
    <dd>This should be provided by the Project Manager. Check the project build specs.</dd>
</dl>
<p>Other settings may be left alone for now</p>
        "
    )
    ,array(
        'title' => "Run inital Import"
        ,'content' => "
<p>Go to Settings &gt; CAT &gt; Importer or <a href='options-general.php?page=cat-settings&tab=importer' target='_blank'>Click Here</a>, then click Import Used Machines. When this is complete, used products should be viewable from the sidebar menu.</p>
        "
    )
    ,array(
        'title' => "Create page with shortcode"
        ,'content' => "
<p>Create a new page to be the top level category page for used products.  For example, <em>Used</em>. Insert the following shortcode on the page to list out all used families:</p>
<dl>
    <dt><strong>All Used Product Families</strong></dt>
    <dd><code>[cat-family type=\"cat_used_machine_family\"]</code></dd>
</dl>
        "
    )
    ,array(
        'title' => "Set up CAT templates in theme"
        ,'content' => "
<p>When the plugin was activated, the CAT template files should have already been copied to your active theme's folder.  You will find them in the \"cat\" directory.</p>
<p>Once copied, you may begin editing the files in your theme and viewing the results on the page you created.</p>
<b>Important:</b> Do not directly edit the files in <em>" . CAT()->plugin_path . "templates</em>.  Rather, edit the copies that exist in your theme folder.
<hr/>
<p><b>If</b> the cat templates failed to copy, you may copy them manually as follows:</p>
<ol>
    <li>In your theme folder, create a directory titled \"cat\"</li>
    <li>Copy the entire folder titled \"used\" from <em>" . CAT()->plugin_path . "templates</em> to your theme's \"cat\" directory</li>
</ol>
        "
    )
);

include_once CAT()->plugin_path.'templates/admin/assistant/abstract-steps.php';
