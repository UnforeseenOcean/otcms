<?hh
namespace core\routes\debug;

require 'core/routes/BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class accountcreatecontroller extends \core\routes\BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) : void {
        $user = $this->ci['user'];
        $userAuth = $user->getAccountAuth();

        $output = 'failure';

        if($userAuth->register('debug_user', 'test@example.oom', 'example') <= 0) {
            $output = 'Username/Email already exist';
        } else {
            $output = 'Created the debug_user';
        }
        $response->withHeader('Content-type', 'application/rss+xml')->write($output, 200);
    }
}
