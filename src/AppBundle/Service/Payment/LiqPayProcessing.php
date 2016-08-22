<?php
// src/AppBundle/Service/Payment/LiqPayProcessing.php
namespace AppBundle\Service\Payment;

use DateTime;

use LiqPay;

use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Order,
    AppBundle\Entity\OrderBook,
    AppBundle\Entity\OrderBookCredentials,
    AppBundle\Entity\Reader,
    AppBundle\Entity\Book,
    AppBundle\Entity\Subscription;

class LiqPayProcessing
{
    const LIQ_PAY_VERSION = 3;
    const LIQ_PAY_SANDBOX = 0;

    const LIQ_PAY_ORDER_STATUS_PROCESSING = "processing";
    const LIQ_PAY_ORDER_STATUS_SUCCESS    = "success";
    const LIQ_PAY_ORDER_STATUS_FAILURE    = "failure";

    private $_manager;

    private $liqPayKeys;

    public function __construct(EntityManager $manager, LiqPay $liqPay)
    {
        $this->_manager = $manager;
        $this->_liqPay  = $liqPay;
    }

    public function setLiqPayKeys($liqPayKeyPublic, $liqPayKeyPrivate)
    {
        $this->liqPayKeys = [
            'public'  => $liqPayKeyPublic,
            'private' => $liqPayKeyPrivate
        ];
    }

    public function getPublicKey()
    {
        return $this->liqPayKeys['public'];
    }

    public function getPrivateKey()
    {
        return $this->liqPayKeys['private'];
    }

    public function getCnbFormAction()
    {
        return $this->_liqPay->getCheckoutUrl();
    }

    public function getCnbFormData(array $parameters)
    {
        $parameters = $this->_liqPay->cnb_params($parameters);

        return base64_encode(json_encode($parameters));
    }

    public function getCnbFormSignature(array $parameters)
    {
        $parameters = $this->_liqPay->cnb_params($parameters);

        return $this->_liqPay->cnb_signature($parameters);
    }

    // Order

    private function generateOrderId(Order $order)
    {
        return strtoupper(sha1($order->getId() . uniqid('', TRUE)));
    }

    public function setLiqPayData(Order $order, $itemDescription, $data, $signature)
    {
        $order
            ->setItemDescription($itemDescription)
            ->setOrderData($data)
            ->setOrderSignature($signature)
        ;

        return $order;
    }

    public function getOrder($orderId)
    {
        $order = $this->_manager->getRepository('AppBundle:Order')->findPendingOrder($orderId);

        return $order;
    }

    public function getOrderParameters($order, $serverUrl, $resultUrl, $itemDescription)
    {
        $orderParameters = [
            'version'     => self::LIQ_PAY_VERSION,
            'sandbox'     => self::LIQ_PAY_SANDBOX,
            'order_id'    => $order->getOrderId(),
            'type'        => 'buy',
            'pay_way'     => 'card,liqpay,privat24',
            'currency'    => $order->getItemCurrency(),
            'amount'      => $order->getItemPrice(),
            'description' => $itemDescription,
            'server_url'  => $serverUrl,
            'result_url'  => $resultUrl,
            'language'    => 'ru'
        ];

        return $orderParameters;
    }

    public function setOrderStatus(Order $order, $status)
    {
        $order->setOrderStatus($status);

        return $order;
    }

    // Checkout

    public function getLiqPayCredentials(Request $request)
    {
        if( !$request->request->has('data') || !$request->request->has('signature') )
            return FALSE;

        return [
            'data'      => $request->request->get('data'),
            'signature' => $request->request->get('signature')
        ];
    }

    public function getLiqPayDecodedData($data)
    {
        return json_decode(base64_decode($data));
    }

    public function checkSignature($liqPayCredentials)
    {
        $orderSignature = $this->getPrivateKey() . $liqPayCredentials['data'] . $this->getPrivateKey();

        $orderSignature = base64_encode(sha1(
            $orderSignature, 1
        ));

        return ( $orderSignature === $liqPayCredentials['signature'] );
    }

