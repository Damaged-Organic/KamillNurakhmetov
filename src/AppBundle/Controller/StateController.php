<?php
// src/AppBundle/Controller/StateController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Controller\Contract\PageInitInterface,
    AppBundle\Controller\Contract\PageCleanupInterface,
    AppBundle\Controller\Payment\LiqPayProcessingController,
    AppBundle\Entity\Story,
    AppBundle\Entity\StoryCategory;

class StateController extends Controller implements PageInitInterface, PageCleanupInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="index",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/",
     *      name="index_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function indexAction(Request $request)
    {
        $_metadata = $this->get('app.metadata');

        return $this->render('AppBundle:State:index.html.twig', [
            'route'    => $_metadata->getCurrentRoute(),
            'metadata' => $_metadata->getCurrentMetadata()
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/author",
     *      name="author",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/author",
     *      name="author_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function authorAction(Request $request)
    {
        $_metadata = $this->get('app.metadata');

        $_manager = $this->getDoctrine()->getManager();

        $author = $_manager->getRepository('AppBundle:Author')->findOneBy([]);

        return $this->render('AppBundle:State:author.html.twig', [
            'metadata' => $_metadata->getCurrentMetadata(),
            'author'   => $author
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/stories/{page}",
     *      name="stories",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%", "page" = 1},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "page" = "\d+"}
     * )
     * @Route(
     *      "/stories/{page}",
     *      name="stories_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%", "page" = 1},
     *      requirements={"domain" = "%domain%", "page" = "\d+"}
     * )
     */
    public function storiesAction(Request $request, $page)
    {
        if( $request->query->has('sorting_parameter') ) {
            $sortingParameter = $request->query->get('sorting_parameter');

            if( !in_array($sortingParameter, Story::getSortingParameters(), TRUE) )
                throw $this->createNotFoundException();
        } else {
            $sortingParameter = NULL;
        }

        if( $request->query->has('filter_parameter') ) {
            $filterParameter = $request->query->get('filter_parameter');

            if( !in_array($filterParameter, StoryCategory::getFilterParameters(), TRUE) )
                throw $this->createNotFoundException();
        } else {
            $filterParameter = NULL;
        }

        $_manager = $this->getDoctrine()->getManager();

        $storiesCategories = $_manager->getRepository('AppBundle:StoryCategory')->findAll();

        if( !$storiesCategories )
            throw $this->createNotFoundException();

        $_metadata = $this->get('app.metadata');

        $results_per_page = 6;
        $pages_step       = 10;

        $stories        = $_manager->getRepository('AppBundle:Story')->findAllByPageSortedFiltered($page, $results_per_page, $sortingParameter, $filterParameter);
        $storiesReviews = $_manager->getRepository('AppBundle:Story')->findAllIndexedById();

        $transform = function($inputArray)
        {
            $outputArray = [];

            foreach($inputArray as $key => $value)
            {
                if( count($value->getReviews()) )
                {
                    foreach( $value->getReviews() as $review )
                    {
                        $outputArray[$value->getId()][] = $review;
                    }
                } else {
                    $outputArray[$value->getId()] = NULL;
                }
            }

            return $outputArray;
        };

        $storiesReviews = $transform($storiesReviews);

        $paginationBarSet = $this->get('app.pagination_bar')->setParameters(
            count($stories), $results_per_page, $page, $pages_step
        );

        if( $paginationBarSet ) {
            $this->get('app.pagination_bar')->setPaginationBar();
        } else {
            throw $this->createNotFoundException();
        }

        $metadata = $_metadata->getCurrentMetadata();

        if( $page > 1 || $sortingParameter || $filterParameter )
            $metadata->setRobots('noindex, nofollow');

        // KLUDGE: set sorting and filter parameters
        $_session = $this->get('session');

        $_session->set('page', $page);
        $_session->set('sorting_parameter', $sortingParameter);
        $_session->set('filter_parameter', $filterParameter);

        return $this->render('AppBundle:State:stories.html.twig', [
            'metadata'          => $metadata,
            'stories'           => $stories,
            'storiesReviews'    => $storiesReviews,
            'sortingParameter'  => $sortingParameter,
            'filterParameter'   => $filterParameter,
            'storiesCategories' => $storiesCategories
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/books/{id}/{slug}",
     *      name="books",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%", "id" = null, "slug" = null},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "id" = "\d+", "slug" = "[a-z0-9_]+"}
     * )
     * @Route(
     *      "/books/{id}/{slug}",
     *      name="books_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%", "id" = null, "slug" = null},
     *      requirements={"domain" = "%domain%", "id" = "\d+", "slug" = "[a-z0-9_]+"}
     * )
     */
    public function booksAction(Request $request, $id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_metadata = $this->get('app.metadata');

        if( $id ) {
            $book = $_manager->getRepository('AppBundle:Book')->findWithActiveReviewsOnly($id);

            if( !$book )
                throw $this->createNotFoundException();

            $response = [
                'data' => [
                    'metadata' => $_metadata->getCurrentMetadata(),
                    'book'     => $book
                ],
                'view' => 'AppBundle:State:book.html.twig'
            ];
        } else {
            $books = $_manager->getRepository('AppBundle:Book')->findAll();

            $response = [
                'data' => [
                    'metadata' => $_metadata->getCurrentMetadata(),
                    'books'    => $books
                ],
                'view' => 'AppBundle:State:books.html.twig'
            ];
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/private_office",
     *      name="private_office",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/private_office",
     *      name="private_office_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function privateOfficeAction(Request $request)
    {
        $_metadata = $this->get('app.metadata');

        $books = $this->getUser()->getBooks();

        $liqPayResponseMessage = ( $this->get('session')->getFlashBag()->has(LiqPayProcessingController::LIQ_PAY_RESPONSE_MESSAGE) )
            ? $this->get('session')->getFlashBag()->get(LiqPayProcessingController::LIQ_PAY_RESPONSE_MESSAGE)[0]
            : NULL;

        return $this->render('AppBundle:State:privateOffice.html.twig', [
            'metadata'              => $_metadata->getCurrentMetadata(),
            'books'                 => $books,
            'liqPayResponseMessage' => $liqPayResponseMessage
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/order",
     *      name="order",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/order",
     *      name="order_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function orderAction(Request $request)
    {
        $_metadata = $this->get('app.metadata');

        $_bookCartManager = $this->get('app.cart.book.manager');

        $orderBooks = $_bookCartManager->getBookCartItems();

        $totalPrice = $_bookCartManager->getBookCartItemsTotalPrice($orderBooks, $this->getUser());

        return $this->render('AppBundle:State:order.html.twig', [
            'metadata'   => $_metadata->getCurrentMetadata(),
            'orderBooks' => $orderBooks,
            'totalPrice' => $totalPrice
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/feedback",
     *      name="feedback",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/feedback",
     *      name="feedback_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function feedbackAction(Request $request)
    {
        $_metadata = $this->get('app.metadata');

        return $this->render('AppBundle:State:feedback.html.twig', [
            'metadata' => $_metadata->getCurrentMetadata()
        ]);
    }
}
