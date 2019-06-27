<?php

namespace App\Http\Controllers;

use App\Models\Secret;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class ApiV1Controller extends Controller
{

    public function addSecret(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'expireAfterViews' => 'required|integer',
            'expireAfter' => 'required|integer',
            'secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new Response("Invalid input, " . $validator->errors()->first(), 405);
        }

        $secret = new Secret();
        $now = new \DateTime();

        if ($request->get('expireAfter') && $request->get('expireAfter') !== 0) {

            $now_add = clone($now);
            $now_add->add(new \DateInterval('PT' . $request->get('expireAfter') . 'M'));

            $secret->expiresAt = $now_add->format('Y-m-d H:i:s');
        }

        $secret->secretText = $request->get('secret');
        $secret->hash = md5($request->get('secret') . $now->format('YmdHisu'));
        $secret->remainingViews = $request->get('expireAfterViews');
        $secret->createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $secret->save();

        $response = $this->prepareResponse($secret, $request);

        return $response;
    }

    public function getSecretByHash($hash, Request $request)
    {

        $secret = Secret::where('hash', '=', $hash)->first();
        $now = new \DateTime();

        if (!$secret || $secret->remainingViews < 1 || ($secret->expiresAt != null && new \DateTime($secret->expiresAt) < $now)) {
            return new Response("Secret not found", 404);
        }

        $secret->remainingViews = $secret->remainingViews - 1;
        $secret->save();

        $response = $this->prepareResponse($secret, $request);

        return $response;

    }

    private function prepareResponse(Secret $secret, Request $request)
    {

        $fieldsForResponse = array('hash', 'secretText', 'createdAt', 'expiresAt', 'remainingViews');

        if ($request->header('accept') == 'application/xml') {
            $xml = new \SimpleXMLElement('<Secret/>');
            $array = array_flip($secret->only($fieldsForResponse));
            array_walk_recursive($array, array($xml, 'addChild'));
            $resp = $xml->asXML();
        }

        if ($request->header('accept') == 'application/json') {
            $resp = json_encode($secret->only($fieldsForResponse));
        }

        if (isset($resp)) {
            return new Response($resp, 200, array('content-type' => $request->header('accept')));
        } else {
            return new Response("Unsupported content type please use xml or json.", 404);
        }
    }

}