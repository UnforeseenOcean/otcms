<?hh
namespace core\routes\admin;

require 'core/routes/BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class homecontroller extends \core\routes\BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) : mixed {
        return $this->render($response, 'admin/admin.home', [
            'page_title' => 'Admin Home',
            'pt_text' => $this->ci['view']['pt']->paragraphs(3, 'p')
        ]);
    }
}
