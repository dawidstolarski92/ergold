<?php
/**
 * LICENCE
 *
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 *
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 *
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */

/**
 * PaczkomatyInpostApi
 */
class PaczkomatyInpostApi
{

    private $url = 'api.paczkomaty.pl/';
    private $url_test = 'sandbox-api.paczkomaty.pl/';

    public function getParams()
    {
        return $this->getContent(Tools::strtolower(__FUNCTION__));
    }

    /**
     * Zwraca pełną listę aktywnych paczkomatów
     *
     * @param string $paymentavailable 't', 'f', ''
     * @param string $pickuppoint 't', 'f', ''
     * @return SimpleXMLElement
     */
    public function listMachines($paymentavailable = '', $pickuppoint = '')
    {
        $data = array(
            'paymentavailable' => $paymentavailable,
            'pickuppoint' => $pickuppoint
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__).'_xml', 'GET', $data);
    }

    /**
     * Zwraca informacje o 3 najbliższych paczkomatach.
     *
     * @param string $postcode
     * @param limit $limit default 3
     * @param string $paymentavailable  't', 'f', ''
     * @return SimpleXMLElement
     */
    public function findNearestMachines($postcode, $limit = 3, $paymentavailable = '')
    {
        $data = array(
            'postcode' => $postcode,
            'limit' => $limit,
            'paymentavailable' => $paymentavailable
        );

        return $this->getContent(Tools::strtolower(__FUNCTION__), 'GET', $data);
    }

    /**
     * Zwraca szczegółowe informacje na temat wybranego paczkomatu.
     *
     * @param string $name
     * @return SimpleXMLElement
     */
    public function findMachineByName($name)
    {
        $data = array(
            'name' => $name
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'GET', $data);
    }

    /**
     * Zwraca standardowy cennik przesyłek.
     *
     * Funkcja zwraca plik XML zawierający informacje o cenach przesyłek o rozmiarach A, B i C
     * oraz o dodatkowym koszcie w przypadku przesyłki za pobraniem.
     *
     * @param string $email
     * @param string $password
     * @return SimpleXMLElement
     */
    public function priceList($email = '', $password = '')
    {
        $data = array(
            'email' => $email,
            'password' => $password
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data);
    }

    /**
     * Zwraca status przesyłki.
     *
     * @param string $packcode
     * @return SimpleXMLElement
     */
    public function getPackStatus($packcode)
    {
        $data = array(
            'packcode' => $packcode
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'GET', $data);
    }

    /**
     * Sprawdza czy użytkownik jest zarejestrowany w systemie i zwraca informacje opisujące użytkownika.
     *
     * @param string $email Adres e-mail użytkownika zarejestrowanego w systemie Paczkomaty InPost
     * @return SimpleXMLElement
     */
    public function findCustomer($email)
    {
        $data = array(
            'email' => $email
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'GET', $data);
    }

    /**
     * Rejestruje w systemie paczki do wysłania.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura danych XML zawierająca informacje o paczkach, które mają być utworzone w systemie.
     * @return SimpleXMLElement
     */
    public function createDeliveryPacks($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Zwraca plik etykiety na paczkę w żądanym formacie.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param int|array $packcode Kod paczki w systemie Paczkomaty InPost
     * @param string $label_type typ etykiety, dostępne typy: A4, lub pusty string – etykieta standardowa – do 3 szt na stronie A4;
     * 							A6P – etykieta A6 w orientacji pionowej
     * @param string $label_format format etykiety, dostępne typy: Pdf, lub pusty string – etykieta w formacie PDF; Epl2 – etykieta w formacie EPL2
     * @return SimpleXMLElement
     */
    public function getSticker($email, $password, $packcode, $label_type = 'A4', $label_format = 'Pdf')
    {
        if ($label_type == null)
            $label_type = 'A4';
        if ($label_format == null)
            $label_format = 'Pdf';

        $data = array(
            'email' => $email,
            'password' => $password,
            'packcode' => $packcode,
            'labelType' => $label_type,
            'labelFormat' => $label_format
        );

        if (is_array($packcode)) {
            unset($data['packcode']);
            $data['packcodes'] = $packcode;
        }
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true, false);
    }

    /**
     * Przypisuje do wygenerowanej wcześniej paczki dodatkową informację, która zostanie wydrukowana na etykiecie.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $packcode Kod paczki w systemie Paczkomaty InPost
     * @param string $customerref Informacja dodatkowa drukowana na etykiecie
     * @return SimpleXMLElement
     */
    public function setCustomerRef($email, $password, $packcode, $customerref)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'packcode' => $packcode,
            'customerref' => $customerref
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja umożliwia pobranie szczegółowych informacji o paczkach wygenerowanych przez określonego nadawcę.
     * Lista paczek może być zawężona do paczek o określonym statusie, wygenerowanych w określonym zakresie dat.
     * Możliwe jest również odfiltrowanie paczek w zależności od tego, czy potwierdzenie nadania zostało wydrukowane.
     * Funkcja umożliwia pobranie listy paczek za okres maksymalnie 60 dni. W przypadku podania większego zakresu dat zostanie zwrócony błąd.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $status Status paczki w systemie (niewymagane)
     * @param string $startdate Data początkowa dla listy (niewymagane) np: 2014-07-22T00:00:00
     * @param string $enddate Data końcowa dla listy (niewymagane) np: 2014-07-23T00:00:00
     * @param string $is_conf_printed Czy potwierdzenie nadania zostało wygenerowane (niewymagane)
     * @param string $customer_ref Informacja dodatkowa podana podczas tworzenia paczki (niewymagane)
     * @return SimpleXMLElement
     */
    public function getPacksBySender($email, $password, $status = '', $startdate = '', $enddate = '', $is_conf_printed = '', $customer_ref = '')
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'status' => $status,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'is_conf_printed' => $is_conf_printed,
            'customerRef' => $customer_ref
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Usuwa przesyłkę o statusie Created.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $packcode Kod paczki w systemie Paczkomaty InPost
     * @return SimpleXMLElement
     */
    public function cancelPack($email, $password, $packcode)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'packcode' => $packcode
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Zmienia gabaryt paczki w statusie Created.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $packcode Kod paczki w systemie Paczkomaty InPost
     * @param string $packsize Nowy gabaryt (dopuszczalne wartości to ‘A’, ‘B’, ‘C’)
     * @return SimpleXMLElement
     */
    public function changePackSize($email, $password, $packcode, $packsize)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'packcode' => $packcode,
            'packsize' => $packsize
        );
        return $this->getContent('change_packsize', 'POST', $data, true);
    }

    /**
     * Pobiera potwierdzenia nadania paczek do wysłania w formacie PDF.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function getConfirmPrintout($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true, false);
    }

    /**
     * Funkcja umożliwia pobranie szczegółowych informacji o transakcjach pobraniowych.
     * Funkcja umożliwia pobranie listy transakcji za okres maksymalnie 60 dni.
     * W przypadku podania większego zakresu dat zostanie zwrócony błąd.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $startdate Data początkowa dla listy
     * @param string $enddate Data końcowa dla listy
     * @return SimpleXMLElement
     */
    public function getCodReport($email, $password, $startdate, $enddate)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'startdate' => $startdate,
            'enddate' => $enddate
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja procesuje paczkę ze statusu Created do Prepared.
     * Implikuje to pobranie opłaty za paczkę.
     * Wykorzystywana dla implementacji w której klient sam drukuje etykiety
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $packcode Kod paczki w systemie Paczkomaty InPost
     * @return SimpleXMLElement
     */
    public function payForPack($email, $password, $packcode)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'packcode' => $packcode
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true, false);
    }

    /**
     * Funkcja tworzy punkt nadawczy, czyli miejsce zdefiniowane przez klienta z którego kurier firmy InPost odbierać będzie przesyłki paczkomatowe.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function createDispatchPoint($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );

        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja zwraca listę punktów nadawczych przypisanych do klienta.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function getDispatchPoints($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja aktualizuje dane punktu nadawczego.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function updateDispatchPoint($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja usuwa punkt nadawczy.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function deleteDispatchPoint($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja tworzy zlecenie zamówienia kuriera do punktu nadawczego,
     * czyli miejsce zdefiniowane przez klienta z którego kurier firmy InPost odebrać ma przesyłki paczkomatowe.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function createDispatchOrder($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content,
            'comment' => ''
        );

        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja zwraca listę zleceń zamówienia kuriera (zzk) do punktu nadawczego przypisanego do klienta.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function getDispatchOrder($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja anuluje zlecenia zamówienia kuriera (zzk).
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function cancelDispatchOrder($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true);
    }

    /**
     * Funkcja zwraca dokument w formacie PDF – potwierdzenie odbioru paczek.
     *
     * @param string $email Adres e-mail użytkownika API (nadawcy)
     * @param string $password Hasło użytkownika API (nadawcy)
     * @param string $content Struktura XML
     * @return SimpleXMLElement
     */
    public function getDispatchOrderPrintout($email, $password, $content)
    {
        $data = array(
            'email' => $email,
            'password' => $password,
            'content' => $content
        );
        return $this->getContent(Tools::strtolower(__FUNCTION__), 'POST', $data, true, false);
    }

    /**
     * Wysyła dane i zwraca odpowiedni XML
     *
     * @param string $action
     * @param string $method post or get
     * @param array $data
     * @throws Exception error
     * @return SimpleXMLElement
     */
    private function getContent($action, $method = 'GET', array $data = array(), $secured = false, $return_xml = true)
    {
        $method = Tools::strtoupper($method);
        $prefix = true ? 'https://' : 'http://';
        $url = $prefix.(Configuration::get('PACZKOMATYINPOST_TEST') ? $this->url_test : $this->url).'?do='.$action;
//        unset($data['content']);
//        d($url);
        if ($method == 'POST') {
//            Tools::d($data);
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded",
                    'method' => Tools::strtoupper($method),
                    'content' => http_build_query($data),
                    'timeout' => 30
                ),
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $context = stream_context_create($options);
        } else {
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded",
                    'method' => Tools::strtoupper($method),
                    'timeout' => 30
                ),
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $context = stream_context_create($options);
            $url .= '&'.http_build_query($data);
        }

        $content = Tools::file_get_contents($url, false, $context, 5);
