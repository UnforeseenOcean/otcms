<?hh
namespace core\routes;

require 'BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class rsscontroller extends BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) : mixed {
        $rss = new \FeedWriter\RSS2();
        $rss->setTitle('OverTells RSS feed');
        $rss->setLink('https://overtells.com/');
        $rss->setDescription('OverTells news RSS feed');

        $page = $request->getQueryParam('page', '1');
        $type = $request->getQueryParam('type', 'all');

        $news = $this->ci['postfactory']->getPost(false, $page, $type, true);

        for($i = 0; $i < count($news); $i++) {
            $post = $news[$i];

            $item = $rss->createNewItem();
            $item->setTitle($post['title']);
            $item->setLink('http://overtells.com/post/'.$post['id']);
            $item->setDate($post['time']);
            $item->setDescription(strip_tags($post['content']));
            $item->setAuthor($post['author']);
            $item->setId($post['id']);

            $rss->addItem($item);
        }

        return $this->write($response, $rss->generateFeed(), [ 'Content-type' => 'application/rss+xml' ]);
    }
}
