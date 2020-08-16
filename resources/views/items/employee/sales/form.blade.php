@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">{{ __("Entry Sales") }}</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
								<li class="breadcrumb-item"><a href="{{ route("sales.employee.entry") }}">{{ __("Sales") }}</a></li>
								<li class="breadcrumb-item active">{{ __("Entry Sales") }}</li>
							</ol>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

			<div class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Sales Information") }}</div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('sales.employee.form.save') }}">
                                        @csrf
                                        <div class="form-group row" hidden>
                                            <label class="col-3 col-form-label">Item ID</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control dateselect" id="item_id" name="item_id" value="{{ $item->id }}" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Item No</label>
                                            <div class="col-9">
                                                {{ $item->item_no }}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Item Name</label>
                                            <div class="col-9">
                                                {{ $item->item_name }}
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="sales_price" class="col-3 col-form-label">Sales Price</label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                            <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="text" class="form-control" name="sales_price" value="{{ old('sales_price') }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sales_at" class="col-3 col-form-label">Sales At</label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control dateselect" id="sales_at" name="sales_at" placeholder="select date" value="{{ date('m/d/Y') }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sales_status_id" class="col-3 col-form-label">Sales Status</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control" id="sales_status_id" name="sales_status_id" value="{{ $salesstatus->id }}" hidden />
                                                <input type="text" class="form-control" id="sales_status_text" name="sales_status_text" value="{{ $salesstatus->code }}" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sales_by" class="col-3 col-form-label">Sales By</label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control dateselect" id="sales_by_id" name="sales_by_id" value="{{ Auth::user()->id }}" hidden />
                                                    <input type="text" class="form-control dateselect" id="sales_by_text" name="sales_by_text" value="{{ Auth::user()->name }}" disabled />
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
</script>
@endsection