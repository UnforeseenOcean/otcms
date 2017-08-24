<?hh
namespace core\factory;

require 'BaseFactory.php';

class userfactory extends BaseFactory
{

  public function getUser(int $uid, ?array $rows = null) : mixed {
    return $this->database->where('id', $uid)->getOne('postStaff', $rows);
  }

  public function getAllUser(int $page, int $limit = 10, ?array $rows = null) : array {
    $users = $this->database;
    $users->pageLimit = $limit;

    return $users->arraybuilder()->paginate('postStaff', intval($page), $rows);
  }
}
