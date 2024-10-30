<?php
/**
 * Set options for Koumpounophobia.
 *
 * This file is part of Koumpounophobia. Please see the Kphobia.php
 * file for copyright and license information.
 *
 * @author Michael Toppa
 * @version 0.5
 * @package Koumpounophobia
 */

?>

<div class="wrap">
    <h2><?php _e("Koumpounophobia Settings", KPHOBIA_L10N_NAME); ?></h2>

    <div style="float: right; font-weight: bold;"><a href="http://www.toppa.com/koumpounophobia-wordpress-plugin/" target="_blank"><?php _e("Koumpounophobia Help", KPHOBIA_L10N_NAME); ?></a></div>

    <?php if (strlen($message)) {
        require (KPHOBIA_DIR . '/display/include-message.php');
    } ?>

    <form action="<?php echo KPHOBIA_ADMIN_URL ?>" method="post">
    <input type="hidden" name="kphobia_action" value="update_options">
    <h3><?php _e("Koumpounophobia Built-in Buttons", KPHOBIA_L10N_NAME); ?></h3>
    <table class="form-table" style="width: auto;">
    <?php if ($kphobia_options['buttons']) {
        foreach ($kphobia_options['buttons'] as $handle=>$values) {
            echo "<tr><td>" . sprintf(__("Use <strong>%s</strong> button?", KPHOBIA_L10N_NAME), $handle) . "</td>";
            echo "<td><input type='radio' name='kphobia_options[buttons][$handle][active]' value='y'";
            echo ($values['active'] == 'y') ? " checked='checked'" : "";
            echo " /> " . __("Yes", KPHOBIA_L10N_NAME) . " ";
            echo "<input type='radio' name='kphobia_options[buttons][$handle][active]' value='n'";
            echo ($values['active'] == 'n') ? " checked='checked'" : "";
            echo " /> " . __("No", KPHOBIA_L10N_NAME) . "</td></tr>" . PHP_EOL;
        }
    }

    else {
        echo "<tr><td colspan='2'>" . __("Koumpounophobia settings data has been deleted or corrupted", KPHOBIA_L10N_NAME) . "</td></tr>" . PHP_EOL;
    } ?>
    </table>

    <h3><?php _e("Buttons From Other Plugins", KPHOBIA_L10N_NAME); ?></h3>
    <table class="form-table" style="width: auto;">
    <?php if ($kphobia_options['external_plugin_buttons']) {
        foreach ($kphobia_options['external_plugin_buttons'] as $handle=>$values) {
            echo "<tr><td>" . sprintf(__("Use <strong>%s</strong> button?", KPHOBIA_L10N_NAME), $handle) . "</td>";
            echo "<td><input type='radio' name='kphobia_options[external_plugin_buttons][$handle][active]' value='y'";
            echo ($values['active'] == 'y') ? " checked='checked'" : "";
            echo " /> " . __("Yes", KPHOBIA_L10N_NAME) . " ";
            echo "<input type='radio' name='kphobia_options[external_plugin_buttons][$handle][active]' value='n'";
            echo ($values['active'] == 'n') ? " checked='checked'" : "";
            echo " /> " . __("No", KPHOBIA_L10N_NAME) . "</td></tr>" . PHP_EOL;
        }
    }

    else {
        echo "<tr><td colspan='2'>" . __("No buttons from other plugins are currently registered.", KPHOBIA_L10N_NAME) . "</td></tr>" . PHP_EOL;
    } ?>
    </table>

    <h3><?php _e("Your Custom Buttons", KPHOBIA_L10N_NAME); ?></h3>
    <table class="form-table" style="width: auto;">
    <?php if ($kphobia_options['custom_buttons']) {
        foreach ($kphobia_options['custom_buttons'] as $handle=>$values) {
            echo "<tr><td><strong>" . __("Name", KPHOBIA_L10N_NAME) . "</strong></td><td><strong>$handle</strong></td></tr>" . PHP_EOL;

            foreach ($values as $k=>$v) {
                echo "<tr><td>" . $button_info[$k]['name'] . "</td><td>";

                if ($button_info[$k]['type'] == 'radio') {
                    foreach ($button_info[$k]['options'] as $input=>$label) {
                        echo "<input type='radio' name='kphobia_options[custom_buttons][$handle][$k]' value='$input'";
                        echo ($v == $input) ? " checked='checked'" : "";
                        echo " /> $label ";
                    }
                }

                elseif ($button_info[$k]['type'] == 'text') {
                    echo "<input type='text' name='kphobia_options[custom_buttons][$handle][$k]' value='$v' size='20' />";
                }

                echo "</td></tr>" . PHP_EOL;
            }
            echo "<tr><td>" . __("Delete", KPHOBIA_L10N_NAME) . "</td>";
            echo "<td><input type='checkbox' name='kphobia_delete_button[$handle]' /></td></tr>" . PHP_EOL;
        }
    }

    else {
        echo "<tr><td colspan='2'>" . __("You have not added any custom buttons yet.", KPHOBIA_L10N_NAME) . "</td></tr>" . PHP_EOL;
    }
    ?>
    </table>
    <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e("Save Options", KPHOBIA_L10N_NAME); ?>" /></p>
    </form>

    <hr />

    <form action="<?php echo KPHOBIA_ADMIN_URL ?>" method="post">
    <input type="hidden" name="kphobia_action" value="add_button">
    <h3><?php _e("Add a Custom Button", KPHOBIA_L10N_NAME); ?></h3>

    <table class="form-table" style="width: auto;">
    <tr>
    <td><?php _e("Name", KPHOBIA_L10N_NAME); ?></td>
    <td><input type="text" name="kphobia_new_button[handle]" size="20" value="<?php echo $validation['handle']; ?>" /></td>
    <td><?php _e("A name to refer to your button. This will be the label shown on the button in the HTML Editor, so it should be short.", KPHOBIA_L10N_NAME); ?></td>
    </tr>

    <?php foreach ($button_info as $k=>$v) {
        echo "<tr><td nowrap='nowrap'>" . $v['name'] . "</td><td>";
        if ($v['type'] == 'radio') {
            foreach ($v['options'] as $input=>$label) {
                echo "<input type='radio' name='kphobia_new_button[settings][$k]' value='$input'";
                if ($validation['settings'][$k] == $input) {
                    echo " checked='checked'";
                }

                else {
                    echo ($input == 'y') ? " checked='checked'" : "";
                }

                echo " /> $label ";
            }
        }

        elseif ($v['type'] == 'text') {
            echo "<input type='text' name='kphobia_new_button[settings][$k]' size='20' value='" . $validation['settings'][$k] . "' />";
        }

        echo "</td><td>" . $v['help'] . "</td></tr>" . PHP_EOL;
    } ?>
    </table>
    <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e("Add Button", KPHOBIA_L10N_NAME); ?>" /></p>
    </form>

    <div style="border: thin solid; padding: 5px;">
        <h3><?php _e("Uninstall Koumpounophobia", KPHOBIA_L10N_NAME); ?></h3>

        <form action="<?php echo KPHOBIA_ADMIN_URL ?>" method="post">
        <input type="hidden" name="kphobia_action" value="uninstall">
        <table border="0" cellspacing="3" cellpadding="3" class="form-table">
        <tr style="vertical-align: top;">
        <td nowrap="nowrap"><?php _e("Uninstall Koumpounophobia?", KPHOBIA_L10N_NAME); ?></td>
        <td><input type="checkbox" name="kphobia_uninstall" value="y" /></td>
        <td><?php _e("Check this box if you want to completely remove Koumpounophobia. <strong>This will permanently remove any Koumpounophobia-based custom buttons in your HTML Editor.</strong> After uninstalling, you can then deactivate Koumpounophobia on your plugins management page.", KPHOBIA_L10N_NAME); ?></td>
        </tr>
        </table>

        <p class="submit"><input class="button-secondary" type="submit" name="save" value="<?php _e("Uninstall Koumpounophobia", KPHOBIA_L10N_NAME); ?>" onclick="return confirm('<?php _e("Are you sure you want to uninstall Koumpounophobia?", KPHOBIA_L10N_NAME); ?>');" /></p>
        </form>
    </div>

    <h3><?php _e("Tipping: it isn't just for cows", KPHOBIA_L10N_NAME); ?></h3>

    <p><?php _e("I develop and maintain my WordPress plugins for the love of course, but a tip would be nice :-) Thanks!", KPHOBIA_L10N_NAME); ?></p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="4228054">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
</div>

