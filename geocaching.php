<?php
/*
Plugin Name: Geocaching
Description: Manage your found Geocaches and display a map of these using a simple shortcode
Version:     0.1
Author:      Ben Atherton
Author URI:  https://www.benatherton.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: geocaching
*/

wp_register_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js' );
wp_register_script( 'jquery-datatables', 'https://cdn.jsdelivr.net/npm/datatables.net@1.10.16/js/jquery.dataTables.min.js' );
wp_register_script( 'datatables-bootstrap', 'https://cdn.jsdelivr.net/npm/datatables.net-bs@1.10.16/js/dataTables.bootstrap.min.js' );
wp_register_script( 'datatables-buttons', plugin_dir_url( __FILE__ ) . 'assets/js/dataTables.buttons.min.js' );
wp_register_script( 'buttons-bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/buttons.bootstrap.min.js' );
wp_register_script( 'datatables-select', plugin_dir_url( __FILE__ ) . 'assets/js/dataTables.select.min.js' );
wp_register_script( 'datatables-editor', plugin_dir_url( __FILE__ ) . 'assets/js/dataTables.editor.min.js' );
wp_register_script( 'editor-bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/editor.bootstrap.min.js' );
wp_register_script( 'scripts', plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js' );
wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . get_option('geocaching_google_maps_api_key') . '&callback=initMap' );
wp_register_script( 'map', plugin_dir_url( __FILE__ ) . 'assets/js/map.js' );

