<?php
// src/AppBundle/Controller/Security/ReaderAccessController.php
namespace AppBundle\Controller\Security;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use AppBundle\Controller\Utility\FormErrorHandlerTrait,
    AppBundle\Form\Model\Registration,
    AppBundle\Form\Type\RegistrationType,
    AppBundle\Form\Model\Reset,
    AppBundle\Form\Type\ResetType;

class ReaderAccessController extends Controller
{
    use FormErrorHandlerTrait;

    const MESSAGE_REGISTRATION = "message_registration";
    const MESSAGE_RESET        = "message_reset";

    public function authorizationAction(Request $request)
    {
        if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') )
            return new Response();

        $authorizationError = $this->get('security.authentication_utils')->getLastAuthenticationError();

        $registrationForm    = $this->createForm(new RegistrationType, new Registration);
        $registrationMessage = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_REGISTRATION) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_REGISTRATION)[0]
            : NULL;

        $resetForm    = $this->createForm(new ResetType, new Reset, ['validation_groups' => 'reset']);
        $resetMessage = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_RESET) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_RESET)[0]
            : NULL;

        return $this->render('AppBundle:Common:authorization.html.twig', [
            'authorizationError'  => $authorizationError,
            'registrationForm'    => $registrationForm->createView(),
            'registrationMessage' => $registrationMessage,
            'resetForm'           => $resetForm->createView(),
            'resetMessage'        => $resetMessage
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/private_office/login_check",
     *      name="private_office_login_check",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     */
    public function loginCheckAction()
    {
        // This controller will not be executed, as the route is handled by the Security system
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/registration",
     *      name="registration",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     */
    public function registrationAction(Request $request)
    {
        $form = $this->createForm(new RegistrationType, new Registration);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_translator = $this->get('translator');

            $registration = $this->get('app.security.reader_security_handler')->registerReader($form);

            $email = [
                'from'    => [$this->container->getParameter('personal_email')['no_reply'] => $_translator->trans('default.from', [], 'emails')],
                'to'      => $registration->getReader()->getEmail(),
                'subject' => $_translator->trans("registration.subject", [], 'emails'),
                'body'    => $this->renderView('AppBundle:Email:registration.html.twig', [
                    'readerId'            => $registration->getReader()->getId(),
                    'registrationDigest'  => $registration->getReader()->getRegistrationDigest()
                ])
            ];

            if( $this->get('app.mailer_shortcut')->sendMail($email['from'], $email['to'], $email['subject'], $email['body']) ) {
                $message = [
                    'notification' => $_translator->trans("registration.success.step_1", [], 'responses')
                ];
            } else {
                $this->get('app.security.reader_security_handler')->cleanupFailedRegistration($registration->getReader()->getId());

                $message = [
                    'error' => $_translator->trans("registration.fail.mail", [], 'responses')
                ];
            }
        } else {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];
        }

        $this->get('session')->getFlashBag()->add(self::MESSAGE_REGISTRATION, $message);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/registration_confirm/{readerId}/{registrationDigest}",
     *      name="registration_confirm",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%", "readerId" = "\d+", "registrationDigest" = "[a-f0-9]{64}"}
     * )
     */
    public function registrationConfirmAction(Request $request, $readerId, $registrationDigest)
    {
        $_manager = $this->getDoctrine()->getManager();

        $reader = $_manager->getRepository('AppBundle:Reader')->findOneBy(['id' => $readerId, 'registrationDigest' => $registrationDigest]);

        if( !$reader )
            throw $this->createNotFoundException();

        $_translator = $this->get('translator');

        if( $reader->getRegistrationDigestDatetime() >= (new DateTime('now')) )
        {
            $reader
                ->setRegistrationDigest(NULL)
                ->setRegistrationDigestDatetime(NULL)
                ->setIsActive(TRUE)
            ;

            $_manager->persist($reader);

            $message = [
                'notification' => $_translator->trans("registration.success.step_2", [], 'responses')
            ];
        } else {
            $_manager->remove($reader);

            $message = [
                'error' => $_translator->trans("registration.fail.expired", [], 'responses')
            ];
        }

        $_manager->flush();

        $this->get('session')->getFlashBag()->add(self::MESSAGE_REGISTRATION, $message);

        return $this->redirectToRoute('index', [
            '_locale' => $request->getLocale()
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/reset",
     *      name="reset",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     */
    public function resetAction(Request $request)
    {
        $form = $this->createForm(new ResetType, new Reset, ['validation_groups' => 'reset']);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager = $this->getDoctrine()->getManager();

            $_translator = $this->get('translator');

            $reset = $form->getData();

            $reader = $_manager->getRepository('AppBundle:Reader')->findOneBy(['email' => $reset->getReader()->getEmail()]);

            if( !$reader ) {
                $this->get('session')->getFlashBag()->add(self::MESSAGE_RESET, [
                    'error' => $_translator->trans("reset.fail.reader", [], 'responses')
                ]);

                return $this->redirect($request->headers->get('referer'));
            }

            $reader = $this->get('app.security.reader_security_handler')->resetPassword($reader);

            $email = [
                'from'    => [$this->container->getParameter('personal_email')['no_reply'] => $_translator->trans('default.from', [], 'emails')],
                'to'      => $reader->getEmail(),
                'subject' => $_translator->trans("reset.subject", [], 'emails'),
                'body'    => $this->renderView('AppBundle:Email:reset.html.twig', [
                    'readerId'     => $reader->getId(),
                    'resetDigest'  => $reader->getResetDigest()
                ])
            ];

            if( $this->get('app.mailer_shortcut')->sendMail($email['from'], $email['to'], $email['subject'], $email['body']) ) {
                $message = [
                    'notification' => $_translator->trans("reset.success.step_1", [], 'responses')
                ];
            } else {
                $this->get('app.security.reader_security_handler')->cleanupFailedReset($reader->getId());

                $message = [
                    'error' => $_translator->trans("reset.fail.mail_1", [], 'responses')
                ];
            }
        } else {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];
        }

        $this->get('session')->getFlashBag()->add(self::MESSAGE_RESET, $message);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/reset_confirm/{readerId}/{resetDigest}",
     *      name="reset_confirm",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%", "readerId" = "\d+", "resetDigest" = "[a-f0-9]{64}"}
     * )
     */
    public function resetConfirmAction(Request $request, $readerId, $resetDigest)
    {
        $_manager = $this->getDoctrine()->getManager();

        $reader = $_manager->getRepository('AppBundle:Reader')->findOneBy(['id' => $readerId, 'resetDigest' => $resetDigest]);

        if( !$reader )
            throw $this->createNotFoundException();

        $_translator = $this->get('translator');

        if( $reader->getResetDigestDatetime() >= (new DateTime('now')) )
        {
            $newPassword = $this->get('app.security.reader_security_handler')->createNewPassword();

            $reader->setPassword($this->get('security.password_encoder')->encodePassword($reader, $newPassword));

            $email = [
                'from'    => [$this->container->getParameter('personal_email')['no_reply'] => $_translator->trans('default.from', [], 'emails')],
                'to'      => $reader->getEmail(),
                'subject' => $_translator->trans("password.subject", [], 'emails'),
                'body'    => $this->renderView('AppBundle:Email:password.html.twig', [
                    'newPassword' => $newPassword
                ])
            ];

            if( $this->get('app.mailer_shortcut')->sendMail($email['from'], $email['to'], $email['subject'], $email['body']) ) {
                $message = [
                    'notification' => $_translator->trans("reset.success.step_2", [], 'responses')
                ];
            } else {
                $message = [
                    'error' => $_translator->trans("reset.fail.mail_2", [], 'responses')
                ];
            }
        } else {
            $message = [
                'error' => $_translator->trans("reset.fail.expired", [], 'responses')
            ];
        }

        $reader
            ->setResetDigest(NULL)
            ->setResetDigestDatetime(NULL)
        ;

        $_manager->persist($reader);

        $_manager->flush();

        $this->get('session')->getFlashBag()->add(self::MESSAGE_RESET, $message);

        return $this->redirectToRoute('index', [
            '_locale' => $request->getLocale()
        ]);
    }
}
