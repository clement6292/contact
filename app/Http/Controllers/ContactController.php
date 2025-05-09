<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ContactCreatedNotification;
use App\Events\ContactEvent;
use App\Events\ContactUpdatedEvent;
use App\Events\ContactDeletedEvent;

class ContactController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
        // $this->middleware('auth')->except(['index']);
    }

    public function index()
{
    $contacts = Contact::latest()->paginate(9);// 6 contacts par page
    $countries = $this->countryService->getCountries();
    return Inertia::render('Contacts/Index', [
        'contacts' => $contacts,
        'countries' => $countries->values()->toArray(),
    ]);
}

    public function create()
    {
        $countries = $this->countryService->getCountries();
        return Inertia::render('Contacts/Create', [
            'countries' => $countries->values()->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'country' => 'required|string',
            'country_fly' => 'nullable|string',
        ]);

        $contact=Contact::create($validated);

        $user = Auth::user();
        $user->notify(new ContactCreatedNotification($contact));


        // dd("avant rediredtion");

        return redirect()->route('contacts.index')->with('success', 'Contact créé avec succès.');
    }

    public function edit(Contact $contact)
{
    $countries = $this->countryService->getCountries();
    return Inertia::render('Contacts/Edit', [
        'contact' => $contact,
        'countries' => $countries->values()->toArray(),
        'errors' => session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : [],
    ]);
}

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'country' => 'required|string',
            'country_fly' => 'nullable|string',
        ]);

        $contact->update($validated);

        event(new ContactUpdatedEvent($contact));

        return redirect()->route('contacts.index')->with('success', 'Contact mis à jour avec succès.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        event(new ContactDeletedEvent($contact));

        return redirect()->route('contacts.index')->with('success', 'Contact supprimé avec succès.');
    }
}