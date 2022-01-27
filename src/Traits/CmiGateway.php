<?php

namespace Combindma\Cmi\Traits;

use Combindma\Cmi\Cmi;
use Illuminate\Support\Facades\Log;

trait CmiGateway
{
    public function requestPayment(Cmi $cmiClient, array $params = [])
    {
        try {
            $cmiClient->guardAgainstInvalidRequest();
            $payData = $cmiClient->getCmiData($params);
            $hash = $cmiClient->getHash($params);
        } catch (\Exception $e) {
            Log::error($e);

            return redirect($cmiClient->getShopUrl())->withErrors(['payment' => __('Une erreur est survenue, veuillez réessayer ultérieurement.')]);
        }

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
        $cmiClient->setTel('0600000000');
        $cmiClient->setCurrency('504');
        $cmiClient->setDescription('ceci est un exemple à utiliser');
        $otherData = [
            'billToStreet1' => 'street fighter',
            'billToCity' => 'casanegra',
            'BillToCountry' => 'morocco',
            //etc...
        ];

        return $this->requestPayment($cmiClient, $otherData);
    }
}
