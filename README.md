# Web corporativa avantek (Twig)

Páginas en Twig + Bootstrap 5.3 + CSS propio. El contenido vive en `data/` (simula una base de datos) y se envía a las plantillas; cada banda es un parcial independiente en `templates/bandas/`.

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

```bash
composer install
php build.php        # genera dist/
php -S localhost:8000 -t dist
```

## Criterios

- Los botones y elementos comunes viven en `css/layout_general.css`; `index.css`, `interior.css` y `noticia.css` solo llevan ajustes particulares.
- Clases propias con guiones bajos. Sin JavaScript propio.
