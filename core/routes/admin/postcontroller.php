<?hh
namespace core\routes\admin;

require 'core/routes/BaseRoute.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class postcontroller extends \core\routes\BaseRoute
{
    public function __invoke(Request $request, Response $response, $args = []) : mixed {
      if($request->isPost()) {
        switch(strtolower($args['type'])) {
          case 'edit':
            return $this->handlePostUpdatePost($request, $response, $args);
          case 'new':
            return $this->handlePostNewPost($request, $response, $args);
        }
        //return $response->withJson($request->getParsedBody());
      } else {
        if(isset($args['type'])) {
          switch(strtolower($args['type'])) {
            case 'edit':
              return $this->editPost($request, $response, $args);
            case 'new':
              return $this->newPost($request, $response, $args);
            default:
              return $this->allPost($request, $response, $args);
          }
        } else {
          return $this->allPost($request, $response, $args);
        }
      }
    }

    public function handlePostUpdatePost(Request $request, Response $response, $args = []) : mixed {
      $postData = $request->getParsedBody();
      $this->database->where('id', $postData['postID']);

      $data = Array (
        'title' => $postData['postTitle'],
      	'summary' => \strip_tags($postData['postSummary']),
        'content' => $postData['editorData'],
        'thumbnail' => $postData['thumbnailURL'],
      	'published' => filter_var($postData['published'], FILTER_VALIDATE_BOOLEAN),
        'update_time' => \time()
      );

      $data = $this->database->update('news', $data);

      return $response->withJson([ 'result' => 'Post has been updated' ]);
    }

    public function handlePostNewPost(Request $request, Response $response, $args = []) : mixed {
      $postData = $request->getParsedBody();
      $time = \time();

      $data = Array (
        'title' => $postData['postTitle'],
        'summary' => \strip_tags($postData['postSummary']),
        'content' => $postData['editorData'],
        'thumbnail' => $postData['thumbnailURL'],
        'published' => filter_var($postData['published'], FILTER_VALIDATE_BOOLEAN),
        'author' => $this->ci['user']['session']['username'],
        'author_id' => $this->ci['user']['session']['id'],
        'type' => 'news',
        'time' => $time,
        'update_time' => $time
      );

      $data = $this->database->insert('news', $data);

      return $response->withJson([ 'result' => 'Post has been saved' ]);
    }

    public function editPost(Request $request, Response $response, $args = []) : mixed {
      $post = $this->ci['postfactory']->getPost($args['pid'], 1, 'all', true, 1, false);

      if(count($post) > 0) {
        $post = $post[0];
      }

      return $this->render($response, "admin/admin.post.edit",  [
          'page_title' => 'Editing: '.$post['title'],
          'post' => $post,
          'pid' => $args['pid'],
          'mode' => 'edit'
      ]);
    }

    public function newPost(Request $request, Response $response, $args = []) : mixed {
      return $this->render($response, "admin/admin.post.edit",  [
          'page_title' => 'Creating new post',
          'post' => [ 'id' => 'new' ],
          'mode' => 'new'
      ]);
    }

    public function allPost(Request $request, Response $response, $args = []) : mixed {
      $page = $request->getQueryParam('page', '1');

      $news = $this->ci['postfactory']->getPost($args['pid'], $page, 'all', false, 25, false);

      $page_title = 'All Post';

      if(count($news) == 1) {
          $page_title = 'Editing: '.$news[0]['title'];
      }

      return $this->render($response, "admin/admin.post",  [
          'page_title' => $page_title,
          'posts' => $news,
          'current_page' => $page,
          'pages' => $news->totalPages,
          'pid' => $args['pid']
      ]);
    }
}
