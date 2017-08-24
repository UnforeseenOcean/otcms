<?hh
namespace core\engine\middleware;

require 'BaseMiddleware.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class adminlinkbuilder extends BaseMiddleware {

  public function __invoke(Request $request, Response $response, $next) : Response {
    $this->ci['view']['links'] = array(
              [
                'perm' => 'admin.panel.modify_post',
                'url' => '/admin/posts',
                'text' => 'All Post'
              ],
              [
                'perm' => 'admin.panel.modify_user',
                'url' => '/admin/users',
                'text' => 'Users & Groups'
              ],
              [
                'perm' => 'admin.panel.modify_permissions',
                'url' => '/admin/permissions',
                'text' => 'Groups Permissions'
              ]
            );
    return $next($request, $response);
  }

}
