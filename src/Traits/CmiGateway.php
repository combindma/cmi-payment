<?php

namespace Combindma\Cmi\Traits;

use Combindma\Cmi\Cmi;

trait CmiGateway
{
    public function requestPayment(Cmi $cmiClient, array $params = [])
    {
        $cmiClient->guardAgainstInvalidRequest();
        $payData = $cmiClient->getCmiData($params);
        $hash = $cmiClient->getHash($params);

        return view('cmi::request-payment', compact('cmiClient', 'payData', 'hash'));
    }

    /*
     * Ceci est un exemple de requête que vous pouvez faire
     * */
    public function testCmiOrder()
    {
        $cmiClient = new Cmi();
        $cmiClient->setOid(date('dmy').rand(100, 9000));
        $cmiClient->setAmount(100);
        $cmiClient->setBillToName('Combind Agency');
        $cmiClient->setEmail('webmaster@combind.ma');
        $cmiClient->setTel(0600000000);
        $cmiClient->setCurrency(504);
        $cmiClient->setDescription('ceci est un exemple à utiliser');
        $otherData = [
            'billToStreet1' => '',
            'billToCity' => '',
            'BillToCountry' => '',
            //etc...
        ];
        $this->requestPayment($cmiClient, $otherData);
    }
}
