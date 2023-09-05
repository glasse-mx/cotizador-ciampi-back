<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    /**
     * This Function Register a Client into the database
     */
    public function createClient(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "first_name" => "required",
                "last_name" => "required",
                "email" => "email|unique:clients",
                "phone" => "required|unique:clients"
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $client = new Client();
            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->email = $request->email;
            $client->phone = $request->phone;
            $client->address_street = $request->address_street;
            $client->address_ext = $request->address_ext;
            $client->address_int = $request->address_int;
            $client->address_col = $request->address_col;
            $client->address_town = $request->address_town;
            $client->address_state = $request->address_state;
            $client->address_zip = $request->address_zip;
            $client->save();

            return response()->json([
                "Message" => "Cliente Agregado con exito!"
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * This Function provides a List of the clients 
     */
    public function getClients()
    {
        $clients = Client::all();

        return response()->json($clients);
    }

    /**
     * This function Returns a single client using as search parameter
     * the clients phone number or Its ID
     */
    public function getClient($item)
    {
        $client = Client::where('id', $item)
            ->orWhere('phone', $item)
            ->first();

        if ($client) {
            return response()->json($client);
        } else {
            return response()->json([
                "Message" => "Cliente no encontrado"
            ]);
        }
    }

    /**
     * Edit a client given its phone or ID
     */
    public function editClient(Request $request, $item)
    {

        try {
            $client = Client::where('id', $item)
                ->orWhere('phone', $item)
                ->first();

            $validator = Validator::make($request->all(), [
                "first_name" => "required",
                "last_name" => "required",
                "email" => "email|unique:clients",
                "phone" => "required|unique:clients"
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->email = $request->email;
            $client->phone = $request->phone;
            $client->address_street = $request->address_street;
            $client->address_ext = $request->address_ext;
            $client->address_int = $request->address_int;
            $client->address_col = $request->address_col;
            $client->address_town = $request->address_town;
            $client->address_state = $request->address_state;
            $client->address_zip = $request->address_zip;
            $client->save();

            return response()->json([
                "Message" => "Usuario Editado con Exito!"
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function deleteClient($item)
    {

        $client = Client::where('id', $item)
            ->orWhere('phone', $item)
            ->first();

        $client->delete();

        return response()->json([
            "message" => "El cliente ha sido eliminado con exito"
        ]);
    }
}
