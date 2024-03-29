@extends('layouts.admin	')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">{{ __("Edit Item Book") }}</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
								<li class="breadcrumb-item"><a href="{{ route("items.index") }}">{{ __("Item Book") }}</a></li>
								<li class="breadcrumb-item active">{{ __("Edit Item Book") }}</li>
							</ol>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

			<div class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<div class="card">
									<div class="card-body">

										<form id="addPhotos" action="{{route('photos.store')}}" method="POST" enctype="multipart/form-data"></form>

										<form id="deletePhotos" action="{{route('photos.destroy')}}" method="POST">
											@method('DELETE')
											@csrf
										</form>
										
										<form method="POST" action="{{ route('items.update', $item->id) }}">

											<div class="row">
												<div class="col-12 mb-3">
													<div class="card">
														<div class="card-header">
															<div class="card-title">{{ __("Store Information") }}</div>
														</div>
														<div class="card-body">
															<div class="form-group row">
																<label for="store_id" class="col-2 col-form-label">Store Name <span style="color: red">*</span></label>
																<div class="col-10">
																	<select class="form-control" name="store_id">
																		@foreach ($stores as $store)
																		<option value="{{ $store->id }}" {{ $store->id == $item->store_id ? 'selected' : '' }}>{{ $store->name }} ({{ $store->code }})</option>
																		@endforeach
																	</select>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
					
											<div class="row">
												<div class="col-md-6 mb-3 col-xs-12">
													<div class="card">
														<div class="card-header">
															<div class="card-title">{{ __("Item Information") }}</div>
														</div>
														<div class="card-body">
															<div class="form-group row">
																@csrf
																<label for="item_no" class="col-3 col-form-label">Item No <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="item_no" value="{{ $item->item_no }}" required readonly />
																	<!-- <label class="col-form-label"><span style="color: lightgrey"><i>auto generated by system.</i></span></label> -->
																</div>
															</div>
															<div class="form-group row">
																<label for="item_name" class="col-3 col-form-label">Item Name <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="item_name" required value="{{ $item->item_name }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="item_weight" class="col-3 col-form-label">Item Weight <span style="color: red">*</span></label>
																<div class="col-9">
																	<div class="input-group">
																		<input type="text" class="form-control" name="item_weight" required value="{{ $item->item_weight }}" />
																		<div class="input-group-append">
																			<div class="input-group-text">gr</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="category_id" class="col-3 col-form-label">Category <span style="color: red">*</span></label>
																<div class="col-9">
																	<!-- <select class="form-control" name="category_id"> -->
																		@foreach ($categories as $category)
																			@if ($category->id == $item->category_id)
																				<input type="text" class="form-control" name="category_id" hidden value="{{ $item->category_id }}" />
																				<input type="text" class="form-control" name="category" readonly value="{{ $category->description }}" />
																				@break
																			@endif
																		@endforeach
																	<!-- </select> -->
																</div>
															</div>
															<div class="form-group row">
																<label for="item_gold_rate" class="col-3 col-form-label">Gold Rate <span style="color: red">*</span></label>
																<div class="col-9">
																	<div class="input-group">
																		<input type="text" class="form-control" name="item_gold_rate" required value="{{ $item->item_gold_rate }}" />
																		<div class="input-group-append">
																			<div class="input-group-text">%</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="allocation_id" class="col-3 col-form-label">Allocation <span style="color: red">*</span></label>
																<div class="col-9">
																	<select class="form-control" name="allocation_id">
																		@foreach ($allocations as $allocation)
																			<option value="{{ $allocation->id }}" {{ $allocation->id == $item->allocation_id ? 'selected' : '' }}>{{ $allocation->description }}</option>
																		@endforeach
																	</select>
																</div>
															</div>
															<div class="form-group row">
																<label for="item_status_id" class="col-3 col-form-label">Item Status <span style="color: red">*</span></label>
																<div class="col-9">
																	<select class="form-control" name="item_status_id">
																		@foreach ($itemstatuses as $itemstatus)
																			<option value="{{ $itemstatus->id }}" {{ $itemstatus->id == $item->item_status_id ? 'selected' : '' }}>{{ $itemstatus->description }}</option>
																		@endforeach
																	</select>
																</div>
															</div>
															<div class="form-group row">
																<label for="inventory_status_id" class="col-3 col-form-label">Inventory Status <span style="color: red">*</span></label>
																<div class="col-9">
																	<select class="form-control" name="inventory_status_id">
																		@foreach ($inventorystatuses as $inventorystatus)
																			<option value="{{ $inventorystatus->id }}" {{ $inventorystatus->id == $item->inventory_status_id ? 'selected' : '' }}>{{ $inventorystatus->description }}</option>
																		@endforeach
																	</select>
																</div>
															</div>
															<div class="form-group row">
																<label for="created_by" class="col-3 col-form-label">Created By</label>
																<div class="col-9">
																	@foreach ($users as $user)
																		@if($user->id == $item->created_by)
																			<input type="text" class="form-control" name="created_by" value="{{ $user->id }}" hidden />
																			<input type="text" class="form-control" name="created_by_name" value="{{ $user->name }}" readonly />
																			@break
																		@endif
																	@endforeach
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-6 mb-3 col-xs-12">
													<div class="card">
														<div class="card-header">
															<div class="card-title">{{ __("Sales Information") }}</div>
														</div>
														<div class="card-body">
															<div class="form-group row">
																<label for="sales_status_id" class="col-3 col-form-label">Sales Status</label>
																<div class="col-9">
																	<select class="form-control" name="sales_status_id">
																		<option selected></option>
																		@foreach ($salesstatuses as $salesstatus)
																			<option value="{{ $salesstatus->id }}" {{ $salesstatus->id == $item->sales_status_id ? 'selected' : '' }}>{{ $salesstatus->code }}</option>
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
																		<input type="text" class="form-control sales_price" name="sales_price" value="{{ $item->sales_price }}" />
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="sales_at" class="col-3 col-form-label">Sales At</label>
																<div class="col-9">
																	<div class="input-group">
																		<input type="text" class="form-control dateselect" id="sales_at" name="sales_at" placeholder="select date" value="{{ $item->sales_at != null ? Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->sales_at)->format('m/d/Y') : '' }}" />
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="sales_by" class="col-3 col-form-label">Sales By</label>
																<div class="col-9">
																	<select class="form-control" name="sales_by">
																		<option></option>
																		@foreach ($users as $user)
																			<option value="{{ $user->id }}" {{ $user->id == $item->sales_by ? 'selected' : '' }}>{{ $user->name }}</option>
																		@endforeach
																	</select>
																</div>
															</div>
														</div>
													</div>
													<div class="card">
														<div class="card-header">
															<div class="card-title">{{ __("Photos") }}</div>
															<label class="btn-sm btn-primary float-right">
																<span><i class="nav-icon fas fa-plus"></i></span> <input type="file" name="images[]" multiple hidden onChange="$('#addPhotos').submit();" form="addPhotos">
																<input type="text" hidden name="itemid" value="{{$item->id}}" form="addPhotos">
																<input name="_token" value="{{ csrf_token() }}" type="hidden" form="addPhotos">
															</label>
														</div>
														<div class="card-body">
															@foreach ($photos as $photo)
																<div class="form-group row">
																	<div class="col-9">
																		<img src="{{asset('img/'.$photo->img_url)}}" width=100 height=100>
																	</div>
																	<div class="col-3 my-auto text-center">
																		<input hidden type="text" value="{{$photo->id}}" name="photoid" form="deletePhotos">
																		<button type="submit" form="deletePhotos">
																			<span>
																				<i class="fa fa-trash" style="color:red"></i>
																			</span>
																		</button>
																	</div>
																</div>
															@endforeach
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
					<!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script type="text/javascript">
	$(function() {
        $('#sales_at').datepicker();
    });

	$('#category_id').on('change', function(e) {
		var categoryText = (this.options[this.selectedIndex].text).toLowerCase();
		if(categoryText == "kalung (k)") {
			document.getElementById("item_gold_rate").value = 42.0;
		} else {
			document.getElementById("item_gold_rate").value = 37.5;
		}
	});
</script>
@endsection