<?hh
namespace core\engine\theme;

require 'themecore.php';

class thememanager {
  private string $rootFolder = "";
  private string $defaultTheme = "";

  private ?themecore $defaultLoadedTheme = null;

  public function __construct($root) : void {
    $this->rootFolder = $root;
  }

  public function setDefaultTheme($theme) : void {
    $this->defaultTheme = $theme;

    $this->defaultLoadedTheme = $this->loadTheme($theme);
  }

  public function getDefaultTheme() : ?themecore {
    return $this->defaultLoadedTheme;
  }

  private function loadTheme(?string $theme = null) : themecore {
    $tc = new themecore($this->rootFolder, $theme);

    return $tc;
  }
}
