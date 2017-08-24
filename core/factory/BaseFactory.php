<?hh
namespace core\factory;

abstract class BaseFactory {
    protected $ci;
    protected \MysqliDb $database;

    public function __construct($ci) {
        $this->ci = $ci;
        $this->database = $ci['database'];
    }
}
