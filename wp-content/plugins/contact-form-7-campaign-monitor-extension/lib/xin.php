<?php
/*  Copyright 2010-2022 Renzo Johnson (email: renzo.johnson at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

$cme_tool_autoupdate = get_option( 'campaignmonitor-update') ;

if ( $cme_tool_autoupdate === '0' or  $cme_tool_autoupdate ==='1' ) {
    update_option( 'campaignmonitor-update', $cme_tool_autoupdate );

    } else {

      $deprecated = null;
      $autoload = 'no';
      add_option( 'campaignmonitor-update', '1', $deprecated, $autoload );
      $cme_tool_autoupdate = 1;
    }

?>

 <table class="form-table mt0 description">
    <tbody>

				<tr>
        <th scope="row">Custom Fields</th>
        <td>
          <fieldset><legend class="screen-reader-text"><span>Custom Fields</span></legend><label for="wpcf7-campaignmonitor-cf-active">
           <input type="checkbox" id="wpcf7-campaignmonitor-cf-active" name="wpcf7-campaignmonitor[cfactive]" value="1"<?php echo ( isset($cf7_cm['cfactive']) ) ? ' checked="checked"' : ''; ?> />
          <?php echo esc_html( __( 'Send more fields to campaingmonitor.com', 'wpcf7' ) ); ?>  <a href="<?php echo CME_URL ?>"class="helping-field" target="_blank" title="get help with Custom Fields"> Learn More </a></label>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row">Required Acceptance</th>
        <td>
          <fieldset>
            <?php cme_html_selected_tag_optin ( $listatags,$cf7_cm ) ?>
            <p class="description">Required Acceptance Field.</p>
          </fieldset>
        </td>
      </tr>

			<tr>
        <th scope="row">Resubscribe</th>
        <td>
          <fieldset><legend class="screen-reader-text"><span>Allow Users to Resubscribe after being Deleted </span></legend><label for="wpcf7-campaignmonitor-resubscribeoption">
        <input type="checkbox" id="wpcf7-campaignmonitor-resubscribeoption" name="wpcf7-campaignmonitor[resubscribeoption]" value="1"<?php echo ( isset($cf7_cm['resubscribeoption']) ) ? ' checked="checked"' : ''; ?> />
           <a href="<?php echo CME_URL ?>" class="helping-field" target="_blank" title="get help with Resubscribe after being Deleted"> Help <span class="red-icon dashicons dashicons-sos"></span></a></label>
          </fieldset>
        </td>
      </tr>

			  <tr>
        <th scope="row">Developer</th>
        <td>
          <fieldset><legend class="screen-reader-text"><span>Developer</span></legend><label for="wpcf7-campaignmonitor-cf-support">
            <input type="checkbox" id="wpcf7-campaignmonitor-cf-support" name="wpcf7-campaignmonitor[cf-supp]" value="1"<?php echo ( isset($cf7_cm['cf-supp']) ) ? ' checked="checked"' : ''; ?> />
          A backlink to my site, not compulsory, but appreciated</label>
          </fieldset>
        </td>
      </tr>

			 <tr>
        <th scope="row">Debug Logger</th>
        <td>
          <fieldset><legend class="screen-reader-text"><span>Debug Logger</span></legend><label for="wpcf7-campaignmonitor-logfileEnabled">
          <input type="checkbox"
                 id="wpcf7-campaignmonitor-logfileEnabled"
                 name="wpcf7-campaignmonitor[logfileEnabled]"
                 value="1" <?php echo ( isset( $cf7_cm['logfileEnabled'] ) ) ? ' checked="checked"' : ''; ?>
          />
          Enable to troubleshoot issues with the extension.</label>
          </fieldset>
        </td>
      </tr>

			<tr>
        <th scope="row">Auto Update</th>
        <td>
          <fieldset><legend class="screen-reader-text"><span>Auto Update</span></legend><label for="wpcf7-campaignmonitor-updates">
          <input type="checkbox" id="campaignmonitor-update" name="campaignmonitor-update" value="1"<?php echo ( $cme_tool_autoupdate == '1'  ) ? ' checked="checked"' : ''; ?> />
          Auto Update campaignmonitor</label>
          </fieldset>
        </td>
      </tr>



		</tbody>
 </table>