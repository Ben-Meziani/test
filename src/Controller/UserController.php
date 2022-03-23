<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

        /**
     * @Route("/user/profil/edit", name="user_profile_edit")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            self::uploadImage($form, $user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Profile updated');
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/editprofile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
 /**
     * @Route("/user/pass/edit", name="user_pass_edit")
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            if($request->request->get('pass') == $request->request->get('pass2')){
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('pass')));
                $em->flush();
                $this->addFlash('message', 'Password updated with success');

                return $this->redirectToRoute('app_user');
            }else{
                $this->addFlash('error', 'The 2 passwords are not identicals');
            }
        }

        return $this->render('user/editpass.html.twig');
    }

    public function uploadImage($form, $user)
    {
        $documents = $form->get('documents')->getData();
        foreach($documents as $document){
            $file = md5(uniqid()).'.'.$document->guessExtension();
            $document->move(
                $this->getParameter('documents_directory'),
                $file
            );
            
            $doc = new Document();
            $doc->setName($file);
            $user->addDocument($doc);
        }
    }

    /**
     * @Route("/delete/document/{id}", name="user_delete_document", requirements={"id" = "\d+"}, methods={"DELETE"})
     */
    public function deleteDocument(Document $document, Request $request){
        $data = json_decode($request->getContent(), true);
        if($this->isCsrfTokenValid('delete'.$document->getId(),$data['_token'])){
            $name =$document->getName();
            unlink($this->getParameter('documents_directory').'/'.$name);
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($document);
            $manager->flush();
            return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}
