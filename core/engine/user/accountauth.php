<?hh
namespace core\engine\user;

require 'vendor/autoload.php';

class accountauth
{
    protected $database;

    public function __construct($ci) : void {
        $this->database = $ci['database'];
    }

    public function login($username, $password) : mixed {
        $userAccount = $this->database->where('lower(username)', strtolower($username))->getOne('postStaff');

        if(count($userAccount) <= 0) {
            return false; // false = invalid username or password error code
        }

        $password = \password_verify($password, $userAccount['password']);

        if($password) {
          // NOTE: we remove these as they are legacy and also the users password
          unset($userAccount['password']);
          unset($userAccount['cookie_val']);
          unset($userAccount['rank']);
        }
        return $password ? $userAccount : false;
    }

    public function register($username, $email, $password, int $rank = 0) : int {
        $userAccount = $this->database->where('lower(username)', strtolower($username))->orWhere('email', strtolower($email))->getOne('postStaff', 1);

        if(count($userAccount) >= 1) {
            return 0; // 0 = user/email already existed
        }

        $data = Array (
            'username' => strtolower($username),
            'email' => strtolower($email),
            'password' => \password_hash($password, PASSWORD_DEFAULT),
            'rank_int' => $rank
        );
        return $this->database->insert ('postStaff', $data); // returns that user ID so anything over '0' is actually correct
    }
}
