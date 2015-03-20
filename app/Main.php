<?php
chdir(__DIR__);
require_once '../vendor/autoload.php';
require 'config.php'; // setup db & constants

// setup slim & twig
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => './templates',
    'slim.url_scheme' => 'https',
));

$app->error(function (\Exception $e) use ($app) {
    error_log($e->getMessage());
    $app->callErrorHandler($e);
});



$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__DIR__) . '/cache'
);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new DisposableEmail\AutoLinkTwigExtension()
);
// add html2Text filter
$view->getInstance()->addFilter(new Twig_SimpleFilter('html2Text', function ($string) {
    $convert = new \Html2Text\Html2Text($string);
    return $convert->get_text();
}));


// setup routing
$app->get('/', function () use ($app) {
    // redirect to random address
    $wordLength = rand(3, 8);
    $container = new PronounceableWord_DependencyInjectionContainer();
    $generator = $container->getGenerator();
    $word = $generator->generateWordOfGivenLength($wordLength);
    $nr = rand(51, 91);
    $name = $word . $nr ;

    $app->redirect($app->urlFor('list', array('name' => $name)));
})->name('home');


$app->post('/switch', function () use ($app) {
    // switch to other accouting using form
    $name = $app->request->params('name');
    $name = str_replace("@".DOMAIN, "", $name);
    $app->redirect($app->urlFor('list', array('name' => $name)));
})->name('switch');




 $app->get('/:name/delete/:id', function ($name, $id) use ($app) {
    // delete an email
    $email = R::findOne( 'mail', ' username = ? AND id = ? ', array( $name, $id ) );
    if(! is_null($email)) {
        R::trash( $email );
    }
    $app->redirect($app->urlFor('list', array('name' => $name)));
})->name('delete');

 $app->get('/:name/source/:id', function ($name, $id) use ($app) {
    // delete an email
    $email = R::findOne( 'mail', ' username = ? AND id = ? ', array( $name, $id ) );
    if(! is_null($email)) {
        $app->contentType("text/plain");
        echo $email->raw;
    }
})->name('source');


$app->get('/:name', function ($name) use ($app) {
  // show inbox
    
    // enable account
    $account  = R::findOne( 'account', ' username = ? ', array( $name ));
    if($account == NULL) {
        // first time usage, create
        $account = R::dispense( 'account' );
        $account->username = $name;
        $account->blocked = FALSE;
        $account->created = time();
    }
    $id = R::store( $account );
    $address = $name . '@' .DOMAIN;

    // get messages
    $emails = R::find( 'mail', ' username = ? ORDER BY received DESC', array( $name ) );
    if ( $emails === NULL || count($emails) == 0) {
        $app->render('waiting.html',array('name' => $name, 'address' => $address));
    } else {
        $app->render('list.html',array('name' => $name, 'address' => $address, 'emails'=>$emails));
    }
})->name('list');



$app->run();

// cleanup
$deleteBefore = strtotime('30 days ago');
R::exec( 'delete from mail where received<?', [ $deleteBefore ] );

R::close();
