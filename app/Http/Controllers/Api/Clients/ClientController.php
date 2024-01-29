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

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getClient']]);
    }


    /**
     * Registrar un nuevo cliente
     * Esta funcion permite agregar un nuevo cliente a la base de datos
     * 
     * @param Request $request
     * @return Response
     */
    public function createClient(Request $request)
    {
        try {
            if ($request->email == '') {
                $validator = Validator::make($request->all(), [
                    "first_name" => "required",
                    "last_name" => "required",
                    "phone" => "required|unique:clients"
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    "first_name" => "required",
                    "last_name" => "required",
                    "email" => "nullable|email|unique:clients",
                    "phone" => "required|unique:clients"
                ]);
            }

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
                "Message" => "Cliente Agregado con exito!",
                "id" => $client->id
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Todos los Clientes
     * 
     * Esta funcion retorna todos los clientes registrados en la base de datos
     * paginados en 10 resultados a la vez
     * 
     * @return Response 
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
        $client = Client::where('id', $item)
            ->orWhere('phone', $item)
            ->first();

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
            "Message" => "Usuario Editado con Exito!",
            "id" => $client->id
        ]);
    }

    /**
     * Delete a client given its phone or ID
     */
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

    /**
     * Search a client by name, phone or email
     */
    public function searchClient($search)
    {

        $clients = DB::table('clients')
            ->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$search%'")
            ->orWhere('phone', 'LIKE', "%$search%")
            ->orWhere('email', 'LIKE', "%$search%")
            ->get();



        return response()->json($clients);
    }
}
