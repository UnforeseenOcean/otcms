<?hh
namespace core\extensions;

use \core\engine\theme\themecore as tc;

class themefunctions extends \Twig_Extension {
  private tc $themeCore;

  public function __construct(tc $core): void {
    $this->themeCore = $core;
  }

  public function getName() {
    return 'theme';
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('get_js', array($this, 'get_js')),
      new \Twig_SimpleFunction('get_image', array($this, 'get_image')),
      new \Twig_SimpleFunction('get_css', array($this, 'get_css')),
      new \Twig_SimpleFunction('get_raw_file', array($this, 'get_raw_file')),
      new \Twig_SimpleFunction('json_encode', array($this, 'json_enc')),
    ];
  }

  public function json_enc(array $data, bool $use_br = false) : string {
    $raw_Json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if($use_br) {
      return nl2br($raw_Json);
    } else {
      return $raw_Json;
    }
  }

  public function get_image(string $file): string {
    return $this->themeCore->getImageFile($file);
  }

  public function get_js(string $file): string {
    return $this->themeCore->getJavaScriptFile($file);
  }

  public function get_css(string $file): string {
    return $this->themeCore->getStyleSheetFile($file);
  }

  public function get_raw_file(string $file) : string {
    return $this->themeCore->getRawFile($file);
  }
}
