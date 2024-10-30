<?php
/*
  Plugin Name: Miso
  Plugin URI: http://wordpress.org/extend/plugins/miso-widget/
  Description: With this Miso widget you can easily add your gomiso.com movie checkin list to your wordpress blog.
  Version: 1.0
  Author: bolint
  Author URI: http://bolint.hu

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function Miso_Widget_install() {
    $widgetoptions = get_option('Miso_widget');
    $newoptions['user_name_id'] = '';
    $newoptions['layout_bg_color'] = '#333333';
    $newoptions['layout_text_color'] = '#FFFFFF';
    $newoptions['content_bg_color'] = '#000000';
    $newoptions['content_text_color'] = '#FFFFFF';
    $newoptions['content_link_color'] = '#9e9c9e';
    $newoptions['width'] = 300;
    $newoptions['height'] = 380;
    $newoptions['count'] = 5;
    add_option('Miso_widget', $newoptions);
}

function Miso_Widget_init($content) {
    if (strpos($content, '[Miso-Widget]') === false) {
        return $content;
    } else {
        $code = Miso_Widget_createjscode(false);
        $content = str_replace('[Miso-Widget]', $code, $content);
        return $content;
    }
}

function Miso_Widget_insert() {
    echo Miso_Widget_createjscode(false);
}

function Miso_Widget_createjscode($widget) {
    if ($widget != true) {
        return '';
    } else {
        $options = get_option('Miso_widget');
        if (!isset($options['width']) || !$options['width'] < 0) {
            $options['width'] = 300;
        }
        if (!isset($options['height']) || !$options['height'] < 0) {
            $options['height'] = 380;
        }
        if (!isset($options['count']) || !$options['count'] < 0) {
            $options['count'] = 5;
        }

        $urlParameters = array();
        foreach ($options as $key => $value) {
            if ($key == 'user_name_id') {
                if (intval($value)) {
                    $urlParameters[] = 'user_id=' . intval($value);
                } else {
                    $urlParameters[] = 'user_name=' . urlencode(trim($value));
                }
            } else if (trim($value) != '') {
                $urlParameters[] = $key . '=' . urlencode($value);
            }
        }

        $url = 'http://gomiso.com/widget/checkins?widget_type=user&amp;' . implode('&amp;', $urlParameters);

        $jstag = '<iframe src="' . $url . '" allowtransparency="true" frameborder="0" scrolling="no" width="' . $options['width'] . 'px" height="' . $options['height'] . 'px" style="border:none; overflow:hidden;"></iframe>';
        return $jstag;
    }
}

function Miso_Widget_uninstall() {
    delete_option('Miso_widget');
}

function Miso_Widget_load($hook_suffix) {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

function widget_init_Miso_Widget_widget() {
    if (!function_exists('register_sidebar_widget'))
        return;

    function Miso_Widget_widget($args) {
        extract($args);
        $options = get_option('Miso_widget');
        ?>
        <?php echo $before_widget; ?>	
        <?php
        if (!stristr($_SERVER['PHP_SELF'], 'widgets.php')) {
            echo Miso_Widget_createjscode(true);
        }
        ?>
        <?php echo $after_widget; ?>
        <?php
    }

    function Miso_Widget_widget_control() {
        $options = $newoptions = get_option('Miso_widget');
        if ($_POST["Miso_widget_submit"]) {
            $newoptions['user_name_id'] = strip_tags(stripslashes($_POST["Miso_widget_user_name_id"]));
            $newoptions['layout_bg_color'] = $_POST["Miso_widget_layout_bg_color"];
            $newoptions['layout_text_color'] = $_POST["Miso_widget_layout_text_color"];
            $newoptions['content_bg_color'] = $_POST["Miso_widget_content_bg_color"];
            $newoptions['content_text_color'] = $_POST["Miso_widget_content_text_color"];
            $newoptions['content_link_color'] = $_POST["Miso_widget_content_link_color"];
            $newoptions['width'] = intval($_POST["Miso_widget_width"]);
            $newoptions['height'] = intval($_POST["Miso_widget_height"]);
            $newoptions['count'] = intval($_POST["Miso_widget_count"]);
        }
        if ($options != $newoptions) {
            $options = $newoptions;
            update_option('Miso_widget', $options);
        }
        $userNameId = attribute_escape($options['user_name_id']);
        $layoutBgColor = attribute_escape($options['layout_bg_color']);
        $layoutTextColor = attribute_escape($options['layout_text_color']);
        $contentBgColor = attribute_escape($options['content_bg_color']);
        $contentTextColor = attribute_escape($options['content_text_color']);
        $contentLinkColor = attribute_escape($options['content_link_color']);
        $width = intval(attribute_escape($options['width']));
        $height = intval(attribute_escape($options['height']));
        $count = intval(attribute_escape($options['count']));
        ?>
        <script type='text/javascript'>
            jQuery(document).ready(function($) {
                $('.color-picker').wpColorPicker();
            });
        </script>

        <p><label for="Miso_widget_user_name_id"><?php _e('User ID/Login name'); ?>:</label> <input class="widefat" id="Miso_widget_user_name_id" name="Miso_widget_user_name_id" type="text" value="<?php echo $userNameId; ?>" /></p>
        <p><label for="Miso_widget_layout_bg_color"><?php _e('Layout Background color'); ?>:</label><input type="text" class="color-picker widefat" id="Miso_widget_layout_bg_color" name="Miso_widget_layout_bg_color" value="<?php echo $layoutBgColor; ?>" /></p>
        <p><label for="Miso_widget_layout_text_color"><?php _e('Layout Text color'); ?>:</label><input type="text" class="color-picker widefat" id="Miso_widget_layout_text_color" name="Miso_widget_layout_text_color" value="<?php echo $layoutTextColor; ?>" /></p>
        <p><label for="Miso_widget_content_bg_color"><?php _e('Content Background color'); ?>:</label><input type="text" class="color-picker widefat" id="Miso_widget_content_bg_color" name="Miso_widget_content_bg_color" value="<?php echo $contentBgColor; ?>" /></p>
        <p><label for="Miso_widget_content_text_color"><?php _e('Content Text color'); ?>:</label><input type="text" class="color-picker widefat" id="Miso_widget_content_text_color" name="Miso_widget_content_text_color" value="<?php echo $contentTextColor; ?>" /></p>
        <p><label for="Miso_widget_content_link_color"><?php _e('Content Link color'); ?>:</label><input type="text" class="color-picker widefat" id="Miso_widget_content_link_color" name="Miso_widget_content_link_color" value="<?php echo $contentLinkColor; ?>" /></p>
        <p><label for="Miso_widget_width"><?php _e('Width (pixels)'); ?>:</label><input type="text" class="widefat" id="Miso_widget_width" name="Miso_widget_width" value="<?php echo $width; ?>" /></p>
        <p><label for="Miso_widget_height"><?php _e('Height (pixels)'); ?>:</label><input type="text" class="widefat" id="Miso_widget_height" name="Miso_widget_height" value="<?php echo $height; ?>" /></p>
        <p><label for="Miso_widget_count"><?php _e('Checkin Count'); ?>:</label><input type="text" class="widefat" id="Miso_widget_count" name="Miso_widget_count" value="<?php echo $count; ?>" /></p>

        <input type="hidden" id="Miso_widget_submit" name="Miso_widget_submit" value="1" />
        <?php
    }

    register_sidebar_widget("Miso", Miso_Widget_widget);
    register_widget_control("Miso", "Miso_Widget_widget_control");
}

add_action('admin_enqueue_scripts', 'Miso_Widget_load');
add_action('widgets_init', 'widget_init_Miso_Widget_widget');
add_filter('the_content', 'Miso_Widget_init');
register_activation_hook(__FILE__, 'Miso_Widget_install');
register_deactivation_hook(__FILE__, 'Miso_Widget_uninstall');
