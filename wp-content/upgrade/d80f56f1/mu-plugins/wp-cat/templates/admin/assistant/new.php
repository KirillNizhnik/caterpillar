<?php

$steps = array(
    array(
        'title' => "Configure Settings"
        ,'content' => "
<p>Go to Settings &gt; CAT &gt; Feeds or <a href='options-general.php?page=cat-settings&tab=feeds#cat-new' target='_blank'>Click Here</a> and configure the New Feed settings.  Specifically:</p>
<dl>
    <dt><strong>Sales Channel Code</strong></dt>
    <dd>This should be provided by the Project Manager. Check the project build specs.</dd>
    <dt><strong>Available Classes</strong></dt>
    <dd>Check the project build specs. If unsure or not provided, check Machines for now; it can be changed later.</dd>
</dl>
<p>Other settings may be left alone for now</p>
        "
    )
    ,array(
        'title' => "Run inital Import"
        ,'content' => "
<p>Go to Settings &gt; CAT &gt; Importer or <a href='options-general.php?page=cat-settings&tab=importer' target='_blank'>Click Here</a>, then click Import for at least one enabled class. When this is complete, the products in that class should be viewable from the sidebar menu.</p>
        "
    )
    ,array(
        'title' => "Create page with shortcode"
        ,'content' => "
<p>Create a new page to be the top level category page for a new product class.  For example, <em>New &gt; CAT Machines</em>. Insert one of the following shortcodes on the page to list out the families in the given class:</p>
<dl>
    <dt><strong>Machines</strong></dt>
    <dd><code>[cat-family type=\"cat_new_machine_family\"]</code></dd>

    <dt><strong>Attachments</strong></dt>
    <dd><code>[cat-family type=\"cat_new_attachments_family\"]</code></dd>

    <dt><strong>Power Systems</strong></dt>
    <dd><code>[cat-family type=\"cat_new_power_family\"]</code></dd>

    <dt><strong>Site Support Products</strong></dt>
    <dd><code>[cat-family type=\"cat_new_allied_family\"]</code></dd>

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
    <li>Copy the entire folder titled \"new\" from <em>" . CAT()->plugin_path . "templates</em> to your theme's \"cat\" directory</li>
</ol>
        "
    )
);

include_once CAT()->plugin_path.'templates/admin/assistant/abstract-steps.php';
