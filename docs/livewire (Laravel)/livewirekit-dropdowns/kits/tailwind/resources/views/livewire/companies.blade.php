<div>
    <form wire:submit.prevent="storeCompany" class="border-b-2 pb-10">
        <div>
            <label class="block font-medium text-sm text-gray-700" for="name">
                Company Name*
            </label>
            <input wire:model="name" type="text"
                   class="mt-2 text-sm sm:text-base pl-2 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400" required />
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700" for="country">
                Country*
            </label>
            <select wire:model="country" name="country"
                    class="mt-2 text-sm sm:text-base pl-2 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400" required>
                <option value="">-- choose country --</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700" for="city">
                City*
            </label>

            <select wire:model="city" name="city"
                    class="mt-2 text-sm sm:text-base pl-2 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400" required>
                @if ($cities->count() == 0)
                    <option value="">-- choose country first --</option>
                @endif
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center mt-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                Add Company
            </button>
        </div>
    </form>

    <h3 class="font-bold text-xl mt-10 mb-5">Latest Companies</h3>

    <table class="min-w-full">
        <thead>
        <tr>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">City</th>
        </tr>
        </thead>
        <tbody class="bg-white">
        @foreach ($companies as $company)
            <tr>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500 text-sm leading-5">{{ $company->name }}</td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500 text-sm leading-5">{{ $company->city->name }}, {{ $company->city->country->name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
