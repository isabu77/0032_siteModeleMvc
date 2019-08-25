<?php

namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\Helpers\TextController;
use \Core\Controller\FormController;
use App\Model\Entity\UserEntity;

/**
 * Classe UserInfosController : Gestion des coordonnées d'un utilisateur
 */
class UserInfosController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe UserTable dans la propriété
        // $this->users qui est créée dynamiquement
        $this->loadModel('user');
        $this->loadModel('userInfos');
        $this->loadModel('garden');
        $this->loadModel('action');
    }

    /**
     * lecture d'un client par son id (appelée par javascript en ajax)
     *
     */
    public function getClient()
    {
        $post = $_POST;
        if (!empty($post)) {
            if (isset($post["user_infos_id"])) {
                // lecture en base des clients du user
                $userInfos = $this->userInfos->find($post["user_infos_id"]);
                echo json_encode($userInfos->getProperties());
            }
        }
    }

    /**
     * mise à jour d'un profil d'utilisateur (user_Infos)
     * à partir d'un formulaire
     * @param ?array $post tableau $_POST passé par le router
     * @param ?int $user_id utilisateur 
     *
     */
    public function updateProfil(array $post, int $user_id)
    {
        //l'adresse' du user
        $user_Infos_id = 0;
        if (isset($post["user_infos_id"]) && is_numeric($post["user_infos_id"])) {
            $user_Infos_id = $post["user_infos_id"];
        }

        // le jardin
        $garden_id = 0;
        if (isset($post["garden_id"]) && is_numeric($post["garden_id"])) {
            $garden_id = $post["garden_id"];
        }

        // supprimer les champs inutiles pour l'enregistrement en base
        unset($post["garden_id"]);
        unset($post["user_infos_id"]);
        unset($post["price"]);
        unset($post["id"]);

        // traitement du formulaire des coordonnées
        if ($user_Infos_id != 0){
            $form = new FormController($post);
            $errors = $form->hasErrors();
            if (!isset($this->errors['post']) ||  $errors["post"] != "no-data") {
                $form->field('lastname', ["require"]);
                $form->field('firstname', ["require"]);
                $form->field('address', ["require"]);
                $form->field('zipCode', ["require"]);
                $form->field('city', ["require"]);
                $form->field('country', ["require"]);
                $form->field('phone', ["require"]);
                $errors = $form->hasErrors();
                if (empty($errors)) {
                    $datas = $form->getDatas();
                    $attributes =
                    [
                        "lastname"     => htmlspecialchars($datas['lastname']),
                        "firstname"    => htmlspecialchars($datas['firstname']),
                        "address"      => htmlspecialchars($datas['address']),
                        "zip_code"      => htmlspecialchars($datas['zipCode']),
                        "city"         => htmlspecialchars($datas['city']),
                        "country"      => htmlspecialchars($datas['country']),
                        "phone"        => htmlspecialchars($datas['phone'])
                    ];

                    // enregistrement des coordonnées dans user_infos
                     if (isset($post["new"]) || $user_Infos_id == 0) {
                        // nouvelle adresse
                        unset($post["new"]);
                        $datas["user_id"] = $user_id;
                        $res = $this->userInfos->insert($attributes);
                        if ($res) {
                            //message modif ok
                            $this->getFlashService()->addSuccess("les coordonnées ont bien été ajoutées");
                        } else {
                            $this->getFlashService()->addAlert("les coordonnées n'ont pas été ajoutées");
                        }
                        return $this->userInfos->last();
                    } else {
                        // update du client dans la table client
                        $res = $this->userInfos->update($user_Infos_id, $attributes);
                        if ($res) {
                            //message modif ok
                            $this->getFlashService()->addSuccess("les coordonnées ont bien été modifiées");
                        } else {
                            $this->getFlashService()->addAlert("les coordonnées n'ont pas été modifiées");
                        }
                        return $user_Infos_id;
                    }
                } else {
                    foreach ($errors as $value) {
                        $this->getFlashService()->addAlert($value);
                    }
                    return null;
                }
            }
        }   
        // traitement du formulaire des coordonnées du JARDIN
        if ($garden_id != 0){
            $form = new FormController($post);
            $errors = $form->hasErrors();
            if (!isset($this->errors['post']) ||  $errors["post"] != "no-data") {
                $form->field('garden_name', ["require"]);
                $form->field('garden_latitude', ["require"]);
                $form->field('garden_longitude', ["require"]);
                $form->field('garden_address', ["require"]);
                $form->field('garden_zipCode', ["require"]);
                $form->field('garden_city', ["require"]);
                $errors = $form->hasErrors();
                if (empty($errors)) {
                    $datas = $form->getDatas();
                    $attributes =
                    [
                        "name"     => htmlspecialchars($datas['garden_name']),
                        "latitude"     => $datas['garden_latitude'],
                        "longitude"     => $datas['garden_longitude'],
                        "address"     => htmlspecialchars($datas['garden_address']),
                        "zip_code"     => htmlspecialchars($datas['garden_zipCode']),
                        "city"     => htmlspecialchars($datas['garden_city']),
                    ];

                    // enregistrement des coordonnées dans garden

                    if (isset($post["new"]) || $garden_id == 0) {
                        // nouvelle adresse

                        $res = $this->garden->insert($attributes);
                        if ($res) {
                            //message modif ok
                            $this->getFlashService()->addSuccess("le jardin a bien été ajouté");
                        } else {
                            $this->getFlashService()->addAlert("le jardin n'a pas été ajouté");
                        }
                        return $this->garden->last();

                    } else {

                        // update du jardin
                        $res = $this->garden->update($garden_id, $attributes);
                        if ($res) {
                            //message modif ok
                            $this->getFlashService()->addSuccess("le jardin a bien été modifié");
                        } else {
                            $this->getFlashService()->addAlert("le jardin n'a pas été modifié");
                        }
                        return $garden_id;
                    }
                } else {
                    foreach ($errors as $value) {
                        $this->getFlashService()->addAlert($value);
                    }
                    return null;
                }
            }
        }   
    }

    /**
     * page profil d'un utilisateur
     * @param ?array $post tableau $_POST passé par le router
     * @param ?int $user_Infos_id coordonnées à afficher 
      *
     */
    public function profil(?array $post = null, int $user_Infos_id = null)
    {
        // l'utilisateur connecté
        $userConnect = $this->getApp()->getConnectedUser();
 
        // et son jardin
        $garden = $this->garden->findBy("user_id", $userConnect->getId(), true);

        // traitement de la modification du profil
        if (isset($post) && !empty($post)) {
            if (isset($post["passwordOld"]) && !empty($post["passwordOld"]) &&
                isset($post["password"]) && !empty($post["password"]) &&
                isset($post["passwordVerify"]) && !empty($post["passwordVerify"])
            ) {
                // traitement du formulaire de changement de password
                $form = new FormController();
                $errors = $form->hasErrors();
                if (!isset($this->errors['post']) ||  $errors["post"] != "no-data") {
                    $form->field('password', ["require", "verify"]);
                    $form->field('passwordOld', ["require"]);
                    $errors = $form->hasErrors();
                    if (empty($errors)) {
                        $datas = $form->getDatas();

                        // vérifier l'existence du user en base
                        $user = $this->user->getUser($userConnect->getMail(), $datas['passwordOld']);
                        if ($user) {
                            // modification du mot de passe en base
                            $password = password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT);

                            $res = $this->user->update($userConnect->getId(), ["password" => $password]);

                            if ($res) {
                                //message modif ok
                                $this->getFlashService()->addSuccess('Votre mot de passe a bien été modifié');
                            } else {
                                $this->getFlashService()->addAlert("Votre mot de passe n'a pas été modifié");
                            }
                        } else {
                            //mdp correspondent pas
                            $this->getFlashService()->addAlert('Les deux mots de passe ne correspondent pas.');
                        }
                    } else {
                        //erreur
                        $this->getFlashService()->addAlert('Mot de passe incorrect');
                    }
                }
            } elseif (isset($post["user_infos_id"]) && !empty($post["user_infos_id"])
                && $userConnect != null
            ) {
                // enregistrement des coordonnées dans user_infos
                $user_Infos_id = $this->updateProfil($post, $userConnect->getId());
                header('Location: '.$this->getUri("profil"));
                exit();
            } elseif (isset($post["garden_id"]) && !empty($post["garden_id"])
            && $userConnect != null
        ) {
            // enregistrement des coordonnées dans user_infos
            $user_Infos_id = $this->updateProfil($post, $userConnect->getId());
            header('Location: '.$this->getUri("profil"));
            exit();
        }
        }

        // lire les infos associées à l'utilisateur 
        $infos = $this->userInfos->getUserInfosByUserId($userConnect->getId());

        $info = null;
        if ($user_Infos_id) {
            $info = $this->userInfos->find($user_Infos_id);
        } else {
            if (count($infos) > 0) {
                $info = $this->userInfos->find($infos[0]->getId());
            }
        }

        // Pagination des travaux
        $paginatedQuery = new PaginatedQueryAppController(
            $this->action,
            $this->generateUrl('profil')
        );

        $actions = $paginatedQuery->getItemsInIdToday($garden->getId());

        $title = $userConnect->getMail();

        // afficher la vue
        return $this->render('users/profil', [
            'user'  => $info,
            'clients' => $infos,
            'garden' => $garden,
            'title' => $title,
            'paginate' => $paginatedQuery->getNavHtmlInIdToday($garden->getId()),
            'actions' => $actions
        ]);
    }
}
