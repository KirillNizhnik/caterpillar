<?php

$steps = array(
    array(
        'title' => "Set up New Feed"
        ,'content' => "
<p>The Rental feed relies heavily on the New feed, therefore it MUST be fully set up first.  Click the \"New\" tab above and follow all steps, if you have not already done so.</p>
        "
    )
    ,array(
        'title' => "Configure Settings"
        ,'content' => "
<p>Go to Settings &gt; CAT &gt; Feeds or <a href='options-general.php?page=cat-settings&tab=feeds#cat-rental' target='_blank'>Click Here</a> and configure the Rental Feed settings.  Specifically:</p>
<dl>
    <dt><strong>Enable the functionality</strong></dt>
    <dd>Select either 'Production Enabled' or 'QA Enabled' - if not specified in build specs, it is likely Production</dd>
    <dt><strong>User &amp; Password</strong></dt>
    <dd>Check the project build specs or request these form the Project Manager</dd>
</dl>
<p>Other settings may be left alone for now</p>
        "
    )
    ,array(
        'title' => "Run inital Import"
        ,'content' => "
<p>Go to Settings &gt; CAT &gt; Importer or <a href='options-general.php?page=cat-settings&tab=importer' target='_blank'>Click Here</a>, then click Import Rental Data. When this is complete, rental products should be viewable from the sidebar menu. They are identical to new products but will have rental rates listed.</p>
        "
    )
    ,array(
        'title' => "Create page with shortcode"
        ,'content' => "
<p>Create a new page to be the top level category page for a rental product class.  For example, <em>Rental &gt; CAT Machines</em>. Insert one of the following shortcodes on the page to list out the families in the given class:</p>
<dl>
    <dt><strong>Machines</strong></dt>
    <dd><code>[cat-family type=\"cat_new_machine_rental_family\"]</code></dd>

    <dt><strong>Attachments</strong></dt>
    <dd><code>[cat-family type=\"cat_new_attachments_rental_family\"]</code></dd>

    <dt><strong>Power Systems</strong></dt>
    <dd><code>[cat-family type=\"cat_new_power_rental_family\"]</code></dd>

    <dt><strong>Site Support Products</strong></dt>
    <dd><code>[cat-family type=\"cat_new_allied_rental_family\"]</code></dd>

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
    <li>Copy the entire folder titled \"rental\" from <em>" . CAT()->plugin_path . "templates</em> to your theme's \"cat\" directory</li>
</ol>
        "
    )
);

include_once CAT()->plugin_path.'templates/admin/assistant/abstract-steps.php';
