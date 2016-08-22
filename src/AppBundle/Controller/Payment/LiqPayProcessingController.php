<?php
// src/AppBundle/Controller/Payment/LiqPayProcessingController.php
namespace AppBundle\Controller\Payment;

use AppBundle\Service\Payment\LiqPayProcessing;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\OrderBookCredentials;

class LiqPayProcessingController extends Controller
{
    const LIQ_PAY_RESPONSE_MESSAGE = "liq_pay_response_message";

    /**
     * @Method({"GET"})
     * @Route(
     *      "/payment/liq_pay_processing/buy_book/{bookId}",
     *      name="payment_liq_pay_processing_buy_book",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "bookId" = "\d+"}
     * )
     * @Route(
     *      "/payment/liq_pay_processing/buy_book/{bookId}",
     *      name="payment_liq_pay_processing_buy_book_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "bookId" = "\d+"}
     * )
     */
    public function buyBookAction(Request $request, $bookId)
    {
        // Dirty hack so user won't be able to hit back button and process
        // order again from payment system checkout interface
        if( $request->headers->get('referer') == NULL ) {
            return $this->redirectToRoute('books', [
                '_locale' => $request->getLocale()
            ]);
        }

        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_liqPayProcessing = $this->get('app.payment.liq_pay_processing');

        $book = $_manager->getRepository('AppBundle:Book')->find($bookId);

        if( !$book )
            throw $this->createNotFoundException();

        $order = $_liqPayProcessing->createBookOrder($this->getUser(), $book);

        $serverUrl = $this->generateUrl('checkout_liq_pay_processing_server_book', [
            '_locale' => $request->getLocale()
        ], TRUE);

        $resultUrl = $this->generateUrl('payment_liq_pay_processing_result', [
                '_locale' => $request->getLocale()
        ], TRUE) . "?order_id=" . $order->getOrderId() . "&type=electronic";

        $itemDescription = $_translator->trans('liq_pay.description.book', [
            '%book_title%' => $order->getItemTitle()
        ]);

        $orderBookParameters = $_liqPayProcessing->getOrderParameters($order, $serverUrl, $resultUrl, $itemDescription);

        $order = $_liqPayProcessing->setLiqPayData(
            $order,
            $itemDescription,
            $_liqPayProcessing->getCnbFormData($orderBookParameters),
            $_liqPayProcessing->getCnbFormSignature($orderBookParameters)
        );

        $_manager->persist($order);
        $_manager->flush();

        return $this->render('AppBundle:Payment:liqPayForm.html.twig', [
            'liqPayAction' => $_liqPayProcessing->getCnbFormAction(),
            'liqPayOrder'  => $order
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/checkout/liq_pay_processing/server/book",
     *      name="checkout_liq_pay_processing_server_book",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/checkout/liq_pay_processing/server/book",
     *      name="checkout_liq_pay_processing_server_book_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function serverUrlBookAction(Request $request)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_liqPayProcessing = $this->get('app.payment.liq_pay_processing');

        if( !$liqPayCredentials = $_liqPayProcessing->getLiqPayCredentials($request) )
            throw $this->createNotFoundException('Liq Pay credentials are not set');

        $liqPayDecodedData = $_liqPayProcessing->getLiqPayDecodedData($liqPayCredentials['data']);

        $order = $_liqPayProcessing->getOrder($liqPayDecodedData->order_id);

        if( !$order )
            throw $this->createNotFoundException('Liq Pay order not found');

        if( !$_liqPayProcessing->checkSignature($liqPayCredentials) )
            throw $this->createNotFoundException('Liq Pay signature mismatch');

        if( !$_liqPayProcessing->checkData($order, $liqPayDecodedData) )
            throw $this->createNotFoundException('Liq Pay data mismatch');

        $reader = $_liqPayProcessing->completeBookOrder($order, $liqPayDecodedData->status);

        $order = $_liqPayProcessing->setOrderStatus($order, $liqPayDecodedData->status);

        $_manager->persist($reader);
        $_manager->persist($order);

        $_manager->flush();

        return new Response(NULL, 200);
    }

    public function buyBooksPaperAction(Request $request, OrderBookCredentials $orderBookCredentials)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_bookCartManager = $this->get('app.cart.book.manager');

        $_liqPayProcessing = $this->get('app.payment.liq_pay_processing');

        $orderBooks = $_bookCartManager->getBookCartItems();

        $itemsTitle = [];

        foreach( $orderBooks as $orderBook )
        {
            $itemsTitle[] = "\"{$orderBook->getBook()->getTitle()}\" ({$orderBook->getQuantity()} экз.)";
        }

        $itemsTitle = implode(', ', $itemsTitle);
        $itemsPrice = $_bookCartManager->getBookCartItemsTotalPrice($orderBooks, $this->getUser());

        $order = $_liqPayProcessing->createBooksPaperOrder(
            $this->getUser(),
            $orderBookCredentials,
            $orderBooks,
            $itemsTitle,
            $itemsPrice
        );

        $serverUrl = $this->generateUrl('checkout_liq_pay_processing_server_books_paper', [
            '_locale' => $request->getLocale()
        ], TRUE);

        $resultUrl = $this->generateUrl('payment_liq_pay_processing_result', [
                '_locale' => $request->getLocale()
        ], TRUE) . "?order_id=" . $order->getOrderId() . "&type=paper";

        $itemDescription = $_translator->trans('liq_pay.description.books_paper', [
            '%books%' => $order->getItemTitle()
        ]);

        $orderBookPaperParameters = $_liqPayProcessing->getOrderParameters($order, $serverUrl, $resultUrl, $itemDescription);

        $order = $_liqPayProcessing->setLiqPayData(
            $order,
            $itemDescription,
            $_liqPayProcessing->getCnbFormData($orderBookPaperParameters),
            $_liqPayProcessing->getCnbFormSignature($orderBookPaperParameters)
        );

        $_manager->persist($order);
        $_manager->flush();

        // Clear cart

        $_bookCartManager->clearBooks();

        return $this->render('AppBundle:Payment:liqPayForm.html.twig', [
            'liqPayAction' => $_liqPayProcessing->getCnbFormAction(),
            'liqPayOrder'  => $order
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/checkout/liq_pay_processing/server/books_paper",
     *      name="checkout_liq_pay_processing_server_books_paper",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/checkout/liq_pay_processing/server/books_paper",
     *      name="checkout_liq_pay_processing_server_books_paper_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function serverUrlBooksPaperAction(Request $request)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_liqPayProcessing = $this->get('app.payment.liq_pay_processing');

        if( !$liqPayCredentials = $_liqPayProcessing->getLiqPayCredentials($request) )
            throw $this->createNotFoundException('Liq Pay credentials are not set');

        $liqPayDecodedData = $_liqPayProcessing->getLiqPayDecodedData($liqPayCredentials['data']);

        $order = $_liqPayProcessing->getOrder($liqPayDecodedData->order_id);

        if( !$order )
            throw $this->createNotFoundException('Liq Pay order not found');

        if( !$_liqPayProcessing->checkSignature($liqPayCredentials) )
            throw $this->createNotFoundException('Liq Pay signature mismatch');

        if( !$_liqPayProcessing->checkData($order, $liqPayDecodedData) )
            throw $this->createNotFoundException('Liq Pay data mismatch');

        $reader = $_liqPayProcessing->completeBooksPaperOrder($order, $liqPayDecodedData->status);

        $order = $_liqPayProcessing->setOrderStatus($order, $liqPayDecodedData->status);

        $_manager->persist($reader);
        $_manager->persist($order);

        $_manager->flush();

        // Send notifications

        $email = [
            'from'    => [$this->container->getParameter('personal_email')['no_reply'] => $_translator->trans('default.from', [], 'emails')],
            'to'      => [$order->getOrderBookCredentials()->getEmail()],
            'subject' => $_translator->trans("order.client.subject", [], 'emails'),
            'body'    => $this->renderView('AppBundle:Email:order_client.html.twig', [
                'order' => $order
            ])
        ];

        $this->get('app.mailer_shortcut')->sendMail($email['from'], $email['to'], $email['subject'], $email['body']);

        $email = [
            'from'    => [$this->container->getParameter('personal_email')['no_reply'] => $_translator->trans('default.from', [], 'emails')],
            'to'      => [$this->container->getParameter('personal_email')['sales']],
            'subject' => $_translator->trans("order.admin.subject", [], 'emails'),
            'body'    => $this->renderView('AppBundle:Email:order_admin.html.twig', [
                'order' => $order
            ])
        ];

        $this->get('app.mailer_shortcut')->sendMail($email['from'], $email['to'], $email['subject'], $email['body']);

        return new Response(NULL, 200);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/payment/liq_pay_processing/buy_subscription",
     *      name="payment_liq_pay_processing_buy_subscription",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/payment/liq_pay_processing/buy_subscription",
     *      name="payment_liq_pay_processing_buy_subscription_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function buySubscriptionAction(Request $request)
    {
        // Dirty hack so user won't be able to hit back button and process
        // order again from payment system checkout interface
        if( $request->headers->get('referer') == NULL ) {
            return $this->redirectToRoute('private_office', [
                '_locale' => $request->getLocale()
            ]);
        }

        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_liqPayProcessing = $this->get('app.payment.liq_pay_processing');

        if( !$request->request->has('subscription') )
            throw $this->createNotFoundException();

        $subscriptionId = $request->request->get('subscription');

        $subscription = $_manager->getRepository('AppBundle:Subscription')->find($subscriptionId);

        if( !$subscription )
            throw $this->createNotFoundException();

        $order = $_liqPayProcessing->createSubscriptionOrder($this->getUser(), $subscription);

        $serverUrl = $this->generateUrl('checkout_liq_pay_processing_server_subscription', [
            '_locale' => $request->getLocale()
        ], TRUE);

        $resultUrl = $this->generateUrl('payment_liq_pay_processing_result', [
            '_locale' => $request->getLocale()
        ], TRUE) . "?order_id=" . $order->getOrderId() . "&type=electronic";

        $itemDescription = $_translator->trans('liq_pay.description.subscription', [
            '%subscription_title%' => $order->getItemTitle()
        ]);

        $orderSubscriptionParameters = $_liqPayProcessing->getOrderParameters($order, $serverUrl, $resultUrl, $itemDescription);

        $order = $_liqPayProcessing->setLiqPayData(
            $order,
            $itemDescription,
            $_liqPayProcessing->getCnbFormData($orderSubscriptionParameters),
            $_liqPayProcessing->getCnbFormSignature($orderSubscriptionParameters)
        );

        $_manager->persist($order);
        $_manager->flush();

        return $this->render('AppBundle:Payment:liqPayForm.html.twig', [
            'liqPayAction' => $_liqPayProcessing->getCnbFormAction(),
            'liqPayOrder'  => $order
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/checkout/liq_pay_processing/server/subscription",
     *      name="checkout_liq_pay_processing_server_subscription",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/checkout/liq_pay_processing/server/subscription",
     *      name="checkout_liq_pay_processing_server_subscription_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function serverUrlSubscriptionAction(Request $request)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_liqPayProcessing = $this->get('app.payment.liq_pay_processing');

        if( !$liqPayCredentials = $_liqPayProcessing->getLiqPayCredentials($request) )
            throw $this->createNotFoundException('Liq Pay credentials are not set');

        $liqPayDecodedData = $_liqPayProcessing->getLiqPayDecodedData($liqPayCredentials['data']);

        $order = $_liqPayProcessing->getOrder($liqPayDecodedData->order_id);

        if( !$order )
            throw $this->createNotFoundException('Liq Pay order not found');

        if( !$_liqPayProcessing->checkSignature($liqPayCredentials) )
            throw $this->createNotFoundException('Liq Pay signature mismatch');

        if( !$_liqPayProcessing->checkData($order, $liqPayDecodedData) )
            throw $this->createNotFoundException('Liq Pay data mismatch');

        $reader = $_liqPayProcessing->completeSubscriptionOrder($order, $liqPayDecodedData->status);

        $order = $_liqPayProcessing->setOrderStatus($order, $liqPayDecodedData->status);

        $_manager->persist($reader);
        $_manager->persist($order);

        $_manager->flush();

        return new Response(NULL, 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/payment/liq_pay_processing/result",
     *      name="payment_liq_pay_processing_result",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/payment/liq_pay_processing/result",
     *      name="payment_liq_pay_processing_result_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function resultUrlAction(Request $request)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_session = $this->get('session');

        $_translator = $this->get('translator');

        if( !$request->query->has("order_id") )
            throw $this->createAccessDeniedException();

        $order = $_manager->getRepository('AppBundle:Order')->findOneBy([
            'orderId' => $request->query->get("order_id")]
        );

        if( $request->query->get("type") === 'paper' ) {
            $messageId = $order->getOrderStatus() . '.paper';
        } else {
            $messageId = $order->getOrderStatus() . '.electronic';
        }

        $message = $_translator->trans($messageId, [], 'liq_pay_responses');

        $_session->getFlashBag()->add(self::LIQ_PAY_RESPONSE_MESSAGE, $message);

        return $this->redirectToRoute('private_office', [
            '_locale' => $request->getLocale()
        ]);
    }
}
