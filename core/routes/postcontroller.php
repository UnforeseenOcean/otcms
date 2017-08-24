<?hh
namespace core\routes;

require 'BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class postcontroller extends BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) {
        $page = $request->getQueryParam('page', '1');
        $type = $request->getQueryParam('type', 'all');

        $news = $this->ci['postfactory']->getPost($args['pid'], $page, $type);

        $page_title = 'News';

        if(count($news) <= 0 ){
            return $this->ci['notFoundHandler']($request, $response);
        }

        if(count($news) == 1) {
            $page_title = $news[0]['title'];
        }

        return $this->render($response, "post",  [
            'page_title' => $page_title,
            'posts' => $news,
            'current_page' => $page,
            'pages' => $news->totalPages,
            'pid' => $args['pid']
        ]);
    }
}
