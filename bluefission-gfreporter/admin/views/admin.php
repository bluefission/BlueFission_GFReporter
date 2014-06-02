<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   BlueFission_GFReporter
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2014 Devon Scott, BlueFission.com
 */ 

		if(!GFCommon::current_user_can_any("gravityforms_export_entries"))
			wp_die("You do not have permission to access this page");

        ?>

        <script type="text/javascript">
            function trigger_report() {
                
                var data = {
                    'action': 'trigger_report'
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function(response) {
                    if ( response == 'true') {
                        alert("Report Sent.");
                    } else {
                        alert("An error occurred, please try again later.");
                    }
                });
            }

            var gfSpinner;

            <?php GFCommon::gf_global(); ?>
            <?php GFCommon::gf_vars(); ?>

            function SelectExportForm(formId){

                if(!formId)
                    return;

                gfSpinner = new gfAjaxSpinner(jQuery('select#export_form'), gf_vars.baseUrl + '/images/spinner.gif', 'position: relative; top: 2px; left: 5px;');

                var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_select_export_form" );
                mysack.setVar( "rg_select_export_form", "<?php echo wp_create_nonce("rg_select_export_form") ?>" );
                mysack.setVar( "form_id", formId);
                mysack.onError = function() { alert('<?php echo esc_js(__("Ajax error while selecting a form", "gravityforms")) ?>' )};
                mysack.runAJAX();

                return true;
            }

            function EndSelectExportForm(aryFields, filterSettings){

                gfSpinner.destroy();

                if(aryFields.length == 0)
                {
                    jQuery("#export_field_container, #export_date_container, #export_submit_container").hide()
                    return;
                }

                var fieldList = "<li><input id='select_all' type='checkbox' onclick=\"jQuery('.gform_export_field').attr('checked', this.checked); jQuery('#gform_export_check_all').html(this.checked ? '<strong><?php _e("Deselect All", "gravityforms") ?></strong>' : '<strong><?php _e("Select All", "gravityforms") ?></strong>'); \"> <label id='gform_export_check_all' for='select_all'><strong><?php _e("Select All", "gravityforms") ?></strong></label></li>";
                for(var i=0; i<aryFields.length; i++){
                    fieldList += "<li><input type='checkbox' id='export_field_" + i + "' name='<?php echo $this->option_var; ?>[fields][]' value='" + aryFields[i][0] + "' class='gform_export_field'> <label for='export_field_" + i + "'>" + aryFields[i][1] + "</label></li>";
                }
                jQuery("#export_field_list").html(fieldList);
                jQuery("#export_date_start, #export_date_end").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});

                jQuery("#export_field_container, #export_filter_container, #export_date_container, #export_submit_container").hide().show();

                gf_vars.filterAndAny = '<?php _e("Export entries if {0} of the following match:", "gravityforms") ?>';
                jQuery("#export_filters").gfFilterUI(filterSettings);
            }
            jQuery(document).ready(function(){
                jQuery("#gform_export").submit(function(){
                    if(jQuery(".gform_export_field:checked").length == 0){
                        alert('<?php _e('Please select the fields to be exported', 'gravityforms');  ?>');
                        return false;
                    }
                });
            });


        </script>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <input type="button" value="Send Report Now" onclick="trigger_report();" style="float:right;" />
	<form method="post" action="options.php">
	<?php settings_fields( $this->plugin_slug . '-options' ); ?>
	<?php do_settings_sections( $this->plugin_slug ); ?>
 
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
	<!-- TODO: Provide markup for your options page here. -->
	
</div>