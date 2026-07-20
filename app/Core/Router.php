<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function ajouter(string $methode, string $chemin, array $action, ?string $filtre = null): void
    {
        $pattern = preg_replace('#\(:num\)#', '([0-9]+)', $chemin);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'methode' => $methode,
            'pattern' => $pattern,
            'action'  => $action,
            'filtre'  => $filtre,
        ];
    }

    public function get(string $chemin, array $action, ?string $filtre = null): void
    {
        $this->ajouter('GET', $chemin, $action, $filtre);
    }

    public function post(string $chemin, array $action, ?string $filtre = null): void
    {
        $this->ajouter('POST', $chemin, $action, $filtre);
    }

    public function dispatch(string $methode, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['methode'] !== $methode) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);

                if ($route['filtre'] === 'clientAuth' && ! Session::get('client_id')) {
                    Session::setFlash('error', 'Veuillez vous connecter avec votre numéro de téléphone.');
                    $this->redirect('/login');

                    return;
                }

                if ($route['filtre'] === 'operateurAuth' && ! Session::get('is_operateur')) {
                    Session::setFlash('error', "Veuillez vous connecter en tant qu'opérateur.");
                    $this->redirect('/operateur/login');

                    return;
                }

                [$classe, $methodeAction] = $route['action'];
                $controleur = new $classe();
                call_user_func_array([$controleur, $methodeAction], $matches);

                return;
            }
        }

        http_response_code(404);
        echo '404 - Page introuvable';
    }

    public function redirect(string $chemin): void
    {
        header('Location: ' . View::baseUrl($chemin));
        exit;
    }
}
