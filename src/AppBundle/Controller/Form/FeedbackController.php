<?php
// src/AppBundle/Controller/Form/FeedbackController.php
namespace AppBundle\Controller\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\FormErrorHandlerTrait,
    AppBundle\Form\Model\Feedback,
    AppBundle\Form\Type\FeedbackType;

class FeedbackController extends Controller
{
    use FormErrorHandlerTrait;

    const MESSAGE_FEEDBACK = "message_feedback";

    public function formAction()
    {
        $form = $this->createForm(new FeedbackType, new Feedback);

        $message = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_FEEDBACK) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_FEEDBACK)[0]
            : NULL;

        return $this->render('AppBundle:Form:feedback.html.twig', [
            'form'    => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/feedback_send",
     *      name="feedback_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/feedback_send",
     *      name="feedback_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function sendAction(Request $request)
    {
        $form = $this->createForm(new FeedbackType, ($feedback = new Feedback));

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];
        } else {
            $_translator = $this->get('translator');

            $email = [
                'from'    => [$this->container->getParameter('personal_email')['no_reply'] => $_translator->trans('default.from', [], 'emails')],
                'to'      => $this->container->getParameter('personal_email')['feedback'],
                'subject' => ( $feedback->getSubject() ) ?: $_translator->trans("feedback.no_subject", [], 'emails'),
                'body'    => $this->renderView('AppBundle:Email:feedback.html.twig', [
                    'feedback' => $feedback
                ])
            ];

            if( $this->get('app.mailer_shortcut')->sendMail($email['from'], $email['to'], $email['subject'], $email['body']) ) {
                $message = [
                    'notification' => $_translator->trans("feedback.success", [], 'responses')
                ];
            } else {
                $message = [
                    'error' => $_translator->trans("feedback.fail", [], 'responses')
                ];
            }
        }

        $this->get('session')->getFlashBag()->add(self::MESSAGE_FEEDBACK, $message);

        return $this->redirectToRoute('feedback', [
            '_locale' => $request->getLocale()
        ]);
    }
}