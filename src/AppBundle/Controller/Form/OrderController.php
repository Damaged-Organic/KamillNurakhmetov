<?php
// src/AppBundle/Controller/Form/OrderController.php
namespace AppBundle\Controller\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\FormErrorHandlerTrait,
    AppBundle\Entity\OrderBookCredentials,
    AppBundle\Form\Type\Order\OrderBookCredentialsType;

class OrderController extends Controller
{
    use FormErrorHandlerTrait;

    const MESSAGE_ORDER = 'message_order';

    public function formOrderAction()
    {
        $_bookCartManager = $this->get('app.cart.book.manager');

        $bookCartItems = $_bookCartManager->getBookCartItems();
        $totalPrice    = $_bookCartManager->getBookCartItemsTotalPrice($bookCartItems, $this->getUser());

        $form = $this->createForm(new OrderBookCredentialsType, new OrderBookCredentials);

        $message = ( $this->get('session')->getFlashBag()->has(self::MESSAGE_ORDER) )
            ? $this->get('session')->getFlashBag()->get(self::MESSAGE_ORDER)[0]
            : NULL;

        return $this->render('AppBundle:Form/Order:order.html.twig', [
            'form'       => $form->createView(),
            'message'    => $message,
            'totalPrice' => $totalPrice
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/order_send",
     *      name="order_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/order_send",
     *      name="order_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function sendOrderAction(Request $request)
    {
        $form = $this->createForm(new OrderBookCredentialsType, ($orderBookCredentials = new OrderBookCredentials));

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $message = [
                'error' => $this->stringifyFormErrors($form)
            ];

            $this->get('session')->getFlashBag()->add(self::MESSAGE_ORDER, $message);

            $response = $this->redirectToRoute('order');
        } else {
            $response = $this->forward('AppBundle:Payment/LiqPayProcessing:buyBooksPaper', [
                'orderBookCredentials' => $orderBookCredentials
            ]);
        }

        return $response;
    }
}
