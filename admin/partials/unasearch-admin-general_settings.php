<?php
/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the plugin settings page.
 *
 * @link       http://unacode.com/unasearch
 * @since      0.1.0
 *
 * @package    Unasearch
 * @subpackage Unasearch/admin/partials
 */

/**
 * Options Page
 *
 * Renders the settings page contents.
 *
 * @since       0.1.0
*/

?>

<div class="wrap">
  <h2><?php echo esc_html( get_admin_page_title() ); ?> </h2>

  <?php settings_errors( $this->plugin_name . '-notices' ); ?>

  <h2 class="nav-tab-wrapper">
    <?php
    foreach( $tabs as $tab_slug => $tab_name ) {

      $tab_url = add_query_arg( array(
        'settings-updated' => false,
        'tab' => $tab_slug
        ) );

      $active = $active_tab == $tab_slug ? ' nav-tab-active' : '';

      echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
      echo esc_html( $tab_name );
      echo '</a>';
    }
    ?>
  </h2>

  <div>

    <div id="post-body">

        <div id="postbox-container" class="postbox-container">

          <form action="options.php" method="POST">
          
            <?php
            settings_fields( 'display_unasearch_admin_page_settings' );
            do_settings_sections( 'display_unasearch_admin_page_settings' );
            ?>

            <?php submit_button(); ?>

          </form>

        </div><!-- #postbox-container-->

    </div><!-- #post-body-->

  </div><!-- #poststuff-->
</div><!-- .wrap -->