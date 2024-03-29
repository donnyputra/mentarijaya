@extends('layouts.admin	')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">{{ __("Item Detail") }}</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
								<li class="breadcrumb-item"><a href="{{ route("items.index") }}">{{ __("Item Book") }}</a></li>
								<li class="breadcrumb-item active">{{ __("Item Detail") }}</li>
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
																	<input class="form-control" name="store_id" readonly value= "{{$item->store->name}} {{$item->store->code}}">
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
																<label for="item_no" class="col-3 col-form-label">Item No <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="item_no" value="{{ $item->item_no }}" required readonly />
																	<!-- <label class="col-form-label"><span style="color: lightgrey"><i>auto generated by system.</i></span></label> -->
																</div>
															</div>
															<div class="form-group row">
																<label for="item_name" class="col-3 col-form-label">Item Name <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="item_name" readonly value="{{ $item->item_name }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="item_weight" class="col-3 col-form-label">Item Weight <span style="color: red">*</span></label>
																<div class="col-9">
																	<div class="input-group">
																		<input type="text" class="form-control" name="item_weight" readonly value="{{ $item->item_weight }}" />
																		<div class="input-group-append">
																			<div class="input-group-text">gr</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="category_id" class="col-3 col-form-label">Category <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="category" readonly value="{{ $item->category->description }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="item_gold_rate" class="col-3 col-form-label">Gold Rate <span style="color: red">*</span></label>
																<div class="col-9">
																	<div class="input-group">
																		<input type="text" class="form-control" name="item_gold_rate" readonly value="{{ $item->item_gold_rate }}" />
																		<div class="input-group-append">
																			<div class="input-group-text">%</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="allocation_id" class="col-3 col-form-label">Allocation <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="allocation" readonly value="{{ $item->allocation->description }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="item_status_id" class="col-3 col-form-label">Item Status <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="item_status" readonly value="{{ $item->itemStatus->description }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="inventory_status_id" class="col-3 col-form-label">Inventory Status <span style="color: red">*</span></label>
																<div class="col-9">
																	<input type="text" class="form-control" name="inventory_status" readonly value="{{ $item->inventoryStatus->description }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="created_by" class="col-3 col-form-label">Created By</label>
																<div class="col-9">
																	<input type="text" class="form-control" name="created_by" readonly value="{{ $item->createdBy->name }}" />
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
														@php
															if($item->sales_status_id != null){
																$salesStatus = $item->salesStatus->description;
															} else {
																$salesStatus = '';
															}

															if($item->sales_by != null) {
																$salesBy = $item->salesBy->name;
															} else {
																$salesBy = '';
															}
														@endphp
														<div class="card-body">
															<div class="form-group row">
																<label for="sales_status_id" class="col-3 col-form-label">Sales Status</label>
																<div class="col-9">
																	<input type="text" class="form-control" name="sales_status" readonly value=" {{ $salesStatus }}" />
																</div>
															</div>
															<div class="form-group row">
																<label for="sales_price" class="col-3 col-form-label">Sales Price</label>
																<div class="col-9">
																	<div class="input-group">
																		<div class="input-group-prepend">
																				<div class="input-group-text">Rp</div>
																		</div>
																		<input type="text" class="form-control sales_price" readonly name="sales_price" value="{{ $item->sales_price }}" />
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="sales_at" class="col-3 col-form-label">Sales At</label>
																<div class="col-9">
																	<div class="input-group">
																		<input type="text" class="form-control" name="sales_at" readonly placeholder="" value="{{ $item->sales_at != null ? Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->sales_at)->format('m/d/Y') : '' }}" />
																	</div>
																</div>
															</div>
															<div class="form-group row">
																<label for="sales_by" class="col-3 col-form-label">Sales By</label>
																<div class="col-9">
																	<input type="text" class="form-control" name="sales_by" readonly value="{{ $salesBy }}" />
																</div>
															</div>
														</div>
													</div>
													<div class="card">
														<div class="card-header">
															<div class="card-title">{{ __("Photos") }}</div>
														</div>
														<div class="card-body">
															<div id="carouselPhotos" class="carousel slide" data-ride="carousel">
																<div class="carousel-inner">
																	@php
																		$i = 0;
																	@endphp
																	@foreach($photos as $photo)
																		@if($i == 0) 
																		<div class="carousel-item active">
																			<img class="d-block w-100" src="{{asset('img/'.$photo->img_url)}}">
																		</div>
																		@else
																		<div class="carousel-item">
																			<img class="d-block w-100" src="{{asset('img/'.$photo->img_url)}}">
																		</div>
																		@endif
																		@php
																			$i++;
																		@endphp
																	@endforeach
																</div>
																<a class="carousel-control-prev" href="#carouselPhotos" role="button" data-slide="prev">
																	<span class="carousel-control-custom-icon" aria-hidden="true">
																		<i class="fas fa-chevron-left"></i>
																	</span>
																	<span class="sr-only">Previous</span>
																</a>
																<a class="carousel-control-next" href="#carouselPhotos" role="button" data-slide="next">
																	<span class="carousel-control-custom-icon" aria-hidden="true">
																		<i class="fas fa-chevron-right"></i>
																	</span>
																	<span class="sr-only">Next</span>
																</a>
															</div>
														</div>
													</div>
												</div>
											</div>
					
											<div class="float-right">
												<a class="btn btn-secondary" href="{{ route('items.index') }}" role="button">{{ __("Back") }}</a>
												<a class="btn btn-primary" href="{{ route('items.edit', $item->id) }}" role="button">{{ __("Edit") }}</a>
											</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
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

	$('.sales_price').mask("#.##0,00", {reverse: true});
</script>
@endsection