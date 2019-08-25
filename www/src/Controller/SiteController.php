<?php

namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

/**
 * Classe SiteController : Le site d'un utilisateur
 */
class SiteController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // chargement de la classe du modèle représentant la table de même nom
        $this->loadModel('action');

    }

    /**
     * La page accueil du site
     */
    public function index()
    {
        $title = '';
 
        // Pagination
        $navHtml = "";
        $actions = [];

        // l'utilisateur connecté (facultatif)
        $userConnect = $this->getApp()->getConnectedUser(false);

        if ($userConnect) {

            $paginatedQuery = new PaginatedQueryAppController(
                $this->action,
                $this->generateUrl('home')
            );

            $actions = $paginatedQuery->getItemsInIdToday($userConnect->getId());
            $navHtml = $paginatedQuery->getNavHTML();
        }

        // afficher la vue
        return $this->render('site/index', [
            'title' => $title,
            'paginate' => $navHtml,
            'actions' => $actions
        ]);
    }

    /**
     * les mentions légales
     *
     */
    public function mentions()
    {
        $title = 'Mentions légales';
        // afficher la vue
        return $this->render('Site/mentions', [
            'title' => $title
        ]);
    }

   
    /**
     * à propos de...
     *
     */
    public function about()
    {
        // la météo de Montluçon avec l'API openweathermap
        // http://api.openweathermap.org/data/2.5/weather?q=Montlucon,fr&units=metric&APPID=API_OPENWEATHER

        // ne fonctionne pas :
        // $request = new HttpRequest();
        // $request->setUrl('http://api.openweathermap.org/data/2.5/weather');
        // $request->setMethod(HTTP_METH_GET);

        // $request->setQueryData(array(
        //   'q' => 'Montlucon,fr',
        //   'APPID' => \App\App::getInstance()->getEnv(API_OPENWEATHER),
        //   'units' => 'metric',
        //   'lang'  => 'fr'
        // ));

        // $request->setHeaders(array(
        //   'cache-control' => 'no-cache',
        //   'Connection' => 'keep-alive',
        //   'Accept-Encoding' => 'gzip, deflate',
        //   'Host' => 'api.openweathermap.org',
        //   'Postman-Token' => 'f57e4cf5-e46a-4e70-a4f2-9e8e6139d97b,436e3647-9816-4095-948f-0ec1b435d4d7',
        //   'Accept' => '*/*',
        //   'User-Agent' => 'PostmanRuntime/7.15.2'
        // ));

        // try {
        //   $response = $request->send();

        //   $meteo =  $response->getBody();
        // } catch (HttpException $ex) {
        //   echo $ex;
        // }    

        // ne fonctionne pas non plus:
        // $response = \http_get("http://api.openweathermap.org/data/2.5/weather?q=Montlucon,fr&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr");
        // $meteo = json_decode($response);

        // //===== ne fonctionne plus (mais fonctionne dans script.js au clic sur le marqueur) =================
        $meteo="";
        // l'appid est dans config.php
        //$appid = \App\App::getInstance()->getEnv('API_OPENWEATHER');

        // la météo de Montluçon avec l'API
        // http://api.openweathermap.org/data/2.5/weather?q=Montlucon,fr&units=metric&APPID=API_OPENWEATHER

        //$url = "https://api.openweathermap.org/data/2.5/weather?q=Montlucon,fr&units=metric&APPID=".$appid."&lang=fr";

        // l'utilisateur connecté (facultatif)
        // $userConnect = $this->getApp()->getConnectedUser(false);
        // if ($userConnect) {
        //     // et son jardin
        //     $Site = $this->Site->findBy("user_id", $userConnect->getId(), true);
        //     if ($Site && $Site->getLatitude() != null && $Site->getLongitude() != null){
        //         // $url = "http://api.openweathermap.org/data/2.5/weather?lat=".$Site->getLatitude()."&lon=".$Site->getLongitude()."&units=metric&APPID=".$appid."&lang=fr";
        //         $url = "https://api.openweathermap.org/data/2.5/weather?q=".$Site->getCity().",fr&units=metric&APPID=".$appid."&lang=fr";
        //     }
        // }
        //dump($url);

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        //     CURLOPT_POSTFIELDS => "",
        //     CURLOPT_HTTPHEADER => array(
        //         // "Postman-Token: d6090321-c068-4856-ad52-f2a4ee18da49"
        //         // "cache-control: no-cache"
        //     )
        // ));

        // //===== ne fonctionne plus (mais fonctionne dans script.js au clic sur le marqueur) =================
        // $response = curl_exec($curl);

        // $err = curl_error($curl);

        // curl_close($curl);

        // if ($err) {
        //     $meteo = "cURL Error #:" . $err;
        //     //echo "cURL Error #:" . $err;
        // } else {
        //     $meteo = json_decode($response);
        // }
        //dump($meteo);

        $title = 'Carte des sites';

        // tous les jardins
        //$Sites = $this->Site->all();

        // afficher la vue
        return $this->render('Site/about', [
            'meteo' => $meteo,
            // 'Sites' => $Sites,
            'title' => $title
        ]);
    }

    /**
     * les travaux d'un utilisateur
     * @param ?array $post tableau $_POST passé par le router
     *
     */
    public function action(?array $post = null)
    {
        // l'utilisateur connecté
        $userConnect = $this->getApp()->getConnectedUser();

        if (isset($post) && !empty($post)) {
            // supression d'une action demandée par ajax
            if (isset($post["idDelete"])) {
                // supprimer la ligne de plantation et renvoyer au javascript
                echo $this->action->delete($post["idDelete"]);
                return;
            } else {

                // traitement du formulaire d'ajout d'une action
                $form = new FormController();
                $errors = $form->hasErrors();

                if (!isset($errors['post']) ||  $errors["post"] != "no-data") {
                    $form->field('title', ["require"])
                        ->field('content', ["require"])
                        ->field('limited_at', ["require"]);
                    $errors = $form->hasErrors();

                    if (empty($errors)) {
                        $datas = $form->getDatas();

                        $attributes =
                            [
                                "user_id"      => $userConnect->getId(),
                                "title"     => htmlspecialchars($datas['title']),
                                "limited_at"    => $datas['limited_at'],
                                "content"      => htmlspecialchars($datas['content'])
                            ];
                        // ajouter l'action en base
                        $res = $this->action->insert($attributes);
                        if ($res) {
                            header('Location: ' . $this->getUri("action"));
                            exit();
                        }
                    }
                }
            }
        }
        
        // toutes les actions (titres) pour les combobox de choix
        $actionTitles = $this->action->allGroupedByTitle();
        $actionContents = $this->action->allGroupedByContent();

        // Pagination

        $paginatedQuery = new PaginatedQueryAppController(
            $this->action,
            $this->generateUrl('action')
        );

        $actions = $paginatedQuery->getItemsInIdToday($userConnect->getId());

        $title = 'Les travaux';

        // afficher la vue
        return $this->render('Site/action', [
            'title' => $title,
            'user' => $userConnect,
            'paginate' => $paginatedQuery->getNavHtmlInIdToday($userConnect->getId()),
            'actions' => $actions,
            'actionTitles' => $actionTitles,
            'actionContents' => $actionContents
        ]);
    }
}
