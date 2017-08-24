<?hh
namespace core\engine\user;

abstract class BaseUser implements KeyedContainer<string, mixed>, \ArrayAccess<string, mixed>
{
    protected $ci;
    protected \MysqliDb $database;

    public function __construct($ci, $view) : void {
        $this->ci = $ci;
        $this->database = $ci['database'];
        $view['user'] = $this;
    }

    /* Ooverrides to the $_SESSION system */
    public function __get($name) : mixed {
        return $this->get($name);
    }

    public function __set($name, $value) : void {
        $_SESSION[$name] = $value;
    }

    public function __isset($name) : bool {
        return isset($_SESSION[$name]);
    }

    public function __unset($name) : void {
        unset($_SESSION[$name]);
    }

    public function clear() : void {
        session_unset();
        session_destroy();
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $_SESSION[] = $value;
        } else {
            $_SESSION[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($_SESSION[$offset]);
    }

    public function offsetUnset($offset) {
        unset($_SESSION[$offset]);
    }

    public function offsetGet($offset) {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }

    public function getRawSession() : array {
      return $_SESSION;
    }

    /* protected functions */
    protected function get($item, $default = null) : mixed {
        return isset($_SESSION[$item]) ? $_SESSION[$item] : $default;
    }

    protected function set($key, $value) : void {
        $_SESSION[$key] = $value;
    }
}
