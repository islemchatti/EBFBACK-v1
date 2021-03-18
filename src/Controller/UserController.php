<?php


namespace App\Controller;


use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UserController extends AbstractFOSRestController
{
    /**
     * @Route("/api/users/{id}",name="getUser", methods={"GET"})
     */
    public function GetUtilisateur(SerializerInterface $serializer, User $utilisateur){

        $data = $serializer->serialize($utilisateur, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * @Route("/api/users",name="getUsers", methods={"GET"})
     */
    public function GetUsers(SerializerInterface $serializer){
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        $data = $serializer->serialize($users,'json');
        $response = new Response($data);
        $response->headers->set('Content-Type','application/json');
        return $response;

    }

    /**
     * @Route("/register",name="createUser",methods={"POST"})
     */
    public function Register(UserPasswordEncoderInterface $passwordEncoder, Request $request){
        $data = json_decode(
            $request->getContent(), true//raw
        );
        $matricule = $data["matricule"];
        $cin = $data["cin"];
        $nom= $data["nom"];
        $prenom = $data["prenom"];
        $email = $data["email"];
        $tel = $data["tel"];
        $adresse = $data["adresse"];
        $cnss = $data["cnss"];

        $user = new User();

        // $user = $this->getUser(); prend l'utilisateur connecté (token)
        // $passwordEncoder->isPasswordValid(user, $password); vérifie si le mdp est correct
        $user->setMatricule($matricule);
        $user->setCin($cin);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setTel($tel);
        $user->setEmail($email);
        $user->setCnss($cnss);
        $user->setAdresse($adresse);
        $user->setRoles(['ROLE_USER']);
        $act=0;
        $user->setActive($act);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $user_ = $em->getRepository(User::class)->findOneBy(["cin" => $cin]);
        return new JsonResponse(["cin" => $user_->getCin() ], 201);
    }

    /**
     * @Route("/api/users/{id}", name="deleteUser", methods={"DELETE"})
     */

    public function supprimer(User $utilisateur){
        $user=$this->getUser();
        if(in_array("ROLE_ADMIN", $user->getRoles())){
            if ($utilisateur){
                $em = $this->getDoctrine()->getManager();
                $em->remove($utilisateur);
                $em->flush();
                return new JsonResponse([], 200);
            }
            else{
                return new JsonResponse([], 404);
            }

        }
        else {
            return new JsonResponse([], 403);
        }


    }

    /**
     * @Route("/api/users/{id}", name="updateUser", methods={"PUT"})
     */

    public function update(User $utilisateur,Request $request, UserPasswordEncoderInterface $passEncoder){
        $user=$this->getUser();
        $data = json_decode(
            $request->getContent(), true//raw
        );
        $username = $data["username"];
        $password = $data["password"];
        $matricule = $data["matricule"];
        $cin = $data["cin"];
        $nom= $data["nom"];
        $prenom = $data["prenom"];
        $email = $data["email"];
        $tel = $data["tel"];
        $adresse = $data["adresse"];
        $cnss = $data["cnss"];

        if(in_array("ROLE_ADMIN", $user->getRoles())){
            if ($utilisateur){
                $utilisateur->setUsername($username);
                $utilisateur->setPassword($passEncoder->encodePassword($utilisateur,$password));
                $utilisateur->setMatricule($matricule);
                $utilisateur->setCin($cin);
                $utilisateur->setNom($nom);
                $utilisateur->setPrenom($prenom);
                $utilisateur->setTel($tel);
                $utilisateur->setEmail($email);
                $utilisateur->setCnss($cnss);
                $utilisateur->setAdresse($adresse);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return new JsonResponse([], 200);
            }
            else{
                return new JsonResponse([], 404);
            }

        }
        else {
            return new JsonResponse([], 403);
        }
    }

    /**
     * @Route("/api/users/activate/{id}", name="activate", methods={"PUT"})
     */
    public function Activer(User $utilisateur){
        if ($utilisateur){$utilisateur->setActive(1);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return new JsonResponse([], 200);
        }
        else{
            return new JsonResponse([], 404);
        }


    }
    /**
     * @Route("/api/users/desactivate/{id}", name="desactivate", methods={"PUT"})
     */
    public function Desactiver(User $utilisateur){
        if ($utilisateur){
            $utilisateur->setActive(0);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return new JsonResponse([], 200);
        }
        else{
            return new JsonResponse([], 404);
        }


    }


}