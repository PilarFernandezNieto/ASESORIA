<?php

declare(strict_types=1);

/**
 * Funciones compartidas por build.php (generación estática) y dev.php (servidor de desarrollo).
 */

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

const RAIZ = __DIR__ . '/..';

/**
 * Lee un archivo JSON y devuelve su contenido decodificado.
 *
 * @throws RuntimeException si el archivo no existe o el JSON no es válido.
 */
function leerJson(string $ruta): array
{
    $json = is_file($ruta) ? file_get_contents($ruta) : false;
    if ($json === false) {
        throw new RuntimeException("No se puede leer $ruta");
    }
    try {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        throw new RuntimeException("JSON inválido en $ruta: {$e->getMessage()}");
    }
}

/**
 * Crea el entorno Twig con los datos globales del sitio (se leen en cada llamada).
 *
 * Con $debug activo, dump() vuelca variables en el HTML (solo para dev.php).
 * Sin él, dump() existe pero no emite nada, así el build estático nunca lo filtra.
 */
function crearTwig(bool $debug = false): Environment
{
    $twig = new Environment(
        new FilesystemLoader(RAIZ . '/templates'),
        ['autoescape' => 'html', 'strict_variables' => true, 'debug' => $debug]
    );
    if ($debug) {
        $twig->addExtension(new DebugExtension());
    } else {
        $twig->addFunction(new TwigFunction('dump', static fn(): string => ''));
    }
    $twig->addGlobal('sitio', leerJson(RAIZ . '/data/sitio.json'));
    $twig->addGlobal('noticias', leerJson(RAIZ . '/data/noticias.json'));
    return $twig;
}

/** Renderiza una página a partir de data/paginas/<nombre>.json. */
function renderizarPagina(Environment $twig, string $nombre): string
{
    $pagina = leerJson(RAIZ . "/data/paginas/$nombre.json");
    return $twig->render('base.html.twig', ['pagina' => $pagina]);
}
