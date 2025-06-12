<?php
/**
 * Gestionnaire d'assets Webpack pour Hanako WP
 * Place ce fichier dans functions/webpack-assets.php
 */

class WebpackAssets {
    private static $manifest = null;
    private static $chunks = null;

    /**
     * Charge le manifeste des assets
     */
    private static function loadManifest() {
        if (self::$manifest === null) {
            $manifest_path = get_template_directory() . '/dist/manifest.json';
            if (file_exists($manifest_path)) {
                self::$manifest = json_decode(file_get_contents($manifest_path), true);
            } else {
                self::$manifest = [];
            }
        }
        return self::$manifest;
    }

    /**
     * Récupère l'URL d'un asset
     */
    public static function getAsset($name) {
        $manifest = self::loadManifest();
        $base_url = get_template_directory_uri() . '/dist/';

        return isset($manifest[$name]) ? $base_url . $manifest[$name] : false;
    }

    /**
     * Enqueue un entry point avec toutes ses dépendances
     */
    public static function enqueueEntry($entry_name, $dependencies = []) {
        $manifest = self::loadManifest();
        $base_url = get_template_directory_uri() . '/dist/';

        // CSS du entry point
        $css_key = $entry_name . '.css';
        if (isset($manifest[$css_key])) {
            wp_enqueue_style(
                'hanako-' . $entry_name,
                $base_url . $manifest[$css_key],
                [],
                self::getVersion($css_key)
            );
        }

        // JS - d'abord les vendors
        self::enqueueVendors();

        // Puis le entry point principal
        $js_key = $entry_name . '.js';
        if (isset($manifest[$js_key])) {
            wp_enqueue_script(
                'hanako-' . $entry_name,
                $base_url . $manifest[$js_key],
                array_merge(['hanako-vendors'], $dependencies),
                self::getVersion($js_key),
                false
            );
        }
    }

    /**
     * Enqueue les vendors (runtime, vendors, bootstrap, hanako)
     */
    private static function enqueueVendors() {
        $manifest = self::loadManifest();
        $base_url = get_template_directory_uri() . '/dist/';

        $vendors = ['runtime', 'vendors', 'bootstrap', 'hanako'];

        foreach ($vendors as $vendor) {
            $js_key = $vendor . '.js';
            if (isset($manifest[$js_key])) {
                wp_enqueue_script(
                    'hanako-' . $vendor,
                    $base_url . $manifest[$js_key],
                    [],
                    self::getVersion($js_key),
                    false
                );
            }
        }
    }

    /**
     * Récupère la version d'un asset (hash du fichier)
     */
    private static function getVersion($asset_name) {
        // Si c'est un fichier avec hash, pas besoin de version
        if (preg_match('/\.[a-f0-9]{8}\./', $asset_name)) {
            return null;
        }

        // Sinon, utilise le timestamp en dev
        return WP_DEBUG ? filemtime(get_template_directory() . '/dist/' . self::$manifest[$asset_name] ?? '') : '1.0';
    }
}

/**
 * Helper function pour Twig
 */
function webpack_asset($name) {
    return WebpackAssets::getAsset($name);
}

/**
 * Enqueue les scripts principaux
 */
function hanako_enqueue_assets() {
    // Page principale
    if (!is_admin()) {
        WebpackAssets::enqueueEntry('main');
    }

    // Admin si nécessaire
    if (is_admin()) {
        WebpackAssets::enqueueEntry('admin');
    }

    // Editor styles pour Gutenberg
    if (function_exists('add_theme_support')) {
        add_theme_support('editor-styles');
        add_editor_style(WebpackAssets::getAsset('editor-style.css'));
    }
}

add_action('wp_enqueue_scripts', 'hanako_enqueue_assets');
add_action('admin_enqueue_scripts', 'hanako_enqueue_assets');
