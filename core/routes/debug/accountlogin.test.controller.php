<?hh
namespace core\routes\debug;

require 'core/routes/BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class accountlogincontroller extends \core\routes\BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) : void {
        $user = $this->ci['user'];
        $userAuth = $user->getAccountAuth();

        $output = 'failure';

        $auth = $userAuth->login('debug_user', 'example');
        if($auth <= 0) {
            $output = 'Invalid username/password';
        } else {
            $output = 'We logged in';

            $user['session'] = $auth;
            $user->setLoggedIn();
        }
        $response->withHeader('Content-type', 'application/rss+xml')->write($output, 200);
    }
}
