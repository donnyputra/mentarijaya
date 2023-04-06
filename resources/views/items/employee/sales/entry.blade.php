@extends('layouts.admin')

@section('content')
<?php 
    $totalWeight = 0;
    $totalSales = 0;
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Sales") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Sales") }}</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-right">
                                {{-- <a class="btn btn-primary mb-3" href="{{ route('items.create') }}" role="button"><span><i class="nav-icon fas fa-plus"></i></span></a>
                                <a class="btn btn-secondary mb-3" href="{{ route('items.bulkupload') }}" role="button">{{ __("Bulk Upload") }}</a> --}}
                            </div> 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title">{{ __("Find Item") }}</div>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('items.employee.find') }}">
                                                <div class="form-group row">
                                                    @csrf
                                                    <label for="item_no" class="col-3 col-form-label">Item No <span style="color: red">*</span></label>
                                                    <div class="col-6">
                                                        <input type="text" id="item_no" class="form-control" name="item_no" placeholder="Search Item No here.." value="{{ old('item_no') }}" required />
                                                    </div>
                                                    <div class="col-3">
                                                        <button type="submit" class="btn btn-primary">{{ __("Find") }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Submitted Sales") }}</div>
                                </div>
                                <div class="card-body">
                                    <table
                                        class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Item No</th>
                                                <th>Item Weight</th>
                                                <th>Sales Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($today_list as $row)
                                            <tr>
                                                <td>{{ $row->sales_at }}</td>
                                                <td>{{ $row->item_no }}</td>
                                                <td>{{ StringHelper::formatDecimalDisplay($row->item_weight) }} gr</td>
                                                <td>Rp {{ StringHelper::formatDecimalDisplay($row->sales_price) }}</td>
                                            </tr>

                                            <?php 
                                                $totalWeight += $row->item_weight;
                                                $totalSales += $row->sales_price;
                                            ?>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Total Count Sold Item") }}</div>
                                </div>
                                <div class="card-body">
                                    <table
                                        class="table table-hover table-responsive table-striped text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Category Code</th>
                                                <th>Category Name</th>
                                                <th>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($total_count_sold_items as $row)
                                            <tr>
                                                <td>{{ $row->category_code }}</td>
                                                <td>{{ $row->category_name }}</td>
                                                <td>{{ $row->item_count }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Total Weight") }}</div>
                                </div>
                                <div class="card-body">
                                    <h1>{{ StringHelper::formatDecimalDisplay($totalWeight) }} gr</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Total Sales") }}</div>
                                </div>
                                <div class="card-body">
                                    <h1>Rp {{ StringHelper::formatDecimalDisplay($totalSales) }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div><!-- /.container-fluid -->
            </div>

        </div>
    </div>
</div>
@endsection

{{-- @section('custom-script')
<script type="text/javascript">
    $('#card-detail').hide();

    function updateDetailForm() {
        // console.log($('#item_no').val());
        if($('#item_no').val() == '') {
            alert('Item No must be filled.');
            $('#card-detail').hide();
        } else {
            $('#card-detail').show();
        }
    }
</script>
@endsection --}}