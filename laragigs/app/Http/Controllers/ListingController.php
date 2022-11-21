<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
	// Show all listings
	public function index() {
		return view('listings.index', [
			// passing data to the view
			// 'listings' => Listing::all(),
			// 'listings' => Listing::latest()->get(), // same as all() but sorted
			// 'listings' => Listing::latest()->filter(request(['tag', 'search']))->get(), // with filter
			'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(6), // with pagination => replace get() by paginate(nb_items_per_page)
		]);
	}

	// Show single listing
	public function show(Listing $listing) {
		return view('listings.show', [
			'listing' => $listing,
		]);
	}

	// Show create form
	public function create() {
		return view('listings.create');
	}

	// Store listing data
	public function store(Request $request) {

		$formFiels = $request->validate([
			'title' => 'required',
			'company' => ['required', Rule::unique('listings', 'company')],
			'location' => 'required',
			'website' => 'required',
			'email' => ['required', 'email'],
			'tags' => 'required',
			'description' => 'required',
		]);

		// If user put a logo => store it in the logos folder
		if ($request->hasFile('logo')) { // if added a photo
			$formFiels['logo'] = $request->file('logo')->store('logos', 'public'); // Add the path to the form fields
		}

		// Add the user id (user who logged in) to the listing which we create to make the "Ownership" with the user
		$formFiels['user_id'] = auth()->id();

		Listing::create($formFiels);

		return redirect('/')->with('message', 'Listing created successfully!');
	}

	// Show edit form
	public function edit(Listing $listing) {
		return view('listings.edit', ['listing' => $listing]);
	}

	// Update listing data
	public function update(Request $request, Listing $listing) {

		// Make sure logged in user is the owner
		if ($listing->user_id != auth()->id()) {
			abort(403, 'Unauthorized Action');
		}

		$formFiels = $request->validate([
			'title' => 'required',
			'company' => ['required'],
			'location' => 'required',
			'website' => 'required',
			'email' => ['required', 'email'],
			'tags' => 'required',
			'description' => 'required',
		]);

		if ($request->hasFile('logo')) { // if added a photo
			$formFiels['logo'] = $request->file('logo')->store('logos', 'public'); // Add the path to the form fields
		}

		$listing->update($formFiels);

		return back()->with('message', 'Listing updated successfully!');
	}

	// Delete listing
	public function destroy(Listing $listing) {
		
		// Make sure logged in user is the owner
		if ($listing->user_id != auth()->id()) {
			abort(403, 'Unauthorized Action');
		}

		$listing->delete();
		return redirect('/')->with('message', 'Listing deleted successfully');
	}

	// Manage listings
	public function manage() {
		return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
	}

}

