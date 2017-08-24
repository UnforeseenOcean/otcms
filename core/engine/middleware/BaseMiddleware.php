<?hh
namespace core\engine\middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class BaseMiddleware {
    protected $ci;
    protected \MysqliDb $database;
    protected \account $user;

    public function __construct($ci) {
        $this->ci = $ci;
        $this->database = $ci['database'];
        $this->user = $ci['user'];
    }

    abstract public function __invoke(Request $request, Response $response, $next) : Response;
}
