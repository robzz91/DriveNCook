public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:clients',
        'password' => 'nullable|string|min:6',
    ]);

    // Si pas de password fourni → on met NULL
    if (!isset($validated['password'])) {
        $validated['password'] = null;
    } else {
        $validated['password'] = Hash::make($validated['password']);
    }

    $client = Client::create($validated);

    return response()->json($client, 201);
}
