<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.preeco.de
 * @since      1.0.0
 *
 * @package    Preeco_Widgets
 * @subpackage Preeco_Widgets/admin/partials
 */
?>

<div class="wrap">
    <h1><?php _e( 'Settings' ) ?> › <?php _e( 'preeco Widgets', 'preeco_widgets' ) ?></h1>
    <h2><?php _e( 'Local caching', 'preeco_widgets' ) ?></h2>

    <p>
		<?php _e( 'The content of the widgets can be cached and delivered locally if desired. In this case, the content of the widget is requested from the server on a daily basis.', 'preeco_widgets' ) ?>
    </p>

    <hr>

    <form action="options.php" method="post">
		<?php
		settings_fields( 'preeco-widgets-settings' );
		do_settings_sections( 'preeco-widgets-settings' );
		?>
        <input type="hidden" placeholder="<?= esc_attr( get_option( 'preeco_widgets_timestamp' ) ); ?>"
               name="preeco_widgets_timestamp" value="<?= time() ?>">

        <table class="form-table tools-privacy-policy-page" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="preeco_widgets_caching_enabled"><?php _e( 'Enable caching', 'preeco_widgets' ) ?></label>
                </th>
                <td>
                    <select name="preeco_widgets_caching_enabled" id="preeco_widgets_caching_enabled">
                        <option value="0" <?php echo esc_attr(get_option('preeco_widgets_caching_enabled')) == '0' ? 'selected="selected"' : ''; ?>><?php _e( 'Cache inactive', 'preeco_widgets' ) ?></option>
                        <option class="level-0" value="1" <?php echo esc_attr(get_option('preeco_widgets_caching_enabled')) == '1' ? 'selected="selected"' : ''; ?>><?php _e( 'Cache active', 'preeco_widgets' ) ?></option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>

        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes') ?>"></p>
    </form>
</div>
