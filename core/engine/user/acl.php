<?hh
namespace core\engine\user;

class acl {
    private $database;
    private $userEngine;

    public function __construct($ci, $userEngine) : void {
        $this->database = $ci['database'];
        $this->userEngine = $userEngine;
    }

    public function hasPermission($perm) : bool {
        $rankID = false;
        if(!isset($this->userEngine['session'])) {
            $rankID = 2; // 2 = guest id
        }

        $perms = json_decode($this->getRankPermissions($rankID)['permissions'], true);

        if(in_array('su.has.all', $perms)) {
            return true;
        }

        return in_array($perm, $perms);
    }

    public function getAllPermissions() : array {
        $ranks = $this->database->get('ranks');

        for($i = 0; $i < count($ranks); $i++) {
            $ranks[$i]['permissions'] = json_decode($ranks[$i]['permissions'], true);
        }

        return $ranks;
    }

    private function getRankPermissions($rankID = false) : array {
        $rank = $rankID;
        if(!$rankID) {
            // Get this from database so we always know there new rank
            $rank = $this->database->where('id', $this->userEngine['session']['id'])->getOne('postStaff', ['rank_int']);
            $rank = $rank['rank_int'];
        }

        $ranks = $this->database->where('id', $rank)->getOne('ranks', ['name', 'permissions']);

        return $ranks;
    }
}
