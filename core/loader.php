<?hh
namespace core;

/* Normal Routes */
require 'core/routes/homecontroller.php';
require 'core/routes/postcontroller.php';
require 'core/routes/rsscontroller.php';

/* Admin Routes */
require 'core/routes/admin/homecontroller.php';
require 'core/routes/admin/postcontroller.php';

/* test routes purge on release */
require 'core/routes/debug/accountcreate.test.controller.php';
require 'core/routes/debug/accountlogin.test.controller.php';

/* Factories */
require 'core/factory/postfactory.php';
require 'core/factory/userfactory.php';

/* engine files */
require 'core/engine/user/account.php';
require 'core/engine/theme/thememanager.php';
require 'core/extensions/themefunctions.php';

/* middlware files */
require 'core/engine/middleware/permissioncheck.php';
require 'core/engine/middleware/adminlinkbuilder.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class loader {
    private \Slim\App $router;
    private $container; /* Can't solve given type */

    /* publics */
    public function __construct() : void {
        $this->router = new \Slim\App();
        $this->container = $this->router->getContainer();

        $this->loadMiddleware();
        $this->setupContainers();
        $this->loadMVCViews();
        $this->injectRouting();
    }

    public function addRoute(string $path, string $func, string $name) : mixed {
        return $this->router->get($path, $func)->setName($name);
    }

    public function run() : void {
        $this->router->run();
    }

    /* privates */
    private function loadMiddleware() : void {
        $this->router->add(new \Slim\Middleware\Minify());
    }

    private function loadMVCViews() : void {
        $this->container[\core\routes\homecontroller::class] = function($c) {
            return new \core\routes\homecontroller($c);
        };

        $this->container[\core\routes\postcontroller::class] = function($c) {
            return new \core\routes\postcontroller($c);
        };

        $this->container[\core\routes\rsscontroller::class] = function($c) {
            return new \core\routes\rsscontroller($c);
        };

        /* Admin Routes */
        $this->container[\core\routes\admin\homecontroller::class] = function($c) {
          return new \core\routes\admin\homecontroller($c);
        };

        $this->container[\core\routes\admin\postcontroller::class] = function($c) {
          return new \core\routes\admin\postcontroller($c);
        };

        /* we need to hide this later */
        $this->container[\core\routes\debug\accountcreatecontroller::class] = function($c) {
            return new \core\routes\debug\accountcreatecontroller($c);
        };

        $this->container[\core\routes\debug\accountlogincontroller::class] = function($c) {
            return new \core\routes\debug\accountlogincontroller($c);
        };
    }

    private function injectRouting() : void {
      $rt = $this;

      $this->router->group('', function() use ($rt) {
        $rt->addRoute('[/]', \core\routes\homecontroller::class, 'home');
        $rt->addRoute('/rss', \core\routes\rsscontroller::class, 'rss');

        $rt->addRoute('/post[/{pid:[0-9]+}]', \core\routes\postcontroller::class, 'post');
        $rt->addRoute('/news[/{pid:[0-9]+}]', \core\routes\postcontroller::class, 'news');
      });

      // NOTE: admin.panel.view is used for everyone even authors to view the admin panel and create new post in the site
      //       meaning that are news team will need this permission in-order to new news to the website
      $this->router->group('/admin', function() use ($rt) {
        $rt->addRoute('[/]', \core\routes\admin\homecontroller::class, 'admin-home');

        $rt->router->map(['GET', 'POST'], '/posts[/{type}[/{pid:[0-9]+}]]', \core\routes\admin\postcontroller::class)->setName('edit-post')
                   ->add(new \core\engine\middleware\permissioncheck($rt->container, 'admin.panel.modify_post'));

        $rt->router->get('/users[/{type}[/{uid:[0-9]+}]]', \core\routes\admin\homecontroller::class)->setName('edit-user')
                   ->add(new \core\engine\middleware\permissioncheck($rt->container, 'admin.panel.modify_user'));

        $rt->router->get('/permissions[/{type}[/{gid:[0-9]+}]]', \core\routes\admin\homecontroller::class)->setName('permissions-groups')
                              ->add(new \core\engine\middleware\permissioncheck($rt->container, 'admin.panel.modify_permissions'));

      })->add(new \core\engine\middleware\permissioncheck($this->container, 'admin.panel.view'))
        ->add(new \core\engine\middleware\adminlinkbuilder($this->container));

      if(file_exists('debug_mode')) {
        // NOTE: (debug.tools.use) hidden permission cannot be set in panel
        $this->router->group('/debug', function() use ($rt) {
          $rt->addRoute('/create_account', \core\routes\debug\accountcreatecontroller::class, 'debug_acct_create');
          $rt->addRoute('/login_account', \core\routes\debug\accountlogincontroller::class, 'debug_acct_login');
        });/*->add(new \core\engine\middleware\permissioncheck($this->container, 'debug.tools.use'));*/
      }
    }

    private function setupContainers() : void {
        $this->container['settings']['displayErrorDetails'] = true;

        // let set up a system to let this be changed on the server at a later time maybe to a local.config.php on all servers that need it
        $this->container['database'] = new \MysqliDb (MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        
        $this->container['theme'] = function($c) {
            $tm = new \core\engine\theme\thememanager('resources/theme');
            $tm->setDefaultTheme('example');
            return $tm;
        };
        // Register component on container
        $this->container['view'] = function ($container) {
            $view = new \Slim\Views\Twig($container['theme']->getDefaultTheme()->getThemeFolder(), [
                'cache' => false,
                'debug' => true
            ]);

            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
            $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));

            /* load any custom twig extenstions here that we need */
            $view->addExtension(new \core\extensions\themefunctions($container['theme']->getDefaultTheme()));

            $view['js_files'] = $container['theme']->getDefaultTheme()->getJavaScript();
            $view['css_files'] = $container['theme']->getDefaultTheme()->getStyleSheets();

            $view['pt'] = new \joshtronic\LoremIpsum();
            $view['site_title'] = 'OverTells - %s'; // will change later to make it have it's own function like get_page_title('page_title') -> Overtells - {Title}
            return $view;
        };

        /* TODO: Clean these up one day so there much smooth in operation to add them */
        $this->container['postfactory'] = function($c) {
            return new \core\factory\postfactory($c);
        };

        $this->container['userfactory'] = function($c) {
            return new \core\factory\userfactory($c);
        };

        $this->container['user'] = function($c) {
            return new \core\engine\user\account($c, $this->container['view']);
        };
    }
}
