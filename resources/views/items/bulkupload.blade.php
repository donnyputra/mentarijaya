@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Item Bulk Upload") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item"><a href="/items">{{ __("Item") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Bulk Upload") }}</li>
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
                                        <form method="POST" action="{{ route('items.importcsv') }}" enctype="multipart/form-data">
                                            <div class="form-group row">
                                                @csrf
                                                <label for="btn_download_csv_template" class="col-3 col-form-label">Download CSV Template</label>
                                                <div class="col-9">
                                                    <a id="btn_download_csv_template" class="btn btn-info" href="{{ route('items.downloadcsvtemplate') }}"><span><i class="nav-icon fas fa-cloud-download-alt"></i></span>    {{ __('link') }}</a>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="uploaded_csv" class="col-3 col-form-label">Upload CSV</label>
                                                <div class="col-9">
                                                    <input type="file" accept=".csv" class="custom-file-input" id="uploaded_csv" name="uploaded_csv">
                                                    <label class="custom-file-label" for="uploaded_csv"><i>{{ __('please choose .csv file') }}</i></label>
                                                </div>
                                            </div>
			
											<div class="float-right">
												<a class="btn btn-secondary" href="{{ url()->previous() }}" role="button">{{ __("Back") }}</a>
												<button type="submit" class="btn btn-primary"><span><i class="nav-icon fas fa-cloud-upload-alt"></i></span>    {{ __("Import") }}</button>
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
    // $(document).ready(function () {
    //     $('#uploaded_csv').on('change', function() {
    //         //get the file name
    //         var fileName = $(this)[0].files[0].name;
    //         console.log(fileName);
    //         //replace the "Choose a file" label
    //         $(this).next('.custom-file-label').html(fileName);
    //     });
    // });
</script>
@endsection
