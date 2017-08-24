<?hh
namespace core\engine\theme;

class themecore {
  private string $themeFolder;
  private string $themeRoot;
  private array  $themeInfo;

  public function __construct($themeFolder, $theme) {
    $this->themeFolder = ROOT_DIR.$themeFolder.'/'.$theme;
    $this->themeRoot = '/'.$themeFolder.'/'.$theme;
    $this->themeInfo = json_decode(file_get_contents($this->themeFolder.'/info.json'), true);
  }

  public function getThemeFolder() : string {
    return $this->themeFolder.'/'.$this->themeInfo['layout_folder'];
  }

  public function getName() : string {
    return $this->themeInfo['name'];
  }

  public function getJavaScript() : array {
    return $this->loadResourceTypes('javascript');
  }

  public function getStyleSheets() : array {
    return $this->loadResourceTypes('stylesheet');
  }

  public function getImageFile(string $file) : string {
    return $this->getFile('images', $file);
  }

  public function getStyleSheetFile(string $file) : string {
    return $this->getFile('stylesheet', $file);
  }

  public function getJavaScriptFile(string $file) : string {
    return $this->getFile('javascript', $file);
  }

  public function getRawFile(string $file) : string {
    return $this->themeRoot.'/'.$file;
  }

  public function getFile(string $type, string $file) : string {
    return $this->themeRoot.'/'.$this->themeInfo[$type].'/'.$file;
  }

  private function loadResourceTypes(string $type) : array {
    $resFolder = $this->themeRoot.'/'.$this->themeInfo[$type];
    $resFiles = [];

    for($i = 0; $i < count($this->themeInfo[$type.'_files']); $i++) {
      array_push($resFiles, $resFolder.'/'.$this->themeInfo[$type.'_files'][$i]);
    }

    return $resFiles;
  }
}
