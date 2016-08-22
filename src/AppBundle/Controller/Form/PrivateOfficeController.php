<?php
// src/AppBundle/Controller/Form/PrivateOfficeController.php
namespace AppBundle\Controller\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\FormErrorHandlerTrait,
    AppBundle\Form\Model\PrivateOffice\ReaderPersonal,
    AppBundle\Form\Model\PrivateOffice\ReaderPassword,
    AppBundle\Form\Model\PrivateOffice\ReaderCurrency,
    AppBundle\Form\Type\PrivateOffice\ReaderPersonalType,
    AppBundle\Form\Type\PrivateOffice\ReaderPasswordType,
    AppBundle\Form\Type\PrivateOffice\ReaderCurrencyType;

class PrivateOfficeController extends Controller
{
    use FormErrorHandlerTrait;

    const MESSAGE_READER_PERSONAL = "message_reader_personal";
    const MESSAGE_READER_PASSWORD = "message_reader_password";
    const MESSAGE_READER_CURRENCY = "message_reader_currency";

    public function formReaderPersonalAction()
    {
        $form = $this->createForm(new ReaderPersonalType, new ReaderPersonal);

        $message = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_READER_PERSONAL) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_READER_PERSONAL)[0]
            : NULL;

        return $this->render('AppBundle:Form/PrivateOffice:readerPersonal.html.twig', [
            'form'    => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/reader_personal_send",
     *      name="reader_personal_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/reader_personal_send",
     *      name="reader_personal_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function sendReaderPersonalAction(Request $request)
    {
        $form = $this->createForm(new ReaderPersonalType, ($readerPersonal = new ReaderPersonal));

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_translator = $this->get('translator');

            $this->getUser()->setEmail($readerPersonal->getEmail());

            if( $readerPersonal->getPseudonym() !== FALSE )
                $this->getUser()->setPseudonym($readerPersonal->getPseudonym());

            $_manager->persist($this->getUser());

            try {
                $caught = FALSE;

                $_manager->flush();
            } catch(\Exception $ex) {
                $caught = TRUE;
            }

            if( $caught ) {
                $message = [
                    'notification' => $_translator->trans("private_office.reader_personal.fail", [], 'responses')
                ];
            } else {
                $message = [
                    'notification' => $_translator->trans("private_office.reader_personal.success", [], 'responses')
                ];
            }
        }

        $this->get('session')->getFlashBag()->add(self::MESSAGE_READER_PERSONAL, $message);

        return $this->redirectToRoute('private_office', [
            '_locale' => $request->getLocale()
        ]);
    }

    public function formReaderPasswordAction()
    {
        $form = $this->createForm(new ReaderPasswordType, new ReaderPassword);

        $message = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_READER_PASSWORD) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_READER_PASSWORD)[0]
            : NULL;

        return $this->render('AppBundle:Form/PrivateOffice:readerPassword.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/reader_password_send",
     *      name="reader_password_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/reader_password_send",
     *      name="reader_password_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function sendReaderPasswordAction(Request $request)
    {
        $form = $this->createForm(new ReaderPasswordType, ($readerPassword = new ReaderPassword));

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_passwordEncoder = $this->get('security.password_encoder');

            $_translator = $this->get('translator');

            if( !$_passwordEncoder->isPasswordValid($this->getUser(), $readerPassword->getOldPassword()) ) {
                $message = [
                    'notification' => $_translator->trans("private_office.reader_password.fail.valid", [], 'responses')
                ];
            } else {
                $this->getUser()->setPassword(
                    $_passwordEncoder->encodePassword($this->getUser(),
                        $readerPassword->getNewPassword())
                );

                $_manager->persist($this->getUser());
                $_manager->flush();

                $message = [
                    'notification' => $_translator->trans("private_office.reader_password.success", [], 'responses')
                ];
            }
        }

        $this->get('session')->getFlashBag()->add(self::MESSAGE_READER_PASSWORD, $message);

        return $this->redirectToRoute('private_office', [
            '_locale' => $request->getLocale()
        ]);
    }

    public function formReaderCurrencyAction()
    {
        $preferredCurrency = $this->getUser()->getPreferredCurrency();

        $form = $this->createForm(new ReaderCurrencyType($preferredCurrency), new ReaderCurrency);

        $message = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_READER_CURRENCY) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_READER_CURRENCY)[0]
            : NULL;

        return $this->render('AppBundle:Form/PrivateOffice:readerCurrency.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/reader_currency_send",
     *      name="reader_currency_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/reader_currency_send",
     *      name="reader_currency_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function sendReaderCurrencyAction(Request $request)
    {
        $form = $this->createForm(new ReaderCurrencyType, ($readerCurrency = new ReaderCurrency));

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_translator = $this->get('translator');

            $this->getUser()->setPreferredCurrency($readerCurrency->getPreferredCurrency());

            $_manager->persist($this->getUser());
            $_manager->flush();

            $message = [
                'notification' => $_translator->trans("private_office.reader_currency.success", [], 'responses')
            ];
        }

        $this->get('session')->getFlashBag()->add(self::MESSAGE_READER_CURRENCY, $message);

        return $this->redirectToRoute('private_office', [
            '_locale' => $request->getLocale()
        ]);
    }

    public function formSubscriptionAction()
    {
        $_manager = $this->getDoctrine()->getManager();

        $subscriptions = $_manager->getRepository('AppBundle:Subscription')->findAll();

        return $this->render('AppBundle:Form/PrivateOffice:subscription.html.twig', [
            'subscriptions' => $subscriptions
        ]);
    }
}
