<?php
declare(strict_types=1);

namespace App\Middleware\UnauthorizedHandler;

use Authorization\Exception\Exception;
use Authorization\Exception\ForbiddenException;
use CakeDC\Users\Middleware\UnauthorizedHandler\DefaultRedirectHandler;
use Cake\Http\Session;
use Cake\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ForbiddenHandler extends DefaultRedirectHandler
{
    /**
     * ログイン済みユーザが権限のないページへアクセスした場合:
     * 同じコントローラの index へリダイレクトし、エラーメッセージを表示する。
     */
    public function handle(
        Exception $exception,
        ServerRequestInterface $request,
        array $options = []
    ): ResponseInterface {
        if ($exception instanceof ForbiddenException && $request->getAttribute('identity')) {
            $response = $this->buildForbiddenResponse($request);
            $session = $request->getAttribute('session');
            if ($session instanceof Session) {
                $messages = (array)$session->read('Flash.flash');
                $messages[] = [
                    'message' => __d('cake_d_c/users', 'You are not authorized to access that location.'),
                    'key' => 'flash',
                    'element' => 'flash/error',
                    'params' => [],
                ];
                $session->write('Flash.flash', $messages);
            }

            return $response;
        }

        return parent::handle($exception, $request, $options);
    }

    private function buildForbiddenResponse(ServerRequestInterface $request): ResponseInterface
    {
        $controller = $request->getParam('controller');
        $plugin     = $request->getParam('plugin');
        $prefix     = $request->getParam('prefix');

        $url = array_filter([
            'plugin'     => $plugin ?: false,
            'prefix'     => $prefix ?: false,
            'controller' => $controller,
            'action'     => 'index',
        ]);

        try {
            $redirectUrl = Router::url($url);
        } catch (\Exception $e) {
            $redirectUrl = '/';
        }

        return (new \Cake\Http\Response())
            ->withStatus(302)
            ->withHeader('Location', $redirectUrl);
    }
}