//        Tools::d(array('action' => $action, 'url' => $url, 'content' => $content, 'options'=>$options));

        if (empty($content)) {
            throw new Exception('Błąd odpowiedzi serwera paczkomatów. Spróbuj ponownie za kilka chwil.');
        }
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (isset($xml->error))
            throw new Exception($xml->error);

        if (!$return_xml)
            return $content;
        return $xml;
    }
}

class PaczkomatyInpostPackStatus
{

    const UNDEFINED = 'UNDEFINED';
    const CREATED = 'Created';
    const PREPARED = 'Prepared';
    const SENT = 'Sent';
    const INTRANSIT = 'InTransit';
    const STORED = 'Stored';
    const AVIZO = 'Avizo';
    const CUSTOMERDELIVERING = 'CustomerDelivering';
    const CUSTOMERSTORED = 'CustomerStored';
    const LABELEXPIRED = 'LabelExpired';
    const EXPIRED = 'Expired';
    const DELIVERED = 'Delivered';
    const RETUNEDTOAGENCY = 'RetunedToAgency';
    const CANCELLED = 'Cancelled';
    const CLAIMED = 'Claimed';
    const CLAIMPROCESSED = 'ClaimProcessed';

    public static function getLabel($status)
    {
        $labels = self::getLabels();
        if (isset($labels[$status]))
            return $labels[$status];
        return 'Nieznany';
    }

    public static function getLabels()
    {
        return array(
            self::UNDEFINED => 'Nie zdefiniowano',
            self::CREATED => 'Oczekuje na wysyłkę',
            self::PREPARED => 'Gotowa do wysyłki',
            self::SENT => 'Przesyłka nadana',
            self::INTRANSIT => 'Przesyłka w drodze',
            self::STORED => 'Oczekuje na odbiór',
            self::AVIZO => 'Ponowne awizo',
            self::CUSTOMERDELIVERING => 'Nadawana w paczkomacie',
            self::CUSTOMERSTORED => 'Umieszczona przez klienta',
            self::LABELEXPIRED => 'Etykieta przeterminowana',
            self::EXPIRED => 'Nie odebrana',
            self::DELIVERED => 'Dostarczona',
            self::RETUNEDTOAGENCY => 'Przekazana do oddziału',
            self::CANCELLED => 'Anulowana',
            self::CLAIMED => 'Przyjęto zgłoszenie reklamacyjne',
            self::CLAIMPROCESSED => 'Rozpatrzono zgłoszenie reklamacyjne',
        );
    }
}
