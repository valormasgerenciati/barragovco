=== Barra GOV.CO ===
Contributors: govco
Tags: gov.co, colombia, government, branding, accessibility
Requires at least: 5.6
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integra la barra superior y la barra azul inferior oficiales de GOV.CO en cualquier sitio WordPress, siguiendo los lineamientos del Kit UI 9.2.

== Description ==

Este plugin añade automáticamente, en el front-end de tu sitio WordPress:

* La **barra superior** oficial de GOV.CO, con el logotipo del Portal Único del Estado Colombiano, vinculada a https://www.gov.co/home/
* La **barra azul inferior** oficial con el logotipo GOV.CO y la Marca País Colombia
* Los **estilos y scripts** oficiales del CDN de GOV.CO v5 (cdn.www.gov.co)

Está pensado para portales y entidades públicas (o privadas) que deben exhibir la imagen institucional del Estado colombiano sin modificar el tema activo.

= Características =

* Funciona con cualquier tema WordPress (incluidos temas personalizados que no usan `wp_body_open`).
* No requiere configuración: instalar y activar.
* Imágenes servidas localmente desde la carpeta `/logos` del plugin (sin dependencia del CDN para los logotipos).
* Cumple con tamaños y colores oficiales definidos en el Kit UI 9.2.
* Etiquetas ARIA para accesibilidad.

== Installation ==

1. Descarga el archivo ZIP del plugin (si aún no lo tienes, comprime la carpeta `barra-govco`).
2. En tu WordPress, ve a **Plugins → Añadir nuevo → Subir plugin**.
3. Selecciona el archivo ZIP y haz clic en **Instalar ahora**.
4. Activa el plugin desde la pantalla **Plugins**.
5. Visita cualquier página del front-end: la barra GOV.CO debería aparecer arriba y abajo del contenido.

= Instalación manual (FTP) =

1. Sube la carpeta `barra-govco` a `wp-content/plugins/` de tu instalación.
2. Activa el plugin en **Plugins** desde el panel de WordPress.

== Frequently Asked Questions ==

= ¿Puedo cambiar los logotipos? =

Sí. Reemplaza los archivos `logos/logoGovCO.png` y `logos/logocolombia.png` por tus propias versiones conservando los nombres.

= ¿Qué pasa si mi tema no implementa `wp_body_open`? =

El plugin incluye un fallback que registra la barra superior también en `wp_footer`. Se utiliza un flag interno para garantizar que la barra nunca se imprima dos veces.

= ¿Este plugin depende de servicios externos? =

Los estilos y scripts se cargan desde el CDN oficial `cdn.www.gov.co`. Los logotipos se sirven localmente desde el propio plugin.

== Changelog ==
= 1.0.4 =
* Visibilidad: la barra superior ahora usa `position: fixed; top: 0` con `z-index: 2147483647` (máximo entero) en lugar de `position: relative`. Esto garantiza que quede visible sobre cualquier `<header>` fijo o sticky del tema (BeTheme y otros builders).
* Se añade un `<style>` inline que aplica `padding-top: 56px` al `<body>` para evitar que el header del tema quede oculto bajo la barra fija, y `scroll-padding-top` en `<html>` para que los anclajes de scroll no queden bajo la barra.
= 1.0.3 =
* Corrección crítica (WSOD): se elimina el `ob_start` anidado dentro del callback de buffer, que podía provocar pantalla en blanco al combinarse con plugins de caché u otros buffers activos.
* `bgc_get_top_bar_html()` ahora construye el HTML por concatenación de cadenas (sin `ob_start`/`ob_get_clean` internos).
* `bgc_inject_top_bar_into_body()` blindado contra retornos `null` de `preg_replace` (PHP 8+) y contra buffers no-string.
* `bgc_start_output_buffer()` añade guards `headers_sent()` y `ob_get_level() > 0` para no activar el buffer cuando hay otro activo o ya se enviaron headers.

= 1.0.2 =
* Corrección: la barra superior ya no se renderiza al pie del documento en temas que no implementan `wp_body_open()` (BeTheme, Muffin Builder, themes con builders personalizados). Se sustituye el fallback basado en `wp_footer` por inyección vía `output buffering` en `template_redirect`, con detección de duplicados.
* Refactor: se extrae `bgc_get_top_bar_html()` para reutilizar el marcado entre `wp_body_open` y el buffer.
* Exclusiones añadidas en el buffer: admin, AJAX, cron, REST y WP-CLI.

= 1.0.1 =
* Ajustes menores de marcado y robustez en la barra superior.

= 1.0.0 =
* Versión inicial. Barra superior, barra azul inferior, y carga de assets oficiales de GOV.CO v5.

== Upgrade Notice ==

= 1.0.4 =
La barra superior ahora es fija y se muestra por encima de cualquier header fijo/sticky del tema. Recomendado para todos los sitios con headers de menú que se superponían a la barra GOV.CO.
