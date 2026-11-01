# Web corporativa Avantek

La web publicada es HTML estático: se puede copiar la carpeta `dist/` a cualquier ordenador y abrir `dist/index.html` en un navegador, sin instalar programas, usar PHP ni tener conexión a Internet. Bootstrap, los iconos y las imágenes se sirven desde archivos locales.

El código fuente usa Twig para generar esas páginas. El contenido vive en `data/` y cada banda es una plantilla independiente en `templates/bandas/`.

## Estructura

```
data/
├── sitio.json                # marca, menú y pie (comunes)
├── noticias.json             # tabla de noticias (id, titulo, categoria, fecha, resumen, url)
└── paginas/<pagina>.json     # titulo, activa, estilos y lista ordenada de bandas
templates/
├── base.html.twig            # <head>, cabecera, bucle de bandas, pie, scripts
├── partials/                 # cabecera.html.twig, pie.html.twig
└── bandas/                   # una <section class="banda_*"> por archivo
css/  assets/                 # estilos y logotipos
build.php                     # una página dist/<pagina>.html por cada data/paginas/*.json
```

## Modelo de datos

Cada página es:

```json
{
  "titulo": "...", "activa": "inicio", "estilos": ["index"],
  "bandas": [ { "plantilla": "hero", "datos": { ... } } ]
}
```

`base.html.twig` recorre `bandas` e incluye `bandas/<plantilla>.html.twig` pasándole `datos`. Para cambiar el orden, añadir o quitar una banda basta con editar el JSON de la página. Cada plantilla documenta en su primera línea los campos que espera.

| Página | Bandas (en orden) |
| --- | --- |
| `index` | hero, actividad, ambitos_iconos, teaser, producto, ultimas_noticias, cierre |
| `sobre-nosotros` | cabecera_interior, texto_imagen, ambitos_texto, valores, relacion, cierre |
| `noticias` | cabecera_interior, listado_noticias (incluye paginacion) |
| `noticia` | noticia_cabecera, noticia_cuerpo |
| `contacto` | cabecera_interior, contacto |

`caja_noticia` es la tarjeta de noticia (portada y listado). Las bandas de noticias leen `noticias.json`; `noticia_cabecera` busca la noticia por `noticia_id` y `noticia_cuerpo` recibe el texto como bloques (`destacado`, `parrafo`, `subtitulo`, `cita`, `lista`, `figura`).

## Uso

### Ver la web

Abre `dist/index.html` con cualquier navegador. Para compartirla, copia la carpeta `dist/` completa; conserva su estructura de carpetas.

### Regenerar la web desde las plantillas

Para editar plantillas o datos y volver a generar `dist/`, sí hacen falta PHP y Composer en el ordenador de desarrollo:

```bash
composer install
php build.php
```

El contenido generado no necesita PHP ni Composer para verse. No hace falta ejecutar un servidor local.

## Criterios

- Los botones y elementos comunes viven en `css/layout_general.css`; `index.css`, `interior.css` y `noticia.css` solo llevan ajustes particulares.
- Clases propias con guiones bajos. Sin JavaScript propio.
