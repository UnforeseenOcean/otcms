<?hh
namespace core\factory;

require 'BaseFactory.php';

class postfactory extends BaseFactory
{

    public function getPost($pid = null, $page = 1, $filter = 'all', $allowContent = false, $pageLimit = 10, bool $onlyPublished = true) {
        $should_use_get = false;
        $rows = Array('id', 'author', 'title', 'thumbnail', 'time', 'type', 'author_id', 'published', 'summary');

        $post = $this->database->orderBy('id','desc');

        if($pid) {
            $post = $post->where('id', $pid);
            $rows = array_merge($rows, Array('content'));
            $should_use_get = true;
        }

        if($onlyPublished) {
          $post = $post->where('published', 1); // we will solve filtering this in admin panel later
        }

        if($allowContent) {
            $rows = array_merge($rows, Array('content'));
        }

        if(strtolower($filter) !== 'all') {
            $post = $post->where('lower(type)', strtolower($filter));
        }

        $post->pageLimit = $pageLimit;
        if(!$should_use_get){
            $post = $post->arraybuilder()->paginate('news', intval($page), $rows);
        } else {
            $post = $post->get('news', 1, $rows);
        }

        for($i = 0; $i < count($post); $i++) {
          $p = $post[$i];

          if($p['author_id'] == -1) continue;

          $userInfo = $this->ci['userfactory']->getUser(intval($p['author_id']), ['username']);
          if(count($userInfo > 0)) {
            $post[$i]['author'] = $userInfo['username'];
          }
        }

        return $post;
    }

}
