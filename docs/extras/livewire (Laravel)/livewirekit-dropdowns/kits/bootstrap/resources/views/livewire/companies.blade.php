<div>
    <form wire:submit.prevent="storeCompany" class="mb-5">
        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">Company Name*</label>

            <div class="col-md-6">
                <input wire:model="name" type="text" class="form-control" required />
            </div>
        </div>

        <div class="form-group row">
            <label for="country" class="col-md-4 col-form-label text-md-right">Country*</label>

            <div class="col-md-6">
                <select wire:model="country" name="country" class="form-control" required>
                    <option value="">-- choose country --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="city" class="col-md-4 col-form-label text-md-right">City*</label>

            <div class="col-md-6">
                <select wire:model="city" name="city" class="form-control" required>
                    @if ($cities->count() == 0)
                        <option value="">-- choose country first --</option>
                    @endif
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    Add Company
                </button>
            </div>
        </div>
    </form>

    <hr />

    <h3>Latest Companies</h3>

    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>City</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($companies as $company)
            <tr>
                <td>{{ $company->name }}</td>
                <td>{{ $company->city->name }}, {{ $company->city->country->name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
