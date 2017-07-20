<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Order.php';
require_once __DIR__ . '/../src/OrdersGateway.php';
require_once __DIR__ . '/../src/CarriersGateway.php';
require_once __DIR__ . '/../src/OrderProductsGateway.php';
require_once __DIR__ . '/../src/OrderStatusesGateway.php';
require_once __DIR__ . '/../src/ShoppingManagerFactory.php';
require_once __DIR__ . '/../src/Carrier.php';
require_once __DIR__ . '/../src/Product.php';
require_once __DIR__ . '/../src/User.php';

class OrderUseCaseTest extends PHPUnit_Extensions_Database_TestCase
{
    private $connection;
    
    protected function getConnection()
    {
        $conn = new PDO(
            'mysql:host=localhost;dbname=store;charset=UTF8',
            'root',
            'coderslab'
        );
        return $this->createDefaultDBConnection($conn, 'store');
    }

    protected function getDataSet()
    {
        $dataXML = $this->createXMLDataSet(__DIR__ . '/testdb.xml');
        return $dataXML;
    }
    
    protected function setUp()
    {
        parent::setUp();
        $this->connection = new PDO(
            'mysql:host=localhost;dbname=store;charset=UTF8',
            'root',
            'coderslab'
        );
    }
    
    public function testOrderUseCase()
    {
        //klient dokonuje jakiejś czynności, np. dodaje produkt do koszyka
        
        //pobieramy id zalgowanego usera z sesji
        $userId = 1;
        
        //wczytujemy użytkownika
        $user = User::loadUserByColumn($this->connection, 'id', $userId);
        
        //tworzymy ShoppingManagera. Manager pomaga wykonać pewne skomplikowane czynności na zamówieniach. Utworzenie go jest dość skomplikowane, dlatego pomagamy sobie fabryką.
        $manager = ShoppingManagerFactory::create($this->connection);
        
        //pobieramy istniejący koszyk użytkownika albo tworzymy nowy. Koszyk i zamówienie to ta sama klasa, różni się tylko statusem
        $basket = $manager->loadOrCreateBasketByUser($user);
        //Koszyk automatycznie będzie mieć ustawione poprawne atrybuty: userId, billingAddress, shippingAddress i co najmniej jeden status: Basket. W koszyku będą już produkty (jeśli użytkownik je wcześniej dodał i zapisałeś to w bazie). Dopóki zamówienie nie zostanie złożone, cena produktów i koszt wysyłki jest aktualizowany po każdym pobraniu z bazy (nie wczytujemy starych, historycznych cen, tylko nowe).
        
        //dokonujemy zmian, np.:
        
        //aby dodać produkt, tworzymy obiekt produktu i dodajemy go do koszyka (podajemy liczbę sztuk)
        $newProduct = Product::showProductById($this->connection, 1);
        $basket->addProducts($newProduct, 2);
        //koszyk automatycznie sam podliczy łączną kwotę do zapłaty w koszyku
        
        //aby usunąć produkt z koszyka
        $basket->removeProducts($newProduct, 1);
        
        //aby usunąć wszystkie produkty z koszyka
        $basket->clearProducts();
        
        //aby policzyć produkty w koszyku
        $numberOfItems = $basket->countProducts();
        
        //aby pobrać obiekty produktów z koszyka
        $products = $basket->getOrderProducts();
        //pamiętaj, że otrzymujesz obiekty OrderProduct, a nie Product. OrderProduct zawiera id zamówienia, id produktu, ilość w koszyku i historyczną cenę. Dzięki temu, że zawiera cenę, możesz zmienić cenę produktu w sklepie, a historia zamówień będzie cały czas pokazywać stare ceny.
        
        //aby zmienić formę dostawy:
        $basket->setCarrier(Carrier::CARRIER_POCZTEX);
        //Koszyk sam automatycznie zmieni koszt wysyłki i łączną kwotę do zapłaty
        
        //aby zmienić status, robimy:
        $manager->setNewStatusFor($basket, OrderStatus::STATUS_SUBMITTED);
        //w ten sposób do listy statusów dodaje się kolejny status o podanym id
        //pierwszy status to Basket, drugi: Submitted, itd.
        //nie da się usunąć raz utworzonego statusu. Można dodać do bazy nowy status, nie można zmienić starego
        
        //aby pobrać wszystkie statusy, czyli historię zamówienia, robimy:
        $basket->getStatuses();
        //statusy są ułożone chronologicznie od najstarszego
        
        //aby pobrać ostatni status, czyli de facto sprawdzić, jaki jest status zamówienia, robimy:
        $basket->getLastStatus();
        
        //zmiana statusu na Submitted oznacza złożenie zamówienia. Po tej zmianie obiekt zachowuje się inaczej - po ponownym wczytaniu z bazy cena produktów i koszt wysyłki nie będą się aktualizować - będa dotyczyć momentu złożenia zamówienia.
        
        //aby zapisać zmiany w bazie (w tym zmianę statusu), robimy:
        $manager->save($basket);
        //zapisze się zamówienie, wszystkie produkty w środku oraz dodane statusy
        
        //aby wczytać wszystkie zamówienia danego użytkownika (bez koszyka), robimy:
        $manager->loadSubmittedOrdersByUser($user);
        
        //aby wczytać wszystkie ostatnie zamówienia, robimy:
        $manager->loadRecentOrders(25, 0);
        //25,  0   => ostatnie 25 zamówień
        //25, 25   => zamówienia 26-50
        //25, 50   => zamówienia 51-75
        //itd.
        
        //uwaga: wymienione klasy nie sprawdzają w żaden sposób, czy dostępność danego produktu jest wystarczająca. Po złożeniu zamówienia nie zmieniają dostępności
        
        //uwaga: nie używaj bezpośrednio klas zakończonych na Gateway i zawsze w miarę możliwości używaj metod w klasach wyżej położonych, np. rób $order->addProducts($product, 2), zamiast pobierać sobie obiekt OrderProduct i robić $orderProduct->setQuantity(2), bo wszystko się rozsypie
        
        
    }
    
}