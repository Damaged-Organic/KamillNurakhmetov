<?php
// src/AppBundle/Controller/Cart/BookCartController.php
namespace AppBundle\Controller\Cart;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BookCartController extends Controller
{
    public function bookCartWidgetAction()
    {
        $_bookCartManager = $this->get('app.cart.book.manager');

        $bookCartItems = $_bookCartManager->getBookCartItems();

        $totalPrice    = $_bookCartManager->getBookCartItemsTotalPrice($bookCartItems, $this->getUser());
        $totalQuantity = $_bookCartManager->getBookCartTotalQuantity();

        return $this->render('AppBundle:Common:cart.html.twig', [
            'totalPrice'    => $totalPrice,
            'totalQuantity' => $totalQuantity
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/cart/book/add/{id}",
     *      name="cart_book_add",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "id" = "\d+"}
     * )
     * @Route(
     *      "/cart/book/add/{id}",
     *      name="cart_book_add_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "id" = "\d+"}
     * )
     */
    public function addAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $book = $_manager->getRepository('AppBundle:Book')->find($id);

        if( !$book )
            throw $this->createNotFoundException();

        $this->get('app.cart.book.manager')->addBook($book);

        return $this->redirectToRoute('order');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/cart/book/remove/{id}",
     *      name="cart_book_remove",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "id" = "\d+"}
     * )
     * @Route(
     *      "/cart/book/remove/{id}",
     *      name="cart_book_remove_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "id" = "\d+"}
     * )
     */
    public function removeAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $book = $_manager->getRepository('AppBundle:Book')->find($id);

        if( !$book )
            throw $this->createNotFoundException();

        $this->get('app.cart.book.manager')->removeBook($book);

        return $this->redirectToRoute('order');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/cart/book/increase/{id}",
     *      name="cart_book_increase",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "id" = "\d+"}
     * )
     * @Route(
     *      "/cart/book/increase/{id}",
     *      name="cart_book_increase_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "id" = "\d+"}
     * )
     */
    public function increaseAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $book = $_manager->getRepository('AppBundle:Book')->find($id);

        if( !$book )
            return new JsonResponse('Book not found', 404);

        $_bookCartManager = $this->get('app.cart.book.manager');

        $_bookCartManager->increaseBookQuantity($book);

        $bookCartItem = $_bookCartManager->getBookCartItem($book);

        if( !$bookCartItem )
            return new JsonResponse('Book cart item not found', 404);

        $bookCartItems = $_bookCartManager->getBookCartItems();

        $user = $this->getUser();

        $totalPrice    = $_bookCartManager->getBookCartItemsTotalPrice($bookCartItems, $user);
        $totalQuantity = $_bookCartManager->getBookCartTotalQuantity();

        $cartWidget =
            $this->get('translator')->transchoice('common.cart.items', $totalQuantity, ['%count%' => $totalQuantity])
            .
            " | {$totalPrice} {$user->getPreferredCurrency()}"
        ;

        return new JsonResponse([
            'quantity'      => $bookCartItem->getQuantity(),
            'itemPrice'     => $bookCartItem->getItemsPrice($user),
            'totalQuantity' => $totalQuantity,
            'totalPrice'    => $totalPrice,
            'cartWidget'    => $cartWidget
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/cart/book/decrease/{id}",
     *      name="cart_book_decrease",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "id" = "\d+"}
     * )
     * @Route(
     *      "/cart/book/decrease/{id}",
     *      name="cart_book_decrease_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "id" = "\d+"}
     * )
     */
    public function decreaseAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $book = $_manager->getRepository('AppBundle:Book')->find($id);

        if( !$book )
            return new JsonResponse('Book not found', 404);

        $_bookCartManager = $this->get('app.cart.book.manager');

        $_bookCartManager->decreaseBookQuantity($book);

        $bookCartItem = $_bookCartManager->getBookCartItem($book);

        if( !$bookCartItem )
            return new JsonResponse('Book cart item not found', 404);

        $bookCartItems = $_bookCartManager->getBookCartItems();

        $user = $this->getUser();

        $totalPrice    = $_bookCartManager->getBookCartItemsTotalPrice($bookCartItems, $user);
        $totalQuantity = $_bookCartManager->getBookCartTotalQuantity();

        $cartWidget =
            $this->get('translator')->transchoice('common.cart.items', $totalQuantity, ['%count%' => $totalQuantity])
            .
            " | {$totalPrice} {$user->getPreferredCurrency()}"
        ;

        return new JsonResponse([
            'quantity'      => $bookCartItem->getQuantity(),
            'itemPrice'     => $bookCartItem->getItemsPrice($user),
            'totalQuantity' => $totalQuantity,
            'totalPrice'    => $totalPrice,
            'cartWidget'    => $cartWidget
        ]);
    }
}
