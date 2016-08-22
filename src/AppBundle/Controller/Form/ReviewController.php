<?php
// src/AppBundle/Controller/Form/ReviewController.php
namespace AppBundle\Controller\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\FormErrorHandlerTrait,
    AppBundle\Form\Model\ReviewBook,
    AppBundle\Form\Model\ReviewStory,
    AppBundle\Form\Type\ReviewBookType,
    AppBundle\Form\Type\ReviewStoryType;

class ReviewController extends Controller
{
    use FormErrorHandlerTrait;

    public function formReviewBookAction($bookId)
    {
        $form = $this->createForm(new ReviewBookType, new ReviewBook);

        return $this->render('AppBundle:Form:reviewBook.html.twig', [
            'form'   => $form->createView(),
            'bookId' => $bookId
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/review_send/book",
     *      name="review_book_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%", "entity" = "book|story"}
     * )
     * @Route(
     *      "/review_send/book",
     *      name="review_book_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "entity" = "book|story"}
     * )
     */
    public function sendReviewBookAction(Request $request)
    {
        $form = $this->createForm(new ReviewBookType, new ReviewBook);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'data' => $this->stringifyFormErrors($form),
                'code' => 500
            ];
        } else {
            $_translator = $this->get('translator');

            if( !$form->get('bookId') ) {
                $message = [
                    'data' => $_translator->trans("review.fail", [], 'responses'),
                    'code' => 500
                ];
            } else {
                $_manager = $this->getDoctrine()->getManager();

                $_authorizationChecker = $this->get('security.authorization_checker');

                $book = $_manager->getRepository('AppBundle:Book')->find($form->get('bookId')->getData());

                if( !$book || !$_authorizationChecker->isGranted('ROLE_READER') ) {
                    $message = [
                        'data' => $_translator->trans("review.denied.book", [], 'responses'),
                        'code' => 500
                    ];
                } else {
                    $review = $form->getData()->getReview();

                    if( $this->getUser()->getPseudonym() )
                    {
                        $review
                            ->setBook($book)
                            ->setAuthorCredentials($this->getUser()->getPseudonym())
                            ->setReader($this->getUser());

                        $_manager->persist($review);
                        $_manager->flush();

                        $message = [
                            'data' => json_encode(['message' => $_translator->trans("review.success", [], 'responses')]),
                            'code' => 200
                        ];
                    } else {
                        $message = [
                            'data' => $_translator->trans("review.denied.pseudonym", [], 'responses'),
                            'code' => 500
                        ];
                    }
                }
            }
        }

        return new Response($message['data'], $message['code']);
    }

    public function formReviewStoryAction()
    {
        $form = $this->createForm(new ReviewStoryType, new ReviewStory);

        return $this->render('AppBundle:Form:reviewStory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/review_send/story",
     *      name="review_story_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%", "entity" = "book|story"}
     * )
     * @Route(
     *      "/review_send/story",
     *      name="review_story_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "entity" = "book|story"}
     * )
     */
    public function sendReviewStoryAction(Request $request)
    {
        $form = $this->createForm(new ReviewStoryType, new ReviewStory);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'data' => $this->stringifyFormErrors($form),
                'code' => 500
            ];
        } else {
            $_translator = $this->get('translator');

            if( !$form->get('storyId') ) {
                $message = [
                    'data' => $_translator->trans("review.fail", [], 'responses'),
                    'code' => 500
                ];
            } else {
                $_manager = $this->getDoctrine()->getManager();

                $_authorizationChecker = $this->get('security.authorization_checker');

                $story = $_manager->getRepository('AppBundle:Story')->find($form->get('storyId')->getData());

                if( !$_authorizationChecker->isGranted('STORY_READER_SUBSCRIBED', $story) ) {
                    $message = [
                        'data' => $_translator->trans("review.denied.story", [], 'responses'),
                        'code' => 500
                    ];
                } else {
                    $review = $form->getData()->getReview();

                    if( $this->getUser()->getPseudonym() ) {
                        $review
                            ->setStory($story)
                            ->setAuthorCredentials($this->getUser()->getPseudonym())
                            ->setReader($this->getUser());

                        $_manager->persist($review);
                        $_manager->flush();

                        $message = [
                            'data' => json_encode(['message' => $_translator->trans("review.success", [], 'responses')]),
                            'code' => 200
                        ];
                    } else {
                        $message = [
                            'data' => $_translator->trans("review.denied.pseudonym", [], 'responses'),
                            'code' => 500
                        ];
                    }
                }
            }
        }

        return new Response($message['data'], $message['code']);
    }
}
