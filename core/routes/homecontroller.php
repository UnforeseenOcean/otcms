<?hh
namespace core\routes;

require 'BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class homecontroller extends BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) : mixed {
        $logout = $request->getQueryParam('logout', false);
        if(filter_var($logout, FILTER_VALIDATE_BOOLEAN)) {
            $this->ci['user']->clear();
            return $response->withRedirect('/');
        }

        if($this->ci['user']->isLoggedIn()) {
            if(!isset($this->ci['user']['count'])) {
                $this->ci['user']['count'] = 1;
            } else {
                $this->ci['user']['count'] = $this->ci['user']['count'] + 1;
            }
        }

        return $this->render($response, 'home', [
            'page_title' => 'Home',
            'name' => $args['name']
        ]);
    }
}