    public function checkData($order, $liqPayDecodedData)
    {
        if( $order->getItemCurrency() != $liqPayDecodedData->currency )
            return FALSE;

        $liqPayDecodedDataAmount = number_format(floatval($liqPayDecodedData->amount), 2);

        if( $order->getItemPrice() != $liqPayDecodedDataAmount )
            return FALSE;

        return TRUE;
    }

    // Books

    public function createBookOrder(Reader $reader, Book $book)
    {
        $order = (new Order)
            ->setReader($reader)
            ->setBook($book)
            ->setItemTitle($book->getTitle())
            ->setItemCurrency($reader->getPreferredCurrency())
            ->setItemPrice($book->getPrice($reader->getPreferredCurrency()))
        ;

        $this->_manager->persist($order);
        $this->_manager->flush();

        $order
            ->setOrderId($this->generateOrderId($order))
            ->setOrderDateTime(new DateTime)
            ->setOrderStatus(self::LIQ_PAY_ORDER_STATUS_PROCESSING)
        ;

        return $order;
    }

    public function completeBookOrder(Order $order, $status)
    {
        $reader = $order->getReader();

        if( $status !== self::LIQ_PAY_ORDER_STATUS_SUCCESS )
            return $reader;

        $reader->addBook(
            $order->getBook()
        );

        return $reader;
    }

    // Books paper

    public function createBooksPaperOrder(Reader $reader, OrderBookCredentials $orderBookCredentials, array $orderBooks, $itemsTitle, $itemsPrice)
    {
        $order = (new Order)
            ->setReader($reader)
            ->setOrderBookCredentials($orderBookCredentials)
        ;

        foreach( $orderBooks as $orderBook )
        {
            if( $orderBook instanceof OrderBook )
                $order->addOrderBook($orderBook);
        }

        $order
            ->setItemTitle($itemsTitle)
            ->setItemCurrency($reader->getPreferredCurrency())
            ->setItemPrice($itemsPrice)
        ;

        $this->_manager->persist($order);
        $this->_manager->flush();

        $order
            ->setOrderId($this->generateOrderId($order))
            ->setOrderDateTime(new DateTime)
            ->setOrderStatus(self::LIQ_PAY_ORDER_STATUS_PROCESSING)
        ;

        return $order;
    }

    public function completeBooksPaperOrder(Order $order, $status)
    {
        $reader = $order->getReader();

        return $reader;
    }

    // Stories

    public function createSubscriptionOrder(Reader $reader, Subscription $subscription)
    {
        $order = (new Order)
            ->setReader($reader)
            ->setSubscription($subscription)
            ->setItemTitle($subscription->getName())
            ->setItemCurrency($reader->getPreferredCurrency())
            ->setItemPrice($subscription->getPrice($reader->getPreferredCurrency()))
        ;

        $this->_manager->persist($order);
        $this->_manager->flush();

        $order
            ->setOrderId($this->generateOrderId($order))
            ->setOrderDateTime(new DateTime)
            ->setOrderStatus(self::LIQ_PAY_ORDER_STATUS_PROCESSING)
        ;

        return $order;
    }

    public function completeSubscriptionOrder(Order $order, $status)
    {
        $reader = $order->getReader();

        if( $status !== self::LIQ_PAY_ORDER_STATUS_SUCCESS )
            return $reader;

        $reader
            ->setIsSubscribed(TRUE)
            ->setSubscriptionEnd(
                (new DateTime('now'))
                    ->modify('+' . $order->getSubscription()->getDurationNumber() . ' ' . $order->getSubscription()->getDurationMeasure())
            )
        ;

        return $reader;
    }

    public function resetExpiredSubscriptions()
    {
        $readerWithExpiredSubscriptions = $this->_manager->getRepository('AppBundle:Reader')->findExpiredSubscriptions();

        foreach($readerWithExpiredSubscriptions as $readerWithExpiredSubscription)
        {
            $readerWithExpiredSubscription
                ->setIsSubscribed(FALSE)
                ->setSubscriptionEnd(NULL)
            ;

            $this->_manager->persist($readerWithExpiredSubscription);
        }

        $this->_manager->flush();
    }
}
