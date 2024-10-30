<?php
/*
Plugin Name: Koumpounophobia
Plugin URI: http://www.toppa.com/koumpounophobia-wordpress-plugin/
Description: A plugin for adding custom buttons to the WordPress HTML Editor.
Author: Michael Toppa
Version: 0.5
Author URI: http://www.toppa.com
*/

/**
 * Koumpounophobia provides new buttons for the WordPress HTML Editor, with
 * custom jQuery-based dialogs. It enables you to add your own custom buttons,
 * and provides an interface for other plugins to add their own buttons.
 *
 * @author Michael Toppa
 * @version 0.5
 * @package Koumpounophobia
 *
 * Copyright 2009 Michael Toppa
 *
 * Koumpounophobia is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3 of
 * the License, or (at your option) any later version.
 *
 * Koumpounophobia is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('KPHOBIA_OPTIONS', get_option('kphobia_options'));
define('KPHOBIA_PLUGIN_NAME', 'Kphobia');
define('KPHOBIA_DISPLAY_NAME', 'Koumpounophobia');
define('KPHOBIA_L10N_NAME', 'kphobia');
define('KPHOBIA_FILE', basename(__FILE__));
define('KPHOBIA_DIR', dirname(__FILE__));
define('KPHOBIA_PATH', KPHOBIA_DIR . '/' . KPHOBIA_FILE);
define('KPHOBIA_ADMIN_URL', $_SERVER['PHP_SELF'] . "?page=" . basename(KPHOBIA_DIR) . '/' . KPHOBIA_FILE);
define('KPHOBIA_FAQ_URL', 'http://www.toppa.com/koumpounophobia-wordpress-plugin/');

/**
 * The Kphobia class is a container for its static methods
 *
 * @author Michael Toppa
 * @package Koumpounophobia
 */
class Kphobia {
    /**
     * Called automatically (after the end of the class) to register
     * hooks and add the actions and filters.
     *
     * @static
     * @access public
     */
    function bootstrap() {
        register_activation_hook(KPHOBIA_PATH, array(KPHOBIA_PLUGIN_NAME, 'install'));
        add_action('admin_menu', array(KPHOBIA_PLUGIN_NAME, 'setupAdmin'));
        add_action('admin_footer', array(KPHOBIA_PLUGIN_NAME, 'addDialogs'));
        load_plugin_textdomain(KPHOBIA_L10N_NAME,
            PLUGINDIR . '/' . basename(KPHOBIA_DIR) . '/languages/',
            basename(KPHOBIA_DIR) . '/languages/');
    }

