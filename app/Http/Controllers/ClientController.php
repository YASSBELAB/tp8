<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        return ClientResource::collection(
            Client::with('orders')->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Client $client)
    {
        $client->load('orders');

        return new ClientResource($client);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore($client->id),
            ],
            'phone' => 'sometimes|nullable|string|max:30',
            'address' => 'sometimes|nullable|string',
        ]);

        $client->update($validated);

        return new ClientResource($client->fresh());
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->noContent();
    }
}
