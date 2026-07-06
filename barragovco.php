<?php
/**
 * Plugin Name:       Barra GOV.CO
 * Plugin URI:        https://www.gov.co/
 * Description:       Integra la barra superior y la barra azul inferior oficiales de GOV.CO (Kit UI 9.2) en cualquier sitio WordPress, cumpliendo con los lineamientos de identidad visual del Estado Colombiano.
 * Version:           1.0.8
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            Valor Mas S.A.S
 * Author URI:        https://valormas.gov.co/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       barra-govco
 * Domain Path:       /languages
 *
 * @package BarraGovCo
 */

/*
 * -----------------------------------------------------------------------------
 *  Cabecera del plugin: evita el acceso directo al archivo.
 * -----------------------------------------------------------------------------
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * -----------------------------------------------------------------------------
 *  Constantes del plugin.
 * -----------------------------------------------------------------------------
 */
if ( ! defined( 'BARRA_GOVCO_VERSION' ) ) {
    define( 'BARRA_GOVCO_VERSION', '1.0.8' );
}
if ( ! defined( 'BARRA_GOVCO_PLUGIN_FILE' ) ) {
    define( 'BARRA_GOVCO_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BARRA_GOVCO_PLUGIN_DIR' ) ) {
    define( 'BARRA_GOVCO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'BARRA_GOVCO_PLUGIN_URL' ) ) {
    define( 'BARRA_GOVCO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}


/**
 * Encola los estilos y scripts oficiales de GOV.CO v5.
 *
 * @since 1.0.0
 * @return void
 */
function bgc_enqueue_assets() {
    // Estilos oficiales de la versión 5 de GOV.CO.
    wp_enqueue_style(
        'govco-v5-styles',
        'https://cdn.www.gov.co/v5/css/govco.css',
        array(),
        '5.0'
    );

    // Scripts oficiales de la versión 5 de GOV.CO.
    wp_enqueue_script(
        'govco-v5-scripts',
        'https://cdn.www.gov.co/v5/js/govco.js',
        array(),
        '5.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'bgc_enqueue_assets' );


/**
 * Devuelve el HTML de la barra superior de GOV.CO como cadena.
 *
 * Construido por concatenación para evitar ob_start anidados (que pueden
 * provocar WSOD en combinación con el callback de buffer principal).
 *
 * @since 1.0.2
 * @since 1.0.3 Refactor: concatenación en lugar de ob_start anidado.
 * @return string
 */
function bgc_get_top_bar_html() {
    $logo_url = esc_url( BARRA_GOVCO_PLUGIN_URL . 'logos/logoGovCO.png' );

    // Estilos inline. position:fixed + z-index máximo asegura que la barra
    // queda por encima de cualquier header fijo/sticky del tema.
    // Se añade body.govco-has-topbar { padding-top: 56px } vía JS para evitar
    // que el header del tema quede oculto bajo la barra fija.
    $html  = '<!-- Barra Superior GOV.CO - Lineamientos Oficiales Kit UI 9.2 -->';
    $html .= '<div id="govco-header-topbar" style="background-color: #0943B5 !important; height: 56px !important; width: 100% !important; display: flex !important; align-items: center !important; padding: 0 16px !important; box-sizing: border-box !important; z-index: 2147483647 !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; margin: 0 !important; border: none !important; float: none !important;">';
    $html .= '<div style="max-width: 1200px !important; width: 100% !important; margin: 0 auto !important; display: flex !important; align-items: center !important; justify-content: flex-start !important; height: 100% !important; border: none !important; padding: 0 !important;">';
    $html .= '<a href="https://www.gov.co/" target="_blank" rel="noopener noreferrer" style="display: flex !important; align-items: center !important; min-width: 44px !important; min-height: 44px !important; justify-content: center !important; text-decoration: none !important; border: none !important; padding: 0 !important; margin: 0 !important; background: none !important; box-shadow: none !important;" aria-label="Portal Único del Estado Colombiano - GOV.CO">';
    $html .= '<img src="' . $logo_url . '" alt="Logo GOV.CO" style="width: 136px !important; height: 24px !important; max-width: 136px !important; max-height: 24px !important; min-width: 136px !important; min-height: 24px !important; display: block !important; border: none !important; padding: 0 !important; margin: 0 !important; box-shadow: none !important; object-fit: contain !important; aspect-ratio: 136 / 24 !important;">';
    $html .= '</a>';
    $html .= '</div>';
    $html .= '</div>';
    // Empujamos el contenido del tema 56px hacia abajo para que no quede
    // oculto bajo la barra fija. Se aplica solo si el body no tiene ya un
    // padding-top que cubra la barra (detectado por la altura 56px).
    $html .= '<style id="govco-topbar-style">body{padding-top:56px !important; box-sizing:border-box;}html{scroll-padding-top:56px;}</style>';

    return $html;
}

/**
 * Renderiza la barra superior cuando el tema invoca wp_body_open().
 *
 * @since 1.0.0
 * @since 1.0.2 Refactorizado: delega en bgc_get_top_bar_html().
 * @return void
 */
function bgc_render_top_bar() {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo bgc_get_top_bar_html();
}

/**
 * Inyecta la barra superior justo después de <body> mediante output buffering.
 *
 * Cubre temas que NO llaman wp_body_open() (BeTheme, builders, etc.).
 * Es tolerante a fallos: cualquier error devuelve el buffer original,
 * nunca una cadena vacía (evita WSOD).
 *
 * @since 1.0.2
 * @since 1.0.3 Blindado contra retornos null/false y buffers no-string.
 * @param mixed $buffer HTML capturado por ob_start.
 * @return string HTML (modificado o intacto).
 */
function bgc_inject_top_bar_into_body( $buffer ) {
    if ( ! is_string( $buffer ) || '' === $buffer ) {
        return $buffer;
    }
    // Si wp_body_open() ya inyectó la barra, no duplicar.
    if ( false !== strpos( $buffer, 'id="govco-header-topbar"' ) ) {
        return $buffer;
    }
    // Solo actuar si vemos una etiqueta <body ...>.
    if ( false === strpos( $buffer, '<body' ) ) {
        return $buffer;
    }

    $top_bar = bgc_get_top_bar_html();
    $result  = preg_replace( '/(<body\b[^>]*>)/i', '$1' . $top_bar, $buffer, 1 );

    // preg_replace devuelve null ante error en PHP 8+. En ese caso, devolvemos
    // el buffer intacto en vez de propagar el null (que podría blankear la página).
    if ( null === $result ) {
        return $buffer;
    }

    return $result;
}

/**
 * Activa el buffer de salida en el front-end para inyectar la barra superior.
 *
 * Solo se inicia si:
 *  - No es admin / AJAX / cron / REST / WP-CLI.
 *  - No se han enviado headers todavía.
 *  - No hay ya un buffer activo de otro plugin/tema (evita anidamientos frágiles).
 *
 * @since 1.0.2
 * @since 1.0.3 Añadidos guards headers_sent() y ob_get_level().
 * @return void
 */
function bgc_start_output_buffer() {
    if ( is_admin() ) {
        return;
    }
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }
    if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
        return;
    }
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return;
    }
    if ( headers_sent() ) {
        return;
    }

    ob_start( 'bgc_inject_top_bar_into_body' );
}

add_action( 'wp_body_open', 'bgc_render_top_bar', 1 );
add_action( 'template_redirect', 'bgc_start_output_buffer' );

/**
 * Fallback en JavaScript para inyectar la barra superior en el frontend.
 *
 * Capa de seguridad #3: si wp_body_open() y el output buffering no lograron
 * inyectar la barra (por temas exóticos, plugins de caché que alteran buffers,
 * o reglas CSS de tema que la ocultan), este script la crea dinámicamente
 * y la posiciona con position:fixed + z-index máximo.
 *
 * Se ejecuta en wp_footer con prioridad 1 (antes que la mayoría de scripts).
 *
 * @since 1.0.5
 * @since 1.0.6 Añadida lógica para ajustar headers sticky/fixed del tema.
 * @since 1.0.7 (Retirado en 1.0.8) Comportamiento auto-hide al hacer scroll.
 * @since 1.0.8 Se elimina toda la lógica de auto-hide, transición y scroll
 *               listener. La barra vuelve a ser `position: fixed; top: 0`
 *               siempre visible, sin transformaciones ni efectos sticky.
 *               Se conserva el ajuste de headers sticky del tema (1.0.6).
 * @return void
 */
function bgc_topbar_js_fallback() {
    if ( is_admin() ) {
        return;
    }
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }
    if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
        return;
    }
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    $logo_url = esc_url( BARRA_GOVCO_PLUGIN_URL . 'logos/logoGovCO.png' );
    ?>
    <script id="govco-topbar-fallback">
    (function(){
        var GOVCO_BAR_HEIGHT = 56;

        function ensureBar(){
            var bar = document.getElementById('govco-header-topbar');
            if (bar) {
                bar.style.setProperty('position', 'fixed', 'important');
                bar.style.setProperty('top', '0', 'important');
                bar.style.setProperty('left', '0', 'important');
                bar.style.setProperty('right', '0', 'important');
                bar.style.setProperty('z-index', '2147483647', 'important');
                return bar;
            }

            var wrapper = document.createElement('div');
            wrapper.innerHTML = '<div id="govco-header-topbar" style="background-color:#0943B5 !important;height:56px !important;width:100% !important;display:flex !important;align-items:center !important;padding:0 16px !important;box-sizing:border-box !important;z-index:2147483647 !important;position:fixed !important;top:0 !important;left:0 !important;right:0 !important;margin:0 !important;border:none !important;float:none !important;"><div style="max-width:1200px !important;width:100% !important;margin:0 auto !important;display:flex !important;align-items:center !important;justify-content:flex-start !important;height:100% !important;border:none !important;padding:0 !important;"><a href="https://www.gov.co/" target="_blank" rel="noopener noreferrer" style="display:flex !important;align-items:center !important;min-width:44px !important;min-height:44px !important;justify-content:center !important;text-decoration:none !important;border:none !important;padding:0 !important;margin:0 !important;background:none !important;box-shadow:none !important;" aria-label="Portal Único del Estado Colombiano - GOV.CO"><img src="<?php echo $logo_url; ?>" alt="Logo GOV.CO" style="width:136px !important;height:24px !important;display:block !important;border:none !important;padding:0 !important;margin:0 !important;box-shadow:none !important;object-fit:contain !important;"></a></div></div>';

            var created = wrapper.firstElementChild;
            if (created && document.body) {
                document.body.insertBefore(created, document.body.firstChild);
            }
            return created;
        }

        /**
         * Detecta headers/menus del tema con position: fixed o sticky y
         * les aplica top: 56px para que se peguen debajo de la barra GOV.CO.
         */
        function adjustStickyHeaders(){
            try {
                var selectors = [
                    'header',
                    '.header',
                    '.site-header',
                    '.site-navigation',
                    '#masthead',
                    '.elementor-location-header',
                    '.elementor-sticky',
                    '.is-sticky',
                    '.sticky-header',
                    '[data-sticky="true"]',
                    '[class*="sticky"]',
                    '[class*="fixed-header"]',
                    '.mfn-header-tmpl',
                    '.mfn-header',
                    '#Header_wrapper',
                    '.tf_sticky'
                ].join(',');

                var elements = document.querySelectorAll(selectors);
                for (var i = 0; i < elements.length; i++) {
                    var el = elements[i];
                    if (el.id === 'govco-header-topbar') { continue; }
                    if (el.closest && el.closest('#govco-header-topbar')) { continue; }

                    var style = window.getComputedStyle(el);
                    var position = style.position;
                    if (position !== 'fixed' && position !== 'sticky') { continue; }

                    var currentTop = parseFloat(style.top);
                    if (!currentTop || currentTop < GOVCO_BAR_HEIGHT) {
                        el.style.setProperty('top', GOVCO_BAR_HEIGHT + 'px', 'important');
                    }
                }
            } catch (e) {
                /* Silenciar errores para no afectar otras funciones. */
            }
        }

        function init(){
            ensureBar();
            document.body.style.paddingTop = GOVCO_BAR_HEIGHT + 'px';
            document.documentElement.style.scrollPaddingTop = GOVCO_BAR_HEIGHT + 'px';
            adjustStickyHeaders();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        // Re-aplicar en eventos clave para capturar headers inicializados tarde
        // (BeTheme, Elementor, Muffin Builder suelen añadir clases tras load).
        function reapply(){
            ensureBar();
            adjustStickyHeaders();
        }

        window.addEventListener('load', reapply);
        setTimeout(reapply, 100);
        setTimeout(reapply, 500);
        setTimeout(reapply, 1500);
        setTimeout(reapply, 3000);
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'bgc_topbar_js_fallback', 1 );


/**
 * Renderiza la barra azul inferior con el logo GOV.CO y la Marca País Colombia.
 *
 * @since 1.0.0
 * @return void
 */
function bgc_render_bottom_bar() {
    $logo_govco_url    = BARRA_GOVCO_PLUGIN_URL . 'logos/logoGovCO.png';
    $logo_colombia_url = BARRA_GOVCO_PLUGIN_URL . 'logos/logocolombia.png';
    ?>
    <!-- Barra Azul Inferior GOV.CO - Lineamientos Oficiales Kit UI 9.2 -->
    <div id="govco-final-bar" style="width: 100% !important; background-color: #0943B5 !important; padding: 12px 0 !important; box-sizing: border-box !important; display: flex !important; align-items: center !important; margin: 0em !important; border: none !important; clear: both !important; float: none !important;">
        <div style="max-width: 1200px !important; width: 100% !important; margin: 0 auto !important; padding: 0 24px !important; display: flex !important; align-items: center !important; justify-content: flex-start !important; box-sizing: border-box !important; gap: 0px !important;">

            <a href="https://www.gov.co/" target="_blank" rel="noopener noreferrer" style="display: flex !important; align-items: center !important; justify-content: center !important; text-decoration: none !important; padding: 0em !important; margin: 0em !important; background: none !important; box-shadow: none !important;" aria-label="Portal Único del Estado Colombiano - GOV.CO">
                <img src="<?php echo esc_url( $logo_govco_url ); ?>"
                     alt="Logo GOV.CO"
                     style="height: 24px !important; width: auto !important; display: block !important; border: none !important; margin: 0em !important; padding: 0em !important; box-shadow: none !important; object-fit: contain !important;">
            </a>

            <div style="height: 30px !important; border-left: 1.5px solid #FFFFFF !important; margin: 0 10px !important; opacity: 0.9 !important; display: block !important; width: 0em !important; padding: 0em !important;"></div>

            <div style="display: flex !important; align-items: center !important; justify-content: center !important; padding: 0em !important; margin: 0em !important;">
                <img src="<?php echo esc_url( $logo_colombia_url ); ?>"
                     alt="Marca País Colombia"
                     style="height: 38px !important; width: auto !important; display: block !important; border: none !important; margin: 0em !important; padding: 0em !important; box-shadow: none !important; object-fit: contain !important;">
            </div>

        </div>
    </div>
    <?php
}

// Desactivamos cualquier hook previo que pueda duplicar la barra inferior.
remove_action( 'wp_footer', 'integrar_barra_azul_final_govco_corregida', 999 );
remove_action( 'wp_footer', 'integrar_barra_azul_final_govco_accesible', 999 );

// Acoplamos la estructura oficial al cierre del documento.
add_action( 'wp_footer', 'bgc_render_bottom_bar', 999 );