    /**
     * Initializes Kphobia options.
     *
     * @static
     * @access public
     */
    function install() {
        $kphobia_options = unserialize(KPHOBIA_OPTIONS);
        $buttons = array(
            'div' => array(
                'tag' => 'div',
                'title' => 'add div tag',
                'id' => 'ed_div',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'span' => array(
                'tag' => 'span',
                'title' => 'add span tag',
                'id' => 'ed_span',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'anchor' => array(
                'tag' => 'a',
                'title' => 'add anchor tag',
                'id' => 'ed_link',
                'self_close' => 'n',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
            'img' => array(
                'tag' => 'img',
                'title' => 'add image tag',
                'id' => 'ed_img',
                'self_close' => 'y',
                'shortcode' => 'n',
                'active' => 'y',
                'input_dialog' => 'y'),
        );

        if (empty($kphobia_options['custom_buttons'])) {
            $custom_buttons = array();
        }

        else {
            $custom_buttons = $kphobia_options['custom_buttons'];
        }

        if (empty($kphobia_options['external_plugin_buttons'])) {
            $external_plugin_buttons = array();
        }

        else {
            $external_plugin_buttons = $kphobia_options['external_plugin_buttons'];
        }

        $rev_options = array(
            'buttons' => $buttons,
            'custom_buttons' => $custom_buttons,
            'external_plugin_buttons' => $external_plugin_buttons);

        add_option('kphobia_options', serialize($rev_options));
    }

    /**
     * For external plugins to register custom buttons with Kphobia. Registered
     * buttons are automatically set to active.
     *
     * @param string $handle the name to use when referring to the custom button (eg: anchor)
     * @param string $tag the tag to insert, not including delimiters (eg: a)
     * @param string $title the title attribute for the button tag (eg: add anchor tag)
     * @param string $id the id attribute for the button tag; should start with ed_ (eg: ed_anchor)
     * @param string $self_close 'y' if a self-closing tag (eg: an image tag) 'n' otherwise
     * @param string $shortcode 'y' if a WordPress shortcode tag, 'n' if an html tag
     * @param string $path optional path to the html file for the button's dialog, relative to the WP base dir (eg: /wp-content/plugins/your_plugin/anchor_dialog.html)
     * @static
     * @access public
     */
    function registerButton($handle, $tag, $title, $id, $self_close, $shortcode, $path = null) {
        $self_close = strtolower($self_close);
        $shortcode = strtolower($shortcode);
        $kphobia_options = unserialize(KPHOBIA_OPTIONS);

        if (!$kphobia_options['external_plugin_buttons'][$handle]
          && strlen($handle) && strlen($tag) && strlen($title)
          && strlen($id) && ($self_close == 'y' || $self_close == 'n')
          && ($shortcode == 'y' || $shortcode == 'n'))  {
            $kphobia_options['external_plugin_buttons'][$handle] = array(
                'tag' => $tag,
                'title' => $title,
                'id' => $id,
                'self_close' => $self_close,
                'shortcode' => $shortcode,
                'active' => 'y',
                'input_dialog' => 'n');

            if ($path) {
                $base_path = substr(ABSPATH, 0, -1); // remove the trailing slash

                if (file_exists($base_path . $path)) {
                    $kphobia_options['external_plugin_buttons'][$handle]['input_dialog'] = 'y';
                    $kphobia_options['external_plugin_buttons'][$handle]['path'] = $path;
                }
            }
            update_option('kphobia_options', serialize($kphobia_options));
            return true;
        }

        return false;
    }

    /**
     * For external plugins to deregister custom buttons with Kphobia.
     *
     * @param string $handle the name to use when referring to the custom button (eg: anchor)
     * @static
     * @access public
     */
    function deregisterButton($handle) {
        $kphobia_options = unserialize(KPHOBIA_OPTIONS);
        unset($kphobia_options['external_plugin_buttons'][$handle]);
        update_option('kphobia_options', serialize($kphobia_options));
        return true;
    }

    /**
     * Adds the settings menu, and code needed in the HTML Editor.
     *
     * @static
     * @access public
     */
    function setupAdmin() {
        add_options_page(KPHOBIA_DISPLAY_NAME, KPHOBIA_DISPLAY_NAME, 6, __FILE__, array(KPHOBIA_PLUGIN_NAME, 'getOptionsMenu'));

        if (in_array(basename($_SERVER['SCRIPT_NAME']),
          array('post-new.php', 'page-new.php', 'post.php', 'page.php', 'comments.php')) ) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-ui-draggable');
            //wp_enqueue_script('jquery-bgiframe', '/' . PLUGINDIR . '/' . basename(KPHOBIA_DIR) . '/display/jquery.bgiframe.min.js', array('jquery'), '2.1.1');
            wp_enqueue_script('kphobia_js', '/' . PLUGINDIR . '/' . basename(KPHOBIA_DIR) . '/display/button_controller.js', array('quicktags'), false);
            wp_enqueue_style('kphobia_css', '/' . PLUGINDIR . '/' . basename(KPHOBIA_DIR) . '/display/kphobia.css');
            $kphobia_options = unserialize(KPHOBIA_OPTIONS);
            $button_groups = array('buttons', 'custom_buttons', 'external_plugin_buttons');

            foreach ($button_groups as $group) {
                if (!empty($kphobia_options[$group])) {
                    foreach ($kphobia_options[$group] as $handle=>$button) {
                        if ($button['active'] == 'y') {
                            $handles[] = $handle;
                            $tags[] = $button['tag'];
                            $titles[] = $button['title'];
                            $ids[] = $button['id'];
                            $self_close[] = $button['self_close'];
                            $shortcode[] = $button['shortcode'];
                            $input_dialogs[] = $button['input_dialog'];
                        }
                    }
                }
            }

            // wp_localize_script takes an array of scalars only, so
            // convert the button data to strings, and then convert
            // back to arrays in the js file.
            wp_localize_script('kphobia_js', 'kphobiaButtons', array(
                'handles' => implode(",",$handles),
                'tags' => implode(",",$tags),
                'titles' => implode(",",$titles),
                'ids' => implode(",",$ids),
                'self_close' => implode(",",$self_close),
                'shortcodes' => implode(",",$shortcode),
                'input_dialogs' => implode(",",$input_dialogs)
            ));
        }
    }

    /**
     * Adds the html for the button dialogs to the admin page footer
     *
     * @static
     * @access public
     */
    function addDialogs() {
        if (in_array(basename($_SERVER['SCRIPT_NAME']),
          array('post-new.php', 'page-new.php', 'post.php', 'page.php', 'comments.php')) ) {
            require_once(KPHOBIA_DIR . '/display/dialogs.html');

            if (file_exists(KPHOBIA_DIR . '/display/custom_dialogs.html')) {
                require_once(KPHOBIA_DIR . '/display/custom_dialogs.html');
            }

           $kphobia_options = unserialize(KPHOBIA_OPTIONS);

            if (!empty($kphobia_options['external_plugin_buttons'])) {
                $base_path = substr(ABSPATH, 0, -1); // remove the trailing slash
                foreach($kphobia_options['external_plugin_buttons'] as $button) {
                    if ($button['active'] == 'y' && $button['input_dialog'] == 'y' && file_exists($base_path . $button['path'])) {
                        require_once($base_path . $button['path']);
                    }
                }
            }
        }
    }

    /**
     * Generates and echoes the HTML for the Kphobia settings menu and
     * sets Kphobia options.
     *
     * @static
     * @access public
     */
    function getOptionsMenu() {
        // can't use the KPHOBIA_OPTIONS constant as the options may have changed
        $kphobia_options = unserialize(get_option('kphobia_options'));
        $button_info = array(
            'tag' => array(
                'type' => 'text',
                'name' => __('Tag', KPHOBIA_L10N_NAME),
                'help'=> __('The tag your button will insert, not including delimiters (do not include &lt; &gt; or [ ] delimiters)', KPHOBIA_L10N_NAME),
                'required' => true),
            'title' => array(
                'type' => 'text',
                'name' => __('Title', KPHOBIA_L10N_NAME),
                'help'=> __('Title text for your button; appears as mouseover help text in most browsers', KPHOBIA_L10N_NAME),
                'required' => true),
            'id' => array(
                'type' => 'text',
                'name' => __('ID', KPHOBIA_L10N_NAME),
                'help'=> __('The id of your button. This should start with "ed_" (for example, "ed_span"). If you use the id of a button already in the  button bar, it will replace that button', KPHOBIA_L10N_NAME),
                'required' => true),
            'self_close' => array(
                'type' => 'radio',
                'options' => array('y' => __('Yes', KPHOBIA_L10N_NAME), 'n' => __('No', KPHOBIA_L10N_NAME)),
                'name' => __('Self closing tag', KPHOBIA_L10N_NAME),
                'help'=> __('Whether your tag self closes (like an &lt;img /&gt; tag) or has a separate closing tag (like a &lt;/p&gt; tag)', KPHOBIA_L10N_NAME),
                'required' => true),
            'shortcode' => array(
                'type' => 'radio',
                'options' => array('y' => __('Shortcode', KPHOBIA_L10N_NAME), 'n' => __('HTML', KPHOBIA_L10N_NAME)),
                'name' => __('Tag type', KPHOBIA_L10N_NAME),
                'help'=> __('Whether your tag is a shortcode tag (with [ ] delimiters) or an HTML tag (with &lt; &gt; delimiters)', KPHOBIA_L10N_NAME),
                'required' => true),
            'active' => array(
                'type' => 'radio',
                'options' => array('y' => __('Yes', KPHOBIA_L10N_NAME), 'n' => __('No', KPHOBIA_L10N_NAME)),
                'name' => __('Enabled', KPHOBIA_L10N_NAME),
                'help'=> __('Whether your button is enabled in the button bar, or disabled and not shown in the button bar', KPHOBIA_L10N_NAME),
                'required' => true),
            'input_dialog' => array(
                'type' => 'radio',
                'options' => array('y' => __('Yes', KPHOBIA_L10N_NAME), 'n' => __('No', KPHOBIA_L10N_NAME)),
                'name' => __('Custom Dialog', KPHOBIA_L10N_NAME),
                'help'=> __("Select 'yes' to enable a custom input dialog for your button. The dialog is where you can accept user input for your tag's attributes. For example, if you are creating a button for the &lt;img&gt; tag, you would have your form dialog ask the user to provide the value for the 'src' attribute. All custom dialogs belong in your plugin directory, in the file kphobia/display/custom_dialogs.html (you'll need to create this file). Use the dialogs in kphobia/display/dialogs.html file as models for creating your custom dialogs. <strong>Important:</strong> you must give your dialog elements IDs that follow the naming conventions you see in dialogs.html (for example, kphobia_<em>yourButtonName</em>_dialog).", KPHOBIA_L10N_NAME),
                'required' => true),
        );

        // Start the cache
        ob_start();

        switch ($_REQUEST['kphobia_action']) {
        case 'uninstall':
            // make doubly sure they want to uninstall
            if ($_REQUEST['kphobia_uninstall'] == 'y') {
                if (Kphobia::uninstall() == true) {
                    // so the form doesn't repopulate, since we defined $kphobia_options above
                    unset($kphobia_options);
                    $message = __("Koumpounophobia has been uninstalled. You can now deactivate Koumpounophobia on your plugins management page.", KPHOBIA_L10N_NAME);
                }

                else {
                    $message = __("Uninstall of Koumpounophobia failed. Database error:", KPHOBIA_L10N_NAME);
                    $db_error = true;
                }
            }

            else {
                $message = __("You must check the 'Uninstall Koumpounophobia' checkbox to confirm you want to uninstall Koumpounophobia", KPHOBIA_L10N_NAME);
            }

            break;

        case 'add_button':
            $validation_ok = true;
            // adding a new button
            array_walk_recursive($_REQUEST['kphobia_new_button'], KPHOBIA_PLUGIN_NAME . '::_htmlentities');
            array_walk_recursive($_REQUEST['kphobia_new_button'], KPHOBIA_PLUGIN_NAME . '::_trim');

            // check required fields
            if (!$_REQUEST['kphobia_new_button']['handle']) {
                $validation_ok = false;
            }

            else {
                foreach ($button_info as $k=>$v) {
                    if (!$_REQUEST['kphobia_new_button']['settings'][$k] && $v['required']) {
                        $validation_ok = false;
                        break;
                    }
                }
            }

            if ($validation_ok) {
                $handle = $_REQUEST['kphobia_new_button']['handle'];
                $kphobia_options['custom_buttons'][$handle] = array();

                foreach($_REQUEST['kphobia_new_button']['settings'] as $k=>$v) {
                    $kphobia_options['custom_buttons'][$handle][$k] = $v;
                }

                $kphobia_options = Kphobia::_array_merge_recursive($kphobia_options, $_REQUEST['kphobia_options']);
                update_option('kphobia_options', serialize($kphobia_options));
                $message = __("Button created.", KPHOBIA_L10N_NAME);
            }

            else {
                $validation = $_REQUEST['kphobia_new_button'];
                $message = __("Settings not saved. All fields for a new button are required.", KPHOBIA_L10N_NAME);
            }

            break;

        case 'update_options':
            array_walk_recursive($_REQUEST['kphobia_options'], array(KPHOBIA_PLUGIN_NAME, '_htmlentities'));
            array_walk_recursive($_REQUEST['kphobia_options'], array(KPHOBIA_PLUGIN_NAME, '_trim'));

            // deleting buttons
            if ($_REQUEST['kphobia_delete_button']) {
                foreach($_REQUEST['kphobia_delete_button'] as $k=>$v) {
                    unset($kphobia_options['custom_buttons'][$k]);
                    unset($_REQUEST['kphobia_options']['custom_buttons'][$k]);
                }

                $message = __("Button deleted. ", KPHOBIA_L10N_NAME);
            }

            // save all settings
            $kphobia_options = Kphobia::_array_merge_recursive($kphobia_options, $_REQUEST['kphobia_options']);
            update_option('kphobia_options', serialize($kphobia_options));

            $message .= __("Koumpounophobia settings saved.", KPHOBIA_L10N_NAME);
            break;
        }

        // Get the markup and display
        require(KPHOBIA_DIR . '/display/options.php');
        $options_form = ob_get_contents();
        ob_end_clean();
        echo $options_form;
    }

    /**
     * Deletes the Kphobia option setttings. This is irrevocable!
     *
     * @static
     * @access public
     * @return boolean true: uninstall successful
     */
    function uninstall() {
        delete_option('kphobia_options');
        return true;
    }

    /**
     * array_walk callback method for htmlentities()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    function _htmlentities(&$string, $key) {
        $string = htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * array_walk callback method for trim()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    function _trim(&$string, $key) {
        $string = trim($string);
    }

    /**
     * Provides the behavior that php's array_merge_recursive should provide.
     * Copied from http://us3.php.net/manual/en/function.array-merge-recursive.php#82976
     *
     * @static
     * @access private
     * @param array $arr (required): base array
     * @param array $ins (required): the array key to merge; values in $ins while overwrite values in $arr
     */
    function _array_merge_recursive($arr,$ins) {
        if (is_array($arr) && is_array($ins)) foreach ($ins as $k => $v) {
            if (isset($arr[$k]) && is_array($v) && is_array($arr[$k])) $arr[$k] = Kphobia::_array_merge_recursive($arr[$k],$v);
            else $arr[$k] = $v;
        }
        return($arr);
    }
}

if (!function_exists('array_walk_recursive')) {
    /**
     * array_walk_recursive is new in PHP 5. This provides PHP 4 compatibility.
     * From http://us2.php.net/manual/en/function.array-walk-recursive.php#59984
     */
    function array_walk_recursive(&$input, $funcname, $userdata = "")
    {
        if (!is_callable($funcname))
        {
            return false;
        }

        if (!is_array($input))
        {
            return false;
        }

        foreach ($input AS $key => $value)
        {
            if (is_array($input[$key]))
            {
                array_walk_recursive($input[$key], $funcname, $userdata);
            }
            else
            {
                $saved_value = $value;
                if (!empty($userdata))
                {
                    $funcname($value, $key, $userdata);
                }
                else
                {
                    $funcname($value, $key);
                }

                if ($value != $saved_value)
                {
                    $input[$key] = $value;
                }
            }
        }
        return true;
    }
}

Kphobia::bootstrap();
?>
