@extends('preferences')

@section('preferencescontent')
<div class="row">
	<div class="col-12">
		<h3>
		    {{ __("System Preferences") }}
		    <small class="text-muted">{{ __("- Manage Stores") }}</small>
		</h3>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<br/>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>@sortablelink('code', 'Store Code')</th>
						<th>@sortablelink('name', 'Store Name')</th>
						<th>@sortablelink('phone_no', 'Phone No')</th>
						<th>@sortablelink('address', 'Address')</th>
						<th>@sortablelink('created_at', 'Created At')</th>
						<th>@sortablelink('updated_at', 'Updated At')</th>
					</tr>
				</thead>
				<tbody>
					@if($stores->count())
						@foreach($stores as $store)
						<tr>
							<td>{{ $store->code }}</td>
							<td>{{ $store->name }}</td>
							<td>{{ $store->phone_no }}</td>
							<td>{{ $store->address }}</td>
							<td>{{ $store->created_at->format('d-m-Y') }}</td>
							<td>{{ $store->updated_at->format('d-m-Y') }}</td>
						</tr>
						@endforeach
					@else
						{{ __("No Data.") }}
					@endif
				</tbody>
			</table>
			{{ $stores->appends(\Request::except('page'))->render() }}
		</div>
	</div>
</div>
@endsection