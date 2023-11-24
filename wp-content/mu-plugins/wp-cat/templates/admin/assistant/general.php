<p>This assistant is meant to help set up the following CAT feeds.  Click a link below or use the tabs above to navigate to each section.</p>
<p>It is recommended that you do each step in order, however check the project build specs to see if all three areas are needed on this site.</p>
<ul>
    <li><a href="?page=<?php echo $this->options_key ?>&tab=new" class="button button-primary" >New</a></li>
    <li><a href="?page=<?php echo $this->options_key ?>&tab=rental" class="button button-primary" >Rental</a></li>
    <li><a href="?page=<?php echo $this->options_key ?>&tab=used" class="button button-primary" >Used</a></li>
</ul>
<hr/>
<p>If you encounter any issues, you may want to review the tips under the <a href="?page=<?php echo $this->options_key ?>&tab=troubleshooting">troubleshooting tab</a>.</p>
<p>When totally finished, click to disable the assistant from showing, as well as the installation alert:</p>
<button type="submit"
        name="<?php echo $this->options_key ?>_disabled"
        id="<?php echo $this->options_key ?>_disabled"
        class="button button-primary"
        value="1"
        onclick="return confirm('Are you sure you want to disable the assistant?  It will be inaccessible until you disable then re-enable the CAT Plugin')"
>I'm finished</button>
<p><b>Note:</b> The assistant can be turned back on later if needed by disabling then re-enabling the CAT Plugin.</p>
