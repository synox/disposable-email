<?php
require_once 'vendor/autoload.php';
require 'app/config.php';

// setup slim & twig
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => 'app/templates',
    'slim.url_scheme' => 'https',
));

$app->error(function (\Exception $e) use ($app) {
    error_log($e->getMessage());
    $app->callErrorHandler($e);
});

$app->view()->parserOptions = array(
    'cache' => __DIR__ . '/cache'
);
$app->view()->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new DisposableEmail\AutoLinkTwigExtension()
);

// add html2Text filter
$app->view()->getInstance()->addFilter(new Twig_SimpleFilter('html2Text', function ($string) {
    $convert = new \Html2Text\Html2Text($string);
    return $convert->get_text();
}));

// redirect to random address
$app->get('/', function () use ($app) {
    $wordLength = rand(3, 8);
    $container = new PronounceableWord_DependencyInjectionContainer();
    $generator = $container->getGenerator();
    $word = $generator->generateWordOfGivenLength($wordLength);
    $nr = rand(51, 91);
    $name = $word . $nr ;

    $app->redirect($app->urlFor('read', array('name' => $name)));
})->name('home');

// switch to other accout using form
$app->post('/switch', function () use ($app) {
    $name = preg_replace('/@.*$/', '', $app->request->params('name'));
    $app->redirect($app->urlFor('read', array('name' => $name)));
})->name('switch');

// ajax check to see if there is new mail
$app->get('/check/:name', function ($name) use ($app) {
    $emails = R::find( 'mail', 'username = ?', array( $name ) );
    print sizeOf($emails);
})->name('mailcount');

// delete an email
$app->get('/:name/delete/:id', function ($name, $id) use ($app) {
    
    $email = R::findOne( 'mail', ' username = ? AND id = ? ', array( $name, $id ) );
    if(! is_null($email)) {
        R::trash( $email );
    }
    $app->redirect($app->urlFor('read', array('name' => $name)));
})->name('delete');

// read original source
$app->get('/:name/source/:id', function ($name, $id) use ($app) {
    $email = R::findOne( 'mail', ' username = ? AND id = ? ', array( $name, $id ) );
    if(! is_null($email)) {
        $app->contentType("text/plain");
        echo $email->raw;
    } else {
      $app->notFound();
    }
})->name('source');

// show html
$app->get('/:name/html/:id', function ($name, $id) use ($app) {
    $email = R::findOne( 'mail', ' username = ? AND id = ? ', array( $name, $id ) );
    if(! is_null($email)) {
        $html_safe = preg_replace("/https?:\\/\\//i", URI_REDIRECT_PREFIX . '\\0', $email->body_html);
        
        
        echo $html_safe;
    } else {
      $app->notFound();
    }
})->name('html');

// read emails
$app->get('/:name/', function ($name) use ($app) {
    $name = preg_replace('/@.*$/', "", $name);    
    $address = $name . '@' .DOMAIN;

    // get messages
    $emails = R::find( 'mail', ' username = ? ORDER BY received DESC', array( $name ) );
    if ( $emails === NULL || count($emails) == 0) {
        $app->render('waiting.html',array('name' => $name, 'address' => $address));
    } else {
        $app->render('list.html',array('name' => $name, 'address' => $address, 'emails'=>$emails));
    }
})->name('read');

$app->run();

// cleanup
$deleteBefore = strtotime('30 days ago');
R::exec( 'delete from mail where received<?', [ $deleteBefore ] );

R::close();
