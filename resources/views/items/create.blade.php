@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

        	@if (session('status'))
            <div class="alert alert-success mb-3" role="alert">
                {{ session('status') }}
            </div>
            <br />
            @endif

            @if ($errors->any())
	        <div class="alert alert-danger">
	            <ul>
	                @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	                @endforeach
	            </ul>
	        </div>
	        <br /> 
	        @endif

	        <div class="row">
                <div class="col-12 mb-3">
                    <h3>
                        {{ __("Create New Item") }}
                    </h3>
                </div>
            </div>

	        <div class="row">
                <div class="col-12">

            		<form method="POST" action="{{ route('items.store') }}">

            			<div class="row">
                			<div class="col-12 mb-3">
		            			<div class="card">
								    <div class="card-body">
								    	<h4 class="card-title mb-4 mt-1">{{ __("Store Information") }}</h4>
				            			<div class="form-group row">
							                <label for="store_id" class="col-2 col-form-label">Store Name <span style="color: red">*</span></label>
							                <div class="col-10">
							                	<select class="form-control" name="store_id">
							                		@foreach ($stores as $store)
							                		<option value="{{ $store->id }}">{{ $store->name }} ({{ $store->code }})</option>
							                		@endforeach
							                	</select>
							                </div>
							            </div>
							        </div>
							    </div>
							</div>
						</div>

						<div class="row">
                			<div class="col-6 mb-3">
		            			<div class="card">
								    <div class="card-body">
								    	<h4 class="card-title mb-4 mt-1">{{ __("Item Information") }}</h4>
								    	<div class="form-group row">
						                	@csrf
						                	<label for="item_no" class="col-3 col-form-label">Item No <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<input type="text" class="form-control" name="item_no" required />
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="item_name" class="col-3 col-form-label">Item Name <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<input type="text" class="form-control" name="item_name" required />
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="item_weight" class="col-3 col-form-label">Item Weight <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<div class="input-group">
							                		<input type="text" class="form-control" name="item_weight" required />
							                		<div class="input-group-append">
							                			<div class="input-group-text">mg</div>
							                		</div>
							                	</div>
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="item_gold_rate" class="col-3 col-form-label">Gold Rate <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<div class="input-group">
							                		<input type="text" class="form-control" name="item_gold_rate" required />
							                		<div class="input-group-append">
							                			<div class="input-group-text">%</div>
							                		</div>
							                	</div>
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="category_id" class="col-3 col-form-label">Category <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<select class="form-control" name="category_id">
							                		@foreach ($categories as $category)
							                			<option value="{{ $category->id }}">{{ $category->description }} ({{ $category->code }})</option>
							                		@endforeach
							                	</select>
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="allocation_id" class="col-3 col-form-label">Allocation <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<select class="form-control" name="allocation_id">
							                		@foreach ($allocations as $allocation)
							                			<option value="{{ $allocation->id }}">{{ $allocation->description }} ({{ $allocation->code }})</option>
							                		@endforeach
							                	</select>
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="item_status_id" class="col-3 col-form-label">Item Status <span style="color: red">*</span></label>
							                <div class="col-9">
							                	<select class="form-control" name="item_status_id">
							                		@foreach ($itemstatuses as $itemstatus)
							                			<option value="{{ $allocation->id }}">{{ $itemstatus->description }} ({{ $itemstatus->code }})</option>
							                		@endforeach
							                	</select>
							                </div>
							            </div>
								    </div>
								</div>
							</div>
							<div class="col-6 mb-3">
								<div class="card">
								    <div class="card-body">
								    	<h4 class="card-title mb-4 mt-1">{{ __("Sales Information") }}</h4>
								    	<div class="form-group row">
							                <label for="sales_status_id" class="col-3 col-form-label">Sales Status</label>
							                <div class="col-9">
							                	<select class="form-control" name="sales_status_id">
							                		@foreach ($salesstatuses as $salesstatus)
							                			<option value="{{ $allocation->id }}">{{ $salesstatus->code }}</option>
							                		@endforeach
							                	</select>
							                </div>
							            </div>
								    	<div class="form-group row">
							                <label for="sales_price" class="col-3 col-form-label">Sales Price</label>
							                <div class="col-9">
							                	<div class="input-group">
								                	<div class="input-group-prepend">
											          	<div class="input-group-text">Rp</div>
											        </div>
								                	<input type="text" class="form-control" name="sales_price" />
								                </div>
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="sales_at" class="col-3 col-form-label">Sales At</label>
							                <div class="col-9">
							                	<div class="input-group">
							                		<input type="text" class="form-control dateselect" id="sales_at" name="sales_at" placeholder="select date" />
							                	</div>
							                </div>
							            </div>
							            <div class="form-group row">
							                <label for="sales_by" class="col-3 col-form-label">Sales By</label>
							                <div class="col-9">
							                	<select class="form-control" name="sales_by">
							                		<option value="1">one</option>
							                		<option value="2">two</option>
							                	</select>
							                </div>
							            </div>
								    </div>
								</div>
							</div>
						</div>

			            <div class="float-right">
			            	<a class="btn btn-secondary" href="{{ url()->previous() }}" role="button">{{ __("Back") }}</a>
				            <button type="submit" class="btn btn-primary">{{ __("Save") }}</button>
				        </div>
            		</form>

                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
	$(function () {
        $('#sales_at').datepicker();
    });
</script>
@endsection