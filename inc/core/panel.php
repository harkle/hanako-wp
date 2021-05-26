<?php
class MySettingsPage {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Start up
   */
  public function __construct() {
    $this->set_default_options();
    add_action('admin_menu', array( $this, 'add_plugin_page'));
    add_action('admin_init', array( $this, 'page_init'));
  }

  /**
   * Add options page
   */
  public function add_plugin_page() {
    add_menu_page(
      'Settings Admin',
      'Configuration',
      'manage_options',
      'abb_options',
      array($this, 'create_admin_page'),
      '',
      2
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page() {
    $active_tab = (isset($_GET[ 'tab' ])) ? $_GET[ 'tab' ] : 'frontend';

    // Set class property
    $this->options['frontend'] = get_option('abb_options_frontend');
    if (!is_array($this->options['frontend'])) $this->options['frontend'] = array();

    $this->options['backend'] = get_option('abb_options_backend');
    if (!is_array($this->options['backend'])) $this->options['backend'] = array();

    $this->options['cpt'] = get_option('abb_options_cpt');
    if (!is_array($this->options['cpt'])) $this->options['cpt'] = array();

    $this->options['tinymce'] = get_option('abb_options_tinymce');
    if (!is_array($this->options['tinymce'])) $this->options['tinymce'] = array();

    $this->options['vendor'] = get_option('abb_options_vendor');
    if (!is_array($this->options['vendor'])) $this->options['vendor'] = array();

    ?>
      <div class="wrap">
        <h1>Paramétrage du site</h1>

        <h2 class="nav-tab-wrapper">
          <a href="?page=abb_options&amp;tab=frontend" class="nav-tab <?php echo $active_tab == 'frontend' ? 'nav-tab-active' : ''; ?>">Frontend</a>
          <a href="?page=abb_options&amp;tab=backend" class="nav-tab <?php echo $active_tab == 'backend' ? 'nav-tab-active' : ''; ?>">Backend</a>
          <a href="?page=abb_options&amp;tab=cpt" class="nav-tab <?php echo $active_tab == 'cpt' ? 'nav-tab-active' : ''; ?>">Post Types</a>
          <a href="?page=abb_options&amp;tab=tinymce" class="nav-tab <?php echo $active_tab == 'tinymce' ? 'nav-tab-active' : ''; ?>">TinyMCE</a>
          <a href="?page=abb_options&amp;tab=vendor" class="nav-tab <?php echo $active_tab == 'vendor' ? 'nav-tab-active' : ''; ?>">Vendor</a>
        </h2>

        <form method="post" action="options.php">
          <?php
            switch ($active_tab) {
              case 'frontend':
                $this->panel_frontend();
                break;
              case 'backend':
                $this->panel_backend();
                break;
              case 'cpt':
                $this->panel_cpt();
                break;
              case 'tinymce':
                $this->panel_tinymce();
                break;
              case 'vendor':
                $this->panel_vendor();
                break;
              }

            if ($active_tab != 'home') submit_button();
          ?>
          </form>
      </div>
    <?php
  }

  private function panel_frontend() {
    settings_fields('options_frontend');
    do_settings_sections('options_frontend');
  }

  private function panel_backend() {
    settings_fields('options_backend');
    do_settings_sections('options_backend');
  }

  private function panel_cpt() {
    settings_fields('options_cpt');
    do_settings_sections('options_cpt');
  }

  private function panel_tinymce() {
    settings_fields('options_tinymce');
    do_settings_sections('options_tinymce');
  }

  private function panel_vendor() {
    settings_fields('options_vendor');
    do_settings_sections('options_vendor');
  }

  /**
   * Register and add settings
   */
  public function page_init() {
    register_setting(
      'options_frontend',
      'abb_options_frontend',
      array( $this, 'sanitize' )
    );

    add_settings_section (
      'options_frontend_main',
      'Paramètres généraux',
      array($this, 'print_section_info'),
      'options_frontend'
    );

    add_settings_field(
      'hide_site',
      'Visibilité du site',
      array( $this, 'checkbox_callback' ),
      'options_frontend',
      'options_frontend_main',
       array('frontend', 'hide_site', 'Masquer le site aux utilisateurs non connectés.')
    );

    add_settings_field(
      'show_admin_bar',
      'Barre d\'administration',
      array( $this, 'checkbox_callback' ),
      'options_frontend',
      'options_frontend_main',
       array('frontend', 'show_admin_bar', 'Activer la barre d\'administration de wordpress')
    );

    add_settings_field(
      'debug',
      'Mode debug',
      array( $this, 'checkbox_callback' ),
      'options_frontend',
      'options_frontend_debug',
      array('frontend', 'debug', 'Activer le mode debug <small>(Ajoute la classe "debug" au tag body)</small>')
    );

    //backend
    register_setting(
      'options_backend',
      'abb_options_backend',
      array( $this, 'sanitize' )
    );

    add_settings_section (
      'options_backend_main',
      'Paramètres généraux',
      array($this, 'print_section_info'),
      'options_backend'
    );

    add_settings_field(
      'theme_support_menus',
      'Menu',
      array( $this, 'checkbox_callback' ),
      'options_backend',
      'options_backend_main',
       array('backend', 'theme_support_menus', 'Activer le support des menus')
    );

    add_settings_field(
      'add_acf_options',
      'Options ACF',
      array( $this, 'checkbox_callback' ),
      'options_backend',
      'options_backend_main',
       array('backend', 'add_acf_options', 'Activer la page d\'option ACF')
    );

    add_settings_section (
      'options_backend_medias',
      'Recadrage des médias',
      array($this, 'print_section_info'),
      'options_backend'
    );

    add_settings_field(
      'activate_medium_crop',
      'Moyen',
      array( $this, 'checkbox_callback' ),
      'options_backend',
      'options_backend_medias',
       array('backend', 'activate_medium_crop', 'Activer le recadrage des médias de taille moyenne')
    );

    add_settings_field(
      'activate_large_crop',
      'Large',
      array( $this, 'checkbox_callback' ),
      'options_backend',
      'options_backend_medias',
      array('backend', 'activate_large_crop', 'Activer le recadrage des médias de taille large')
    );

    add_settings_section (
      'options_backend_hide',
      'Eléments à masquer',
      array($this, 'print_section_info'),
      'options_backend'
    );

    add_settings_field(
      'hide_screen_options',
      'Options d\'écran',
      array( $this, 'checkbox_callback' ),
      'options_backend',
      'options_backend_hide',
       array('backend', 'hide_screen_options', 'Masquer les options d\'écran')
    );

    add_settings_field(
      'hide_help',
      'Aide',
      array( $this, 'checkbox_callback' ),
      'options_backend',
      'options_backend_hide',
       array('backend', 'hide_help', 'Masquer l\'aide')
    );

    add_settings_field (
      'hide_metabox',
      'Masquage metabox',
      array( $this, 'textarea_callback' ),
      'options_backend',
      'options_backend_hide',
       array('backend', 'hide_metabox', '<small>Une metabox par ligne</small>')
    );

    add_settings_field (
      'hide_metabox_posttype',
      'Masquage metabox PT',
      array( $this, 'textarea_callback' ),
      'options_backend',
      'options_backend_hide',
       array('backend', 'hide_metabox_posttype', '<small>Réccursif pour chaque type de poste. Une metabox par ligne</small>')
    );

    add_settings_field (
      'hide_css',
      'Masquage CSS',
      array( $this, 'textarea_callback' ),
      'options_backend',
      'options_backend_hide',
       array('backend', 'hide_css', '<small>Une query CSS par ligne</small>')
    );

    //cpt
    register_setting (
      'options_cpt',
      'abb_options_cpt',
      array( $this, 'sanitize' )
    );

    add_settings_section (
      'options_cpt_cpt',
      'Post types',
      array($this, 'print_section_info'),
      'options_cpt'
    );

    add_settings_field (
      'post_types',
      'Liste des type de postes',
      array( $this, 'textarea_callback' ),
      'options_cpt',
      'options_cpt_cpt',
       array('cpt', 'post_types', "<pre>[{\n  \"name\": \"postype_name\",\n  \"data\": {\n    \"labels\": {\n      \"name\": \"Pluriel\",\n      \"singular_name\": \"Singulier\"\n      },\n    \"public\": true,\n    \"has_archive\": true,\n    \"menu_icon\": \"dashicons-icon\",\n    \"supports\": [\"title\"]\n  }\n}]</pre>")
    );


    add_settings_section (
      'options_cpt_tax',
      'Taxnomies',
      array($this, 'print_section_info'),
      'options_cpt'
    );


    add_settings_field (
      'taxnomies',
      'Liste des taxonomies',
      array( $this, 'textarea_callback' ),
      'options_cpt',
      'options_cpt_tax',
       array('cpt', 'taxnomies', "<pre>[{\n  \"name\": \"taxonomy_name\",\n  \"post_type\": \"posttype_name\",\n  \"data\": {\n    \"label\": \"Nom\",\n    \"hierarchical\": true\n  }\n}]</pre>")
    );

    //tinymce
    register_setting (
      'options_tinymce',
      'abb_options_tinymce',
      array( $this, 'sanitize' )
    );

    add_settings_section (
      'options_tinymce_main',
      'Paramètres',
      array($this, 'print_section_info'),
      'options_tinymce'
    );

    add_settings_field (
      'paste_as_text',
      'Coller',
      array( $this, 'checkbox_callback' ),
      'options_tinymce',
      'options_tinymce_main',
       array('tinymce', 'paste_as_text', 'Coller comme texte')
    );

    add_settings_field (
      'block_formats',
      'Formats',
      array( $this, 'text_callback' ),
      'options_tinymce',
      'options_tinymce_main',
       array('tinymce', 'block_formats', '')
    );

    add_settings_field (
      'style_formats',
      'Styles',
      array( $this, 'textarea_callback' ),
      'options_tinymce',
      'options_tinymce_main',
       array('tinymce', 'style_formats', '')
    );

    add_settings_section (
      'options_tinymce_toolbars',
      'Barres d\'outils',
      array($this, 'print_section_info'),
      'options_tinymce'
    );

    add_settings_field (
      'toolbars',
      'Barres d\'outils',
      array( $this, 'textarea_callback' ),
      'options_tinymce',
      'options_tinymce_toolbars',
       array('tinymce', 'toolbars', '<small>Une barre d\'outil par ligne.</small>')
    );

    //vendor
    register_setting(
      'options_vendor',
      'abb_options_vendor',
      array( $this, 'sanitize' )
    );

    add_settings_section (
      'options_vendor_externals',
      'Ressources externes',
      array($this, 'print_section_info'),
      'options_vendor'
    );

    add_settings_field(
      'externals_css',
      'CSS externes',
      array($this, 'textarea_callback' ),
      'options_vendor',
      'options_vendor_externals',
       array('vendor', 'externals_css', '{template_directory}<br><small>Une URL par ligne</small>')
    );

    add_settings_field(
      'externals_scripts',
      'JS externes',
      array($this, 'textarea_callback' ),
      'options_vendor',
      'options_vendor_externals',
       array('vendor', 'externals_scripts', '{template_directory}<br><small>Une URL par ligne</small>')
    );
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize( $input ) {
    return $input;
  }

  public function set_default_options() {
    if (!get_option('abb_options_set')) {
      update_option('blogdescription', '');
      update_option('timezone_string', 'Europe/Zurich');
      update_option('default_pingback_flag', 0);
      update_option('thread_comments_depth', 1);
      update_option('default_ping_status', "closed");
      update_option('default_comment_status', "closed");
      update_option('thread_comments', 0);

      update_option('abb_options_set', true);

      update_option('abb_options_frontend', array(
        'hide_site' => true
      ));

      update_option('abb_options_cpt', array(
        'post_types' => "[]",
        'taxnomies' => "[]"
      ));

      update_option('abb_options_tinymce', array(
        'toolbars' => "[\n  {\n    \"title\": \"Simple\",\n    \"data\": [\"formatselect\", \"styleselect\", \"bold\" , \"italic\" , \"bullist\", \"numlist\", \"outdent\", \"indent\", \"link\", \"unlink\", \"undo\", \"redo\", \"removeformat\"]\n  }, {\n    \"title\": \"Links\",\n    \"data\": [\"link\", \"unlink\", \"undo\", \"redo\", \"removeformat\"]\n  }\n]",
        'paste_as_text' => true,
        'block_formats' => 'Paragraphe=p;Sous-titre=h2',
        'style_formats' => "[\n  {\n    \"title\": \"Style 1\",\n    \"block\": \"p\",\n    \"classes\": \"\"\n  }\n]"
      ));

      update_option('abb_options_backend', array(
        'theme_support_menus' => true,
        'hide_screen_options' => true,
        'hide_metabox' => "itsec-dashboard-widget,dashboard,side,\nrg_forms_dashboard,dashboard, side\nlinkxfndiv,link,normal\nlinkadvanceddiv,link,normal'\ndashboard_quick_press,dashboard,side\ndashboard_plugins,dashboard,side\ndashboard_incoming_links,dashboard,side\ndashboard_recent_drafts,dashboard,side\nicl_dashboard_widget,dashboard,side\ndashboard_recent_comments,dashboard,normal\ndashboard_activity,dashboard,side\nwpseo-dashboard-overview,dashboard,side\ndashboard_incoming_links,dashboard,normal\ndashboard_primary,dashboard,side\ndashboard_secondary,dashboard,side\ndashboard_right_now,dashboard,side",
        'hide_metabox_posttype' => "categorydiv,side\ncommentsdiv,side\nrevisionsdiv,side\ncryptx,advanced\nrocket_post_exclude,side",
        'hide_css' => "#wp-admin-bar-WPML_ALS img\n.user-rich-editing-wrap\n.user-admin-color-wrap\n.user-comment-shortcuts-wrap\n.user-admin-bar-front-wrap\n.user-language-wrap\n.user-url-wrap\n.user-googleplus-wrap\n.user-twitter-wrap\n.user-facebook-wrap\n.user-description-wrap\n.setting[data-setting=\"description\"]\n.setting[data-setting=\"alt\"]\n.misc-pub-revisions\n#tagsdiv-client"
      ));
    }
  }

  /**
   * Print the Section text
   */
  public function print_section_info() {

  }

  /**
   * Get the settings option array and print one of its values
   */
  public function checkbox_callback($args) {
    echo '<input type="checkbox" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']" value="1" ' . checked(1, $this->options[$args[0]][$args[1]], false ) . '>';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }

  public function text_callback($args) {
    echo '<input class="large-text" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']" value="' . $this->options[$args[0]][$args[1]] . '">';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }

  public function textarea_callback($args) {
    echo '<textarea class="large-text code" rows="10" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']">' . $this->options[$args[0]][$args[1]] . '</textarea>';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }
}

if(is_admin() && current_user_can('administrator')) new MySettingsPage();
?>