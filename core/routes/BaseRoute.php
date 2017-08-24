<?hh
namespace core\routes;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class BaseRoute {
    protected $ci;
    protected \MysqliDb $database;

    public function __construct($ci) {
        $this->ci = $ci;
        $this->database = $ci['database'];
    }

    protected function render(Response $response, string $page, $args = []) : mixed {
        return $this->ci['view']->render($response, $page.'.twig', $args);
    }

    protected function write(Response $response, string $data, array $headers = [], int $status = 200) : mixed {
        $newRes = $response;
        foreach ($headers as $key => $value) {
          $newRes = $newRes->withHeader($key, $value);
        }

        return $newRes->write($data, $status);
    }

    abstract public function __invoke(Request $request, Response $response, $args = []) : mixed ;
}
