<?hh
namespace core\engine\user;

require 'core/engine/user/BaseUser.php';
require 'core/engine/user/accountauth.php';
require 'core/engine/user/acl.php';

class account extends BaseUser {
    private $accountauth;
    private $acl;

    public function __construct($ci, $view) : void {
        parent::__construct($ci, $view);
        $this->accountauth = new accountauth($ci);
        $this->acl = new acl($ci, $this);
    }

    public function isLoggedIn() : bool {
        return filter_var($this->get('logged_in'), FILTER_VALIDATE_BOOLEAN);
    }

    public function setLoggedIn($value = true) : void {
        $this->set('logged_in', $value);
    }

    public function getUserID() : string {
        if(!isset($_SESSION['uuid'])){
            $_SESSION['uuid'] = uniqid();
        }

        return $_SESSION['uuid'];
    }

    public function getAccountAuth() : accountauth {
        return $this->accountauth;
    }

    public function getACL() : acl {
        return $this->acl;
    }
}
