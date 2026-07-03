=== Barra GOV.CO ===
Contributors: govco
Tags: gov.co, colombia, government, branding, accessibility
Requires at least: 5.6
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 1.0.0
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

= 1.0.0 =
* Versión inicial. Barra superior, barra azul inferior, y carga de assets oficiales de GOV.CO v5.

== Upgrade Notice ==

= 1.0.0 =
Primera versión estable.
