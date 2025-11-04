<?php

namespace App\Http\Middleware;

use App\Services\FeatureFlag;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class LegacyProxyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Se o middleware está desabilitado, passa direto
        if (!Config::get('modern_routes.enabled', true)) {
            return $next($request);
        }

        $path = $request->path();
        $decision = $this->decideRoute($path, $request);

        // Log a decisão se habilitado
        $this->logDecision($path, $decision, $request);

        // Se a rota é moderna, continua no Laravel
        if ($decision['type'] === 'modern') {
            return $next($request);
        }

        // Se é legado, executa o código PHP legado
        if ($decision['type'] === 'legacy') {
            return $this->executeLegacyCode($request, $decision['file']);
        }

        // Fallback: passa para o próximo middleware
        return $next($request);
    }

    /**
     * Decide se a rota deve ser processada por código moderno ou legado
     *
     * @param string $path
     * @param Request $request
     * @return array
     */
    protected function decideRoute(string $path, Request $request): array
    {
        // Primeiro, verifica se é uma rota Laravel registrada
        if ($this->isLaravelRoute($request)) {
            return [
                'type' => 'modern',
                'reason' => 'Laravel registered route',
            ];
        }

        // Verifica se a rota combina com algum padrão moderno
        $modernPatterns = Config::get('modern_routes.modern_patterns', []);
        
        foreach ($modernPatterns as $pattern) {
            if (fnmatch($pattern, $path)) {
                // Verifica feature flag associada
                $feature = $this->getFeatureForRoute($path);
                
                if ($feature && !FeatureFlag::isEnabled($feature, auth()->user())) {
                    return [
                        'type' => 'legacy',
                        'reason' => "Feature '{$feature}' is disabled",
                        'file' => $this->findLegacyFile($path),
                    ];
                }

                return [
                    'type' => 'modern',
                    'reason' => "Matches modern pattern: {$pattern}",
                ];
            }
        }

        // Verifica se tem extensão .php (provavelmente legado)
        if (str_ends_with($path, '.php')) {
            return [
                'type' => 'legacy',
                'reason' => 'PHP file extension',
                'file' => $this->findLegacyFile($path),
            ];
        }

        // Verifica padrões explícitos de legado
        $legacyPatterns = Config::get('modern_routes.legacy_patterns', []);
        
        foreach ($legacyPatterns as $pattern) {
            if (fnmatch($pattern, $path)) {
                return [
                    'type' => 'legacy',
                    'reason' => "Matches legacy pattern: {$pattern}",
                    'file' => $this->findLegacyFile($path),
                ];
            }
        }

        // Default: tenta encontrar arquivo legado
        $legacyFile = $this->findLegacyFile($path);
        
        if ($legacyFile && file_exists($legacyFile)) {
            return [
                'type' => 'legacy',
                'reason' => 'Legacy file found',
                'file' => $legacyFile,
            ];
        }

        // Passa para Laravel (pode gerar 404 se não existir)
        return [
            'type' => 'modern',
            'reason' => 'No legacy file found, trying Laravel',
        ];
    }

    /**
     * Verifica se existe uma rota Laravel registrada para o request
     *
     * @param Request $request
     * @return bool
     */
    protected function isLaravelRoute(Request $request): bool
    {
        try {
            $route = app('router')->getRoutes()->match($request);
            return $route !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Encontra o arquivo legado correspondente ao path
     *
     * @param string $path
     * @return string|null
     */
    protected function findLegacyFile(string $path): ?string
    {
        $basePath = base_path();
        
        // Lista de diretórios onde procurar arquivos legados
        $searchPaths = [
            $basePath . '/' . $path,
            $basePath . '/resources/legacy/' . $path,
            $basePath . '/' . dirname($path) . '/' . basename($path, '.php') . '.php',
        ];

        foreach ($searchPaths as $filePath) {
            if (file_exists($filePath) && is_file($filePath)) {
                return $filePath;
            }
        }

        return null;
    }

    /**
     * Executa código PHP legado
     *
     * @param Request $request
     * @param string|null $legacyFile
     * @return mixed
     */
    protected function executeLegacyCode(Request $request, ?string $legacyFile)
    {
        if (!$legacyFile || !file_exists($legacyFile)) {
            abort(404, 'Legacy file not found');
        }

        // Compartilha sessão com código legado se configurado
        if (Config::get('modern_routes.session.share_with_legacy', true)) {
            $this->shareLegacySession();
        }

        // Inicia buffer de saída
        ob_start();

        try {
            // Define variáveis globais que o código legado pode esperar
            $_SERVER = array_merge($_SERVER, $request->server->all());
            $_GET = $request->query->all();
            $_POST = $request->request->all();
            $_REQUEST = array_merge($_GET, $_POST);
            $_COOKIE = $request->cookies->all();

            // Executa o arquivo legado
            require $legacyFile;

            $output = ob_get_clean();

            // Retorna a resposta
            return response($output)
                ->header('Content-Type', 'text/html; charset=ISO-8859-1');

        } catch (\Throwable $e) {
            ob_end_clean();
            
            Log::error('Error executing legacy code', [
                'file' => $legacyFile,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Compartilha sessão com código legado
     *
     * @return void
     */
    protected function shareLegacySession(): void
    {
        if (session()->isStarted()) {
            // Garante que a sessão PHP está disponível
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            // Copia dados da sessão Laravel para $_SESSION
            $_SESSION = array_merge($_SESSION ?? [], session()->all());
        }
    }

    /**
     * Identifica qual feature flag está associada à rota
     *
     * @param string $path
     * @return string|null
     */
    protected function getFeatureForRoute(string $path): ?string
    {
        $features = Config::get('modern_routes.feature_flags.features', []);

        foreach ($features as $featureName => $feature) {
            $routes = $feature['routes'] ?? [];
            
            foreach ($routes as $routePattern) {
                if (fnmatch($routePattern, $path)) {
                    return $featureName;
                }
            }
        }

        return null;
    }

    /**
     * Loga a decisão de roteamento
     *
     * @param string $path
     * @param array $decision
     * @param Request $request
     * @return void
     */
    protected function logDecision(string $path, array $decision, Request $request): void
    {
        if (!Config::get('modern_routes.logging.enabled', false)) {
            return;
        }

        Log::channel(Config::get('modern_routes.logging.channel', 'stack'))
            ->debug('Route decision', [
                'path' => $path,
                'type' => $decision['type'],
                'reason' => $decision['reason'],
                'file' => $decision['file'] ?? null,
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);
    }
}
