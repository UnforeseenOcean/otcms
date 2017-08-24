<?hh
namespace core\engine\middleware;

require 'BaseMiddleware.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class permissioncheck extends BaseMiddleware {
  private string $permission = '';

  public function __construct($ci, string $permission) {
    parent::__construct($ci);
    $this->permission = $permission;
  }

  public function __invoke(Request $request, Response $response, $next) : Response {
    if($this->user->getACL()->hasPermission($this->permission)) {
      return $next($request, $response);
    }
    $body = new \Slim\http\Body(fopen('php://temp', 'r+'));
    $body->write('Forbidden');

    return $response->withBody($body)->withStatus(403);
  }
}
