<?php
namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Form\UserVerifyType;
use AppBundle\Form\UserResetType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    const tokenLength = 10;
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        if($this->checkIsAlreadyLoggedIn()) {
            $this->addFlash(
                'error',
                'You are already logged in.'
            );
            return $this->redirectToRoute('news_index');
        }
        // build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        //  handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // save the User!
            $random =  base64_encode(random_bytes(self::tokenLength));
            $user->setActivationCode($random);
            $user->setIsActive(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $baseUrl = $request->getScheme() . '://' . $request->getHttpHost();
            $this->sendEmail('News portal verification link',$user->getEmail(),'Emails/registration.html.twig', array('name' => $user->getFirstname().' '.$user->getLastname(), 'activationCode' => $random, 'baseUrl' =>$baseUrl ));
            $this->addFlash(
                'notice',
                'Registration complete,Please check your inbox for verification link.'
            );
            return $this->redirectToRoute('news_index');
        }
        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("/forgot_password", name="forgot_password")
     */
    public function forgotPasswordAction(Request $request)
    {
        if($this->checkIsAlreadyLoggedIn()) {
            $this->addFlash(
                'error',
                'You are already logged in.'
            );
            return $this->redirectToRoute('news_index');
        }
        // build the form
        $user = new User();
        $form = $this->createForm(UserResetType::class, $user);

        //  handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findOneBy( array('email' => $user->getEmail()));
            if(!empty($user)) {
                $random =  base64_encode(random_bytes(self::tokenLength));
                $user->setActivationCode($random);
                $user->setIsActive(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $baseUrl = $request->getScheme() . '://' . $request->getHttpHost();
                $this->sendEmail('Reset Password Link',$user->getEmail(),'Emails/reset_password.html.twig', array('name' => $user->getFirstname().' '.$user->getLastname(), 'activationCode' => $random, 'baseUrl' =>$baseUrl ));
                $this->addFlash(
                    'notice',
                    'password change link sent. please check inbox'
                );
            }
            else {
                $this->addFlash(
                    'error',
                    'Account does not exists. Please enter valid email address.'
                );
            }

            return $this->redirectToRoute('news_index');
        }
        return $this->render(
            'registration/forgot_password.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("verification/{activation_code}", name="user_verification")
     */
    public function verificationAction(Request $request, $activation_code )
    {
        if($this->checkIsAlreadyLoggedIn()) {
            $this->addFlash(
                'error',
                'You are already logged in.'
            );
            return $this->redirectToRoute('news_index');
        }
        if(!empty($activation_code)) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findOneBy( array('activation_code' => $activation_code));
            if(!empty($user)) {
                $form = $this->createForm(UserVerifyType::class, $user);

                // handle the submit (will only happen on POST)
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                        $password = $this->get('security.password_encoder')
                            ->encodePassword($user, $user->getPlainPassword());
                        $user->setPassword($password);
                        $user->setActivationCode('');
                        $user->setIsActive(true);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($user);
                        $em->flush();
                        $this->addFlash(
                            'notice',
                            'Password saved successfully. please login to your account.'
                        );
                        return $this->redirectToRoute('login');
                }

                return $this->render(
                    'registration/change_password.html.twig',
                    array('form' => $form->createView())
                );
            }

        }
        $this->addFlash(
            'error',
            'Invalid activation code.'
        );
        return $this->redirectToRoute('news_index');

    }

    private function checkIsAlreadyLoggedIn() {
        if($this->get('security.context')->getToken()->getRoles()) {
            return true;
        }
        return false;
    }

    private function sendEmail($subject, $emailId, $templatePath, $data){


        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('from_email'))
            ->setTo($emailId)
            ->setBody(
                $this->renderView(
                    $templatePath, $data
                ),
                'text/html'
            );
        return $this->get('mailer')->send($message);
    }

}