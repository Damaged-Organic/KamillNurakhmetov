<?php
// src/AppBundle/Service/Cart/BookCartManager.php
namespace AppBundle\Service\Cart;

use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Reader,
    AppBundle\Entity\Book,
    AppBundle\Entity\OrderBook;

class BookCartManager
{
    const CART_BOOK_KEY = 'cart_book_key';

    const CART_BOOK_ITEM_ID       = 'id';
    const CART_BOOK_ITEM_QUANTITY = 'quantity';

    const MIN_QUANTITY = 1;
    const MAX_QUANTITY = 100;

    private $_session;
    private $_manager;

    public function setSession(Session $session)
    {
        $this->_session = $session;
    }

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function getCart()
    {
        $cart = $this->_session->get(self::CART_BOOK_KEY);

        return $cart ?: [];
    }

    private function setCart($cart)
    {
        $this->_session->set(self::CART_BOOK_KEY, $cart);
    }

    private function unsetCart()
    {
        $this->_session->remove(self::CART_BOOK_KEY);
    }

    private function getItem(Book $book, $cart)
    {
        foreach( $cart as $key => $item )
        {
            if( $item[self::CART_BOOK_ITEM_ID] === $book->getId() )
                return $key;
        }

        return FALSE;
    }

    private function setItem(Book $book, $cart)
    {
        $item = [
            self::CART_BOOK_ITEM_ID       => $book->getId(),
            self::CART_BOOK_ITEM_QUANTITY => 1
        ];

        array_push($cart, $item);

        return $cart;
    }

    private function unsetItem($key, $cart)
    {
        if( $cart[$key] )
            unset($cart[$key]);

        return $cart;
    }

    private function increaseItem($key, $cart)
    {
        if( $cart[$key] ) {
            if( $cart[$key][self::CART_BOOK_ITEM_QUANTITY] < self::MAX_QUANTITY )
                $cart[$key][self::CART_BOOK_ITEM_QUANTITY]++;
        }

        return $cart;
    }

    private function decreaseItem($key, $cart)
    {
        if( $cart[$key] ) {
            if( $cart[$key][self::CART_BOOK_ITEM_QUANTITY] > self::MIN_QUANTITY )
                $cart[$key][self::CART_BOOK_ITEM_QUANTITY]--;
        }

        return $cart;
    }

    public function addBook(Book $book)
    {
        $cart = $this->getCart();

        // Check if paper version available before adding to cart
        if( $book->getHasPaper() )
        {
            $key = $this->getItem($book, $cart);

            if( $key !== FALSE ) {
                $cart = $this->increaseItem($key, $cart);
            } else {
                $cart = $this->setItem($book, $cart);
            }

            $this->setCart($cart);
        }
    }

    public function removeBook(Book $book)
    {
        $cart = $this->getCart();

        $key = $this->getItem($book, $cart);

        if( $key !== FALSE )
            $cart = $this->unsetItem($key, $cart);

        $this->setCart($cart);
    }

    public function increaseBookQuantity(Book $book)
    {
        $cart = $this->getCart();

        $key = $this->getItem($book, $cart);

        if( $key !== FALSE )
            $cart = $this->increaseItem($key, $cart);

        $this->setCart($cart);
    }

    public function decreaseBookQuantity(Book $book)
    {
        $cart = $this->getCart();

        $key = $this->getItem($book, $cart);

        if( $key !== FALSE )
            $cart = $this->decreaseItem($key, $cart);

        $this->setCart($cart);
    }

    public function clearBooks()
    {
        $this->unsetCart();
    }

    public function getBookCartItem(Book $book)
    {
        $cart = $this->getCart();

        $key = $this->getItem($book, $cart);

        if( $key === FALSE )
            return FALSE;

        $bookCartItem = (new OrderBook)
            ->setBook($book)
            ->setQuantity($cart[$key][self::CART_BOOK_ITEM_QUANTITY])
        ;

        return $bookCartItem;
    }

    public function getBookCartItems()
    {
        $bookCartItems = [];

        $cart = $this->getCart();

        if( $cart )
        {
            foreach( $cart as $item )
            {
                $book = $this->_manager->getRepository('AppBundle:Book')->find($item[self::CART_BOOK_ITEM_ID]);

                if( $book )
                {
                    $bookCartItem = (new OrderBook)
                        ->setBook($book)
                        ->setQuantity($item[self::CART_BOOK_ITEM_QUANTITY])
                    ;

                    $bookCartItems[] = $bookCartItem;
                }
            }
        }

        return $bookCartItems;
    }

    public function getBookCartItemsTotalPrice(array $bookCartItems, Reader $user)
    {
        $totalPrice = 0;

        foreach( $bookCartItems as $bookCartItem )
        {
            if( !($bookCartItem instanceof OrderBook) )
                continue;

            if( !$bookCartItem->getBook() )
                continue;

            $price = $bookCartItem->getItemsPrice($user);

            $totalPrice = bcadd($totalPrice, $price, 2);
        }

        return $totalPrice;
    }

    public function getBookCartTotalQuantity()
    {
        $totalQuantity = 0;

        $cart = $this->getCart();

        foreach( $cart as $item )
        {
            $totalQuantity += $item[self::CART_BOOK_ITEM_QUANTITY];
        }

        return $totalQuantity;
    }
}
