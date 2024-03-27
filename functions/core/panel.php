<?php
class HW_Settings_Page {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Start up
   */
  public function __construct() {
    $this->set_default_options();
    add_action('admin_menu', [$this, 'add_plugin_page']);
    add_action('admin_init', [$this, 'page_init']);
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
      [$this, 'create_admin_page'],
      '',
      2
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page() {
    $active_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'frontend';

    // Set class property
    $this->options['frontend'] = get_option('abb_options_frontend');
    if (!is_array($this->options['frontend'])) $this->options['frontend'] = [];

    $this->options['backend'] = get_option('abb_options_backend');
    if (!is_array($this->options['backend'])) $this->options['backend'] = [];

    $this->options['tinymce'] = get_option('abb_options_tinymce');
    if (!is_array($this->options['tinymce'])) $this->options['tinymce'] = [];

    $this->options['vendor'] = get_option('abb_options_vendor');
    if (!is_array($this->options['vendor'])) $this->options['vendor'] = [];

?>
    <div class="wrap">
      <h1>Paramétrage du site</h1>

      <h2 class="nav-tab-wrapper">
        <a href="?page=abb_options&amp;tab=frontend" class="nav-tab <?php echo $active_tab == 'frontend' ? 'nav-tab-active' : ''; ?>">Frontend</a>
        <a href="?page=abb_options&amp;tab=backend" class="nav-tab <?php echo $active_tab == 'backend' ? 'nav-tab-active' : ''; ?>">Backend</a>
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
      [$this, 'sanitize']
    );

    add_settings_section(
      'options_frontend_main',
      'Paramètres généraux',
      [$this, 'print_section_info'],
      'options_frontend'
    );

    add_settings_field(
      'show_admin_bar',
      'Barre d\'administration',
      [$this, 'checkbox_callback'],
      'options_frontend',
      'options_frontend_main',
      ['frontend', 'show_admin_bar', 'Activer la barre d\'administration de wordpress']
    );

    add_settings_field(
      'dev_mode',
      'Mode développement',
      [$this, 'checkbox_callback'],
      'options_frontend',
      'options_frontend_main',
      ['frontend', 'dev_mode', 'Activer le mode développement']
    );

    add_settings_field(
      'error_reporting',
      'Nivaux d\'erreurs',
      [$this, 'select_callback'],
      'options_frontend',
      'options_frontend_main',
      ['frontend', 'error_reporting', ['Ne rien afficher', 'Tout afficher', 'Afficher les erreurs', 'Afficher les avertissements', 'Afficher les notices', 'Afficher les déprécié']]
    );

    add_settings_section(
      'options_frontend_visibility',
      'Redirection',
      [$this, 'print_section_info'],
      'options_frontend'
    );

    add_settings_field(
      'hide_site',
      'Visibilité du site',
      [$this, 'checkbox_callback'],
      'options_frontend',
      'options_frontend_visibility',
      ['frontend', 'hide_site', 'Masquer le site aux utilisateurs non connectés.']
    );

    add_settings_field(
      'redirect_to',
      'Redirection',
      [$this, 'text_callback'],
      'options_frontend',
      'options_frontend_visibility',
      ['frontend', 'redirect_to', '']
    );

    add_settings_field(
      'allowed_urls',
      'Adresses autorisées',
      [$this, 'text_callback'],
      'options_frontend',
      'options_frontend_visibility',
      ['frontend', 'allowed_urls', 'Séparer les urls par des virgules']
    );

    //backend
    register_setting(
      'options_backend',
      'abb_options_backend',
      [$this, 'sanitize']
    );

    add_settings_section(
      'options_backend_main',
      'Paramètres généraux',
      [$this, 'print_section_info'],
      'options_backend'
    );

    add_settings_field(
      'disable_comments',
      'Commentaires',
      [$this, 'checkbox_callback'],
      'options_backend',
      'options_backend_main',
      ['backend', 'disable_comments', 'Désactiver les commentaires']
    );


    add_settings_section(
      'options_backend_hide',
      'Eléments à masquer',
      [$this, 'print_section_info'],
      'options_backend'
    );

    add_settings_field(
      'hide_screen_options',
      'Options d\'écran',
      [$this, 'checkbox_callback'],
      'options_backend',
      'options_backend_hide',
      ['backend', 'hide_screen_options', 'Masquer les options d\'écran']
    );

    add_settings_field(
      'hide_help',
      'Aide',
      [$this, 'checkbox_callback'],
      'options_backend',
      'options_backend_hide',
      ['backend', 'hide_help', 'Masquer l\'aide']
    );

    add_settings_field(
      'hide_metabox',
      'Masquage metabox',
      [$this, 'textarea_callback'],
      'options_backend',
      'options_backend_hide',
      ['backend', 'hide_metabox', '<small>Une metabox par ligne</small>']
    );

    add_settings_field(
      'hide_metabox_posttype',
      'Masquage metabox PT',
      [$this, 'textarea_callback'],
      'options_backend',
      'options_backend_hide',
      ['backend', 'hide_metabox_posttype', '<small>Réccursif pour chaque type de poste. Une metabox par ligne</small>']
    );

    add_settings_field(
      'hide_css',
      'Masquage CSS',
      [$this, 'textarea_callback'],
      'options_backend',
      'options_backend_hide',
      ['backend', 'hide_css', '<small>Une query CSS par ligne</small>']
    );

    //tinymce
    register_setting(
      'options_tinymce',
      'abb_options_tinymce',
      [$this, 'sanitize']
    );

    add_settings_section(
      'options_tinymce_main',
      'Paramètres',
      [$this, 'print_section_info'],
      'options_tinymce'
    );

    add_settings_field(
      'paste_as_text',
      'Coller',
      [$this, 'checkbox_callback'],
      'options_tinymce',
      'options_tinymce_main',
      ['tinymce', 'paste_as_text', 'Coller comme texte']
    );

    add_settings_field(
      'block_formats',
      'Formats',
      [$this, 'text_callback'],
      'options_tinymce',
      'options_tinymce_main',
      ['tinymce', 'block_formats', '']
    );

    add_settings_field(
      'style_formats',
      'Styles',
      [$this, 'textarea_callback'],
      'options_tinymce',
      'options_tinymce_main',
      ['tinymce', 'style_formats', '']
    );

    add_settings_section(
      'options_tinymce_toolbars',
      'Barres d\'outils',
      [$this, 'print_section_info'],
      'options_tinymce'
    );

    add_settings_field(
      'toolbars',
      'Barres d\'outils',
      [$this, 'textarea_callback'],
      'options_tinymce',
      'options_tinymce_toolbars',
      ['tinymce', 'toolbars', '<small>Une barre d\'outil par ligne.</small>']
    );

    //vendor
    register_setting(
      'options_vendor',
      'abb_options_vendor',
      [$this, 'sanitize']
    );

    add_settings_section(
      'options_vendor_externals',
      'Ressources externes',
      [$this, 'print_section_info'],
      'options_vendor'
    );

    add_settings_field(
      'externals_css',
      'CSS externes',
      [$this, 'textarea_callback'],
      'options_vendor',
      'options_vendor_externals',
      ['vendor', 'externals_css', '<strong>{template_directory}</strong>: ' . get_bloginfo('template_directory') . '<br><strong>{dev}:</strong> ?time=' . date('U') . '<br><small>Une URL par ligne</small>']
    );

    add_settings_field(
      'externals_scripts',
      'JS externes',
      [$this, 'textarea_callback'],
      'options_vendor',
      'options_vendor_externals',
      ['vendor', 'externals_scripts', '<strong>{template_directory}</strong>: ' . get_bloginfo('template_directory') . '<br><strong>{dev}:</strong> ?time' . date('U') . '<br><small>Une URL par ligne</small>']
    );
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize($input) {
    return $input;
  }

  public function set_default_options() {
    if (!get_option('abb_options_set')) {
      update_option('blogdescription', '');
      update_option('timezone_string', 'Europe/Zurich');
      update_option('default_pingback_flag', 0);
      update_option('thread_comments_depth', 1);
      update_option('default_ping_status', 'closed');
      update_option('default_comment_status', 'closed');
      update_option('thread_comments', 0);

      update_option('abb_options_set', true);

      update_option('abb_options_frontend', [
        'hide_site' => true,
        'dev_mode' => true,
        'error_reporting' => 1,
        'allowed_urls' => '/wp-admin/',
        'redirect_to' => '/wp-admin/'
      ]);

      update_option('dev_mode', true);

      update_option('abb_options_tinymce', [
        'toolbars' => "[\n  {\n    \"title\": \"Simple\",\n    \"data\": [\"formatselect\", \"styleselect\", \"bold\" , \"italic\" , \"bullist\", \"numlist\", \"outdent\", \"indent\", \"link\", \"unlink\", \"undo\", \"redo\", \"removeformat\"]\n  }, {\n    \"title\": \"Links\",\n    \"data\": [\"link\", \"unlink\", \"undo\", \"redo\", \"removeformat\"]\n  }\n]",
        'paste_as_text' => true,
        'block_formats' => 'Paragraphe=p;Sous-titre=h2',
        'style_formats' => "[\n  {\n    \"title\": \"Style 1\",\n    \"block\": \"p\",\n    \"classes\": \"\"\n  }\n]"
      ]);

      update_option('abb_options_backend', [
        'disable_comments' => true,
        'hide_screen_options' => true,
        'hide_metabox' => "itsec-dashboard-widget,dashboard,side,\nrg_forms_dashboard,dashboard, side\nlinkxfndiv,link,normal\nlinkadvanceddiv,link,normal'\ndashboard_quick_press,dashboard,side\ndashboard_plugins,dashboard,side\ndashboard_incoming_links,dashboard,side\ndashboard_recent_drafts,dashboard,side\nicl_dashboard_widget,dashboard,side\ndashboard_recent_comments,dashboard,normal\ndashboard_activity,dashboard,side\nwpseo-dashboard-overview,dashboard,side\ndashboard_incoming_links,dashboard,normal\ndashboard_primary,dashboard,side\ndashboard_secondary,dashboard,side\ndashboard_right_now,dashboard,side",
        'hide_metabox_posttype' => "categorydiv,side\ncommentsdiv,side\nrevisionsdiv,side\ncryptx,advanced\nrocket_post_exclude,side",
        'hide_css' => "#wp-admin-bar-WPML_ALS img\n.user-rich-editing-wrap\n.user-admin-color-wrap\n.user-comment-shortcuts-wrap\n.user-admin-bar-front-wrap\n.user-language-wrap\n.user-url-wrap\n.user-googleplus-wrap\n.user-twitter-wrap\n.user-facebook-wrap\n.user-description-wrap\n.setting[data-setting=\"description\"]\n.setting[data-setting=\"alt\"]\n.misc-pub-revisions\n#tagsdiv-client"
      ]);
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
    $option = isset($this->options[$args[0]][$args[1]]) ? $this->options[$args[0]][$args[1]] : false;

    echo '<input type="checkbox" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']" value="1" ' . checked(1, $option, false) . '>';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }

  public function text_callback($args) {
    $option = isset($this->options[$args[0]][$args[1]]) ? $this->options[$args[0]][$args[1]] : false;

    echo '<input class="large-text" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']" value="' . $option . '">';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }

  public function textarea_callback($args) {
    $option = isset($this->options[$args[0]][$args[1]]) ? $this->options[$args[0]][$args[1]] : false;

    echo '<textarea class="large-text code" rows="10" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']">' . $option . '</textarea>';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }

  public function textarea_readonly_callback($args) {
    $option = '';

    if (is_array($GLOBALS[$args[1]])) {
      foreach ($GLOBALS[$args[1]] as $entry) {
        $option .= $entry . "\n";
      }
    }

    echo '<textarea readonly class="large-text code" rows="10" id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']">' . $option . '</textarea>';
    echo '<label for="frontend_' . $args[1] . '">' . $args[2] . '</label>';
  }

  public function select_callback($args) {
    $option = isset($this->options[$args[0]][$args[1]]) ? $this->options[$args[0]][$args[1]] : false;

    echo '<select id="' . $args[0] . '_' . $args[1] . '" name="abb_options_' . $args[0] . '[' . $args[1] . ']">';
    foreach ($args[2] as $key => $value) {
      echo '<option value="' . $key . '" ' . selected($key, $option, false) . '>' . $value . '</option>';
    }
    echo '</select>';
  }
}

if (is_admin() && current_user_can('administrator')) new HW_Settings_Page();