wp_register_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css' );
wp_register_style( 'datatables-bootstrap', 'https://cdn.jsdelivr.net/npm/datatables.net-bs@1.10.16/css/dataTables.bootstrap.css' );
wp_register_style( 'buttons-bootstrap', plugin_dir_url( __FILE__ ) . 'assets/css/buttons.bootstrap.min.css' );
wp_register_style( 'select-bootstrap', plugin_dir_url( __FILE__ ) . 'assets/css/select.bootstrap.min.css' );
wp_register_style( 'editor-bootstrap', plugin_dir_url( __FILE__ ) . 'assets/css/editor.bootstrap.min.css' );
wp_register_style( 'style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );
  
  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  global $geocaching_db_version;
  $geocaching_db_version = '0.1';
  
  function geocaching_install() {
  	global $wpdb;
  	global $geocaching_db_version;
  
  	$table_name = $wpdb->prefix . 'geocaching';
  	$charset_collate = $wpdb->get_charset_collate();
  
  	$sql = "CREATE TABLE $table_name (
      `id` int(10) UNSIGNED NOT NULL,
      `geocacheID` char(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
      `geocacheTitle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
      `lat` float(10,6) NOT NULL,
      `lng` float(10,6) NOT NULL,
      `found` date NOT NULL,
      `type` enum('Traditional','A.P.E.','Letterbox','Multi-Cache','Event','Mega-Event','Giga-Event','Cache In Trash Out','GPS Adventures','Virtual','Webcam','EarthCache','Mystery','Wherigo') NOT NULL)
  		
  		PRIMARY KEY (`id`)
  	) $charset_collate;";
  
  	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  	dbDelta( $sql );
  
  	add_option( 'geocaching_db_version', $geocaching_db_version );
  }
  
  function my_geocaches() {
    wp_enqueue_script('bootstrap');
    wp_enqueue_script('jquery-datatables');
    wp_enqueue_script('datatables-bootstrap');
    wp_enqueue_script('datatables-buttons');
    wp_enqueue_script('buttons-bootstrap');
    wp_enqueue_script('datatables-select');
    wp_enqueue_script('datatables-editor');
    wp_enqueue_script('editor-bootstrap');
    wp_enqueue_script('scripts');
    
    wp_enqueue_style('bootstrap');
    wp_enqueue_style('datatables-bootstrap');
    wp_enqueue_style('buttons-bootstrap');
    wp_enqueue_style('select-bootstrap');
    wp_enqueue_style('editor-bootstrap');
    wp_enqueue_style('style');
?>
    
    <div id="header">
      <div class="pull-left">
        <h1>Geocaches I've Found</h1>
      </div>
      <div class="pull-right">
        <form id="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="XHJ5ETWZSAHZA">
          <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
          <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
        </form>
      </div>
    </div>
	  
	  <table id="found-geocaches" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Geocache ID</th>
          <th>Title</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Date Found</th>
          <th>Cache Type</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th>ID</th>
          <th>Geocache ID</th>
          <th>Title</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Date Found</th>
          <th>Cache Type</th>
        </tr>
      </tfoot>
    </table>
    
    <div class="pull-left">
      <h1>Geocaches I've Placed</h1>
    </div>
    
    <table id="my-geocaches" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Geocache ID</th>
          <th>Title</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Date Placed</th>
          <th>Cache Type</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th>ID</th>
          <th>Geocache ID</th>
          <th>Title</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Date Placed</th>
          <th>Cache Type</th>
        </tr>
      </tfoot>
    </table>
    
    <span class="pull-left">The Geocaching Logo is a registered trademark of Groundspeak, Inc. Used with permission.</span>
    
<?php
  }
  
  register_activation_hook( __FILE__, 'geocaching_install' );
  
  add_action('admin_menu', 'geocaching_create_menu');
  function geocaching_create_menu() {
    $icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIgICB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgICB2aWV3Qm94PSIwIDAgMTcxLjA2NjY3IDE1NS42OTMzMyIgICBoZWlnaHQ9IjE1NS42OTMzMyIgICB3aWR0aD0iMTcxLjA2NjY3IiAgIHhtbDpzcGFjZT0icHJlc2VydmUiICAgaWQ9InN2ZzIiICAgdmVyc2lvbj0iMS4xIj48bWV0YWRhdGEgICAgIGlkPSJtZXRhZGF0YTgiPjxyZGY6UkRGPjxjYzpXb3JrICAgICAgICAgcmRmOmFib3V0PSIiPjxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0PjxkYzp0eXBlICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPjwvY2M6V29yaz48L3JkZjpSREY+PC9tZXRhZGF0YT48ZGVmcyAgICAgaWQ9ImRlZnM2Ij48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMjAiICAgICAgIGNsaXBQYXRoVW5pdHM9InVzZXJTcGFjZU9uVXNlIj48cGF0aCAgICAgICAgIGlkPSJwYXRoMTgiICAgICAgICAgZD0iTSAwLDAgSCAxMjgzIFYgMTE2Ny42NyBIIDAgWiIgLz48L2NsaXBQYXRoPjwvZGVmcz48ZyAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMS4zMzMzMzMzLDAsMCwtMS4zMzMzMzMzLDAsMTU1LjY5MzMzKSIgICAgIGlkPSJnMTAiPjxnICAgICAgIHRyYW5zZm9ybT0ic2NhbGUoMC4xKSIgICAgICAgaWQ9ImcxMiI+PGcgICAgICAgICBpZD0iZzE0Ij48ZyAgICAgICAgICAgY2xpcC1wYXRoPSJ1cmwoI2NsaXBQYXRoMjApIiAgICAgICAgICAgaWQ9ImcxNiI+PHBhdGggICAgICAgICAgICAgaWQ9InBhdGgyMiIgICAgICAgICAgICAgc3R5bGU9ImZpbGw6I2ZmZmZmZjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIgICAgICAgICAgICAgZD0ibSAxMjI1LjcxLDExMzMuMjggdiAtMTMuOTkgaCAxMC42MyBjIDQuMDUsMCA3LjEyLDIuNjYgNy4xMiw2Ljk5IDAsNC42MiAtMy4wNyw3IC03LjEyLDcgeiBtIC0xMC45LDkuMDggaCAyMS45NSBjIDkuNSwwIDE3LjQ4LC01LjE3IDE3LjQ4LC0xNS45MyAwLC03Ljk4IC01LjYsLTEzLjAxIC0xMS44OSwtMTQuNTUgbCAxMy40MywtMTQuOTYgdiAtMi4xIGggLTExLjc1IGwgLTEzLjg0LDE2LjUxIGggLTQuNDggdiAtMTYuNTEgaCAtMTAuOSB6IG0gMTkuMTUsLTY0LjQ2IGMgMjMuMzUsMCA0MC41NSwxNy40OCA0MC41NSw0MC43IDAsMjMuMzUgLTE3LjIsNDAuODIgLTQwLjU1LDQwLjgyIC0yMy4yMSwwIC00MC4yNywtMTcuNDcgLTQwLjI3LC00MC44MiAwLC0yMy4yMiAxNy4wNiwtNDAuNyA0MC4yNywtNDAuNyB6IG0gMCw4OS43NyBjIDI4LjEsMCA0OS4wOCwtMjAuOTcgNDkuMDgsLTQ5LjA3IDAsLTI3LjgzIC0yMC45OCwtNDguOCAtNDkuMDgsLTQ4LjggLTI3LjgyLDAgLTQ4LjgsMjAuOTcgLTQ4LjgsNDguOCAwLDI4LjEgMjAuOTgsNDkuMDcgNDguOCw0OS4wNyB6IG0gLTk1Ni4yMywtOTU3LjUxIDYyLjYxOCw2Mi42MTcgYyA1Ni44MTYsLTQ0LjUxOSAxMjYuMTAxLC03My44MjggMjAxLjgyLC04MS42OTkgViA1NDIuMTY0IEggMTkxLjA3IGMgNy45MjYsLTc2LjI0MiAzNy41NzgsLTE0NS45NjkgODIuNjIxLC0yMDMuMDA4IGwgLTYyLjU5MywtNjIuNTk0IGMgLTYwLjYzNyw3My40NzMgLTEwMCwxNjUuMDkgLTEwOC41MTYsMjY1LjYwMiBMIDAsNTQyLjE2NCBWIDAgSCA1NDIuMTY4IFYgMTAyLjU3OCBDIDQ0Mi4xOCwxMTEuMDUxIDM1MS4wMDgsMTUwLjA1OSAyNzcuNzMsMjEwLjE2IFogbSAzNDYuNzY2LDk1Ni41IHYgLTEwMi41OCBjIDEwMC41MTIsLTguNTEgMTkyLjEyNSwtNDcuODggMjY1LjU5OCwtMTA4LjUwNiBsIC02Mi41NzgsLTYyLjU3OCBjIC01Ny4wMzksNDUuMDQ3IC0xMjYuNzcsNzQuNjk1IC0yMDMuMDIsODIuNjE3IFYgNjI0LjQ5NiBoIDM1MS4xMDYgYyAtNy44NjQsNzUuNzI3IC0zNy4xNzYsMTQ1LjAyIC04MS43MDQsMjAxLjg0NCBsIDYyLjU5OCw2Mi41OTggQyAxMDE2LjYsODE1LjY2IDEwNTUuNjEsNzI0LjQ4NCAxMDY0LjA4LDYyNC40OTYgaCAxMDIuNTggViAxMTY2LjY2IFogTSAzMzMuMjI3LDEwMzkuNzMgYyAwLDI0Ljg2IC0yMC4zMzYsNDUuMDEgLTQ1LjQyNiw0NS4wMSAtMjUuMDksMCAtNDUuNDE4LC0yMC4xNSAtNDUuNDE4LC00NS4wMSAwLC0yNC44NCAyMC4zMjgsLTQ0Ljk4IDQ1LjQxOCwtNDQuOTggMjUuMDksMCA0NS40MjYsMjAuMTQgNDUuNDI2LDQ0Ljk4IHogbSAyMDguOTQxLDEyNi45MyBWIDYyNC40OTYgSCAxOTAuOTU3IGMgMS45ODEsOTMuMTc2IDM0LjM3MSwxOTMuNjc2IDk2Ljg0LDI1NC43OTcgNjEuNjUyLC00Ny40MzggMTAxLjU1NSwtMTA5LjIwMyAxMTcuMjU4LC0xODcuMzA5IDE0LjQxLDAgNzAuMTY0LDAgOTAsMCAtMjIuNTU5LDEwOS4xODQgLTgzLjUzMiwxNzguOTA3IC0xNTYuNTI4LDIyNi40ODEgODcuMzE3LDY5LjQxOCAxMDQuMjg1LDE0Mi4xOTUgMTA0LjI4NSwxODAuNzE1IGggLTM0Ljg2NyBjIC01LjMwNCwtMzYuMiAtMjQuODA0LC05MS4yNyAtMTIwLjE0OCwtMTQ3LjE1NyAtMTAxLjAyNCw1OC42NDcgLTExNi40ODEsMTEwLjcwNyAtMTIwLjEzMywxNDcuMTU3IGggLTM0Ljg2NyBjIDAsLTQxLjQ3IDE3LjQzMywtMTE2LjExNCAxMDQuNTEyLC0xODAuNzMxIEMgMTQ3LjUxMiw4MjguNjg0IDExMC41OTQsNzE3LjQwNiAxMDIuNTMxLDYyNC40OTYgSCAwIFYgMTE2Ni42NiBaIE0gMTE2Ni42Niw1NDIuMTY0IFYgMCBIIDg0Ni41NDMgbCAtNjIuNjI5LDI1My4xODQgYyAyNS4yOTMsMzAuNTgyIDUxLjU5LDQ0LjY2OCAxMTQuMjIzLDU0LjQzNyAxMTYuNzYzLDE4LjIyMyAxMzcuNzMzLDExOS4zNDQgMTM3LjczMywxMTkuMzQ0IC01NC41MzQsLTEzLjExIC05OC4yMjUsLTAuMDUxIC0xNDkuNjU1LDEyLjQ5MiAtNTAuNzExLDEyLjM2NyAtOTkuMzk5LDExLjI1OCAtMTQ1LjgwNSwtNC45MjYgLTM1LjI0MiwtMTIuMjkzIC00My43NDYsLTIzLjc5NyAtNDMuNzQ2LC0yMy43OTcgTCA3OTguNTk0LDAgSCA2MjQuNDk2IHYgNTQyLjE2NCBoIDU0Mi4xNjQiIC8+PC9nPjwvZz48L2c+PC9nPjwvc3ZnPg==';
  	add_menu_page( 'Geocaching', 'Geocaching', 'manage_options', 'geocaching', 'my_geocaches', $icon_svg);
  	add_options_page( 'Geocaching Configuration', 'Geocaching', 'manage_options', 'geocaching-configuration', 'geocaching_options_page' );
  	
  	//call register settings function
    add_action( 'admin_init', 'register_geocaching_plugin_settings' );
  }
  
  function output_map()  {
    wp_enqueue_script('map');
    wp_enqueue_script('google-maps');
    wp_enqueue_style('style');
    
    $output = '<a href="https://www.geocaching.com/profile/?guid=' . get_option('geocaching_guid') . '" target="_blank"><img src="https://img.geocaching.com/stats/img.aspx?txt=View+my+profile&amp;uid=' . get_option('geocaching_guid') . '" alt="Geocaching Statistics"></a>';
    $output .= '<a href="https://www.sidetrackedseries.info" target="_blank"><img src="https://img.sidetrackedseries.info/awards/st_F_award.php?name=' . get_option('geocaching_username') . '&brand=jobs"></a>';
    $output .= '<div id="map"></div>';
    
    return $output;
  }
  add_shortcode('geocache_map', 'output_map');
  
  function register_geocaching_plugin_settings() {
  	//register our settings
  	register_setting( 'geocaching-settings-group', 'geocaching_google_maps_api_key' );
  	register_setting( 'geocaching-settings-group', 'geocaching_username' );
  	register_setting( 'geocaching-settings-group', 'geocaching_guid' );
  }
  
  function geocaching_options_page() {
?>
    <div class="wrap">
      <h2>Geocaching Plugin Options</h2>
      <form method="post" action="options.php">
        <?php settings_fields( 'geocaching-settings-group' ); ?>
        <?php do_settings_sections( 'geocaching-settings-group' ); ?>
        <table class="form-table">
          <tr valign="top">
            <th scope="row">Google Maps API Key</th>
              <td>
                <input type="text" name="geocaching_google_maps_api_key" value="<?php echo esc_attr( get_option('geocaching_google_maps_api_key') ); ?>" />
              </td>
          </tr>
          <tr valign="top">
            <th scope="row">Geocaching.com Username</th>
              <td>
                <input type="text" name="geocaching_username" value="<?php echo esc_attr( get_option('geocaching_username') ); ?>" />
              </td>
          </tr>
          <tr valign="top">
            <th scope="row">Geocaching.com Profile GUID</th>
              <td>
                <input type="text" name="geocaching_guid" value="<?php echo esc_attr( get_option('geocaching_guid') ); ?>" />
              </td>
          </tr>
        </table>
        
        <?php submit_button(); ?>
      </form>
    </div>
<?php
  }
?>